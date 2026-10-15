#!/usr/bin/env node
/**
 * Measure page speed on the live site (PERFORMANCE-PLAN.md #0).
 *
 * Usage:
 *   pnpm perf [mobile|desktop|both] [url-or-path ...] [options]
 *
 * Options:
 *   --runs <n>       Loads per page. Metrics are the median; the details come from the first load (default: 1)
 *   --label <name>   Added to the output file name, e.g. before-4 / after-4 (default: run)
 *   --base <url>     Base URL for paths (default: https://www.fdry.com)
 *   --settle <ms>    Wait after the load event before reading the metrics (default: 5000)
 *   --dpr <n>        Device pixel ratio instead of the device's own (mobile 1.75, desktop 1),
 *                    e.g. to see which srcset candidate a 3× phone picks (PERFORMANCE-PLAN.md #7)
 *   --profile        Add main-thread JS time by script
 *   --legacy-css     Add the legacy CSS rules each page uses (PERFORMANCE-PLAN.md Appendix A)
 *   --screenshots    Save a first-screen and a full-page PNG per page (the full page at DPR 1,
 *                    after the scroll, with fixed elements only at the top)
 *   --no-warm        Don't request each page once before loading it (see below)
 *   --reduced-motion Emulate prefers-reduced-motion, so fades, the hero video,
 *                    the tech grid and CSS animations hold still for screenshots
 *
 * Simulating a change on the live pages before deploying it (PERFORMANCE-PLAN.md #4):
 *   --block <pattern>     Block requests matching a URL pattern (* is a wildcard).
 *                         Repeat it for several, e.g. --block '*foundry/css/theme.min.css*'
 *   --inject-css <file>   Add a local CSS file to the page, e.g. a fresh dist/fdry.*.css
 *                         (block the live one too)
 *   --add-class <names>   Add classes to <body>, e.g. one a PHP change would print
 * Compare two runs' screenshots with `pnpm perf:diff` (scripts/perf-diff.mjs).
 *
 * Without URLs it measures the plan's verification set (DEFAULT_PAGES). It
 * prints a summary and writes everything to build/perf/<date>-<label>-<device>.json:
 * FCP, LCP and its element, CLS, approximate TBT, TTFB, the render-blocking
 * resources and synchronous scripts, the 15 largest requests, page weight, and
 * CSS coverage per stylesheet.
 *
 * Each load is a first visit in a fresh headless Chrome profile with the cache
 * off, so the cookie banner shows, as in the 12 Oct 2026 baseline:
 * - mobile: 412×823 at DPR 1.75, slow 4G (150 ms, 1.6 Mbps) and 4× CPU;
 * - desktop: 1440×900 at DPR 1, 10 Mbps and 40 ms.
 *
 * Before each load the page is requested once from Node, with the same user
 * agent, so WP Rocket's page cache is warm: it keeps a separate cache for
 * phones, which nothing fills after a purge until a phone visits. TTFB shows
 * whether that worked.
 *
 * TBT is approximate: the long tasks after FCP, up to the reading. The JS
 * profile covers the same window. CSS coverage starts after the reading,
 * because tracking it during the load made TBT about 5× higher; it covers the
 * page as loaded, then a scroll to the bottom (unthrottled).
 *
 * Needs Google Chrome (or CHROME_PATH) and Node 22. No npm packages: it talks
 * to Chrome over the DevTools protocol with Node's built-in WebSocket.
 */

import { spawn } from 'node:child_process'
import { existsSync, mkdtempSync, readFileSync, rmSync } from 'node:fs'
import { mkdir, writeFile } from 'node:fs/promises'
import os from 'node:os'
import path from 'node:path'
import { decodePng, encodePng } from './lib/png.mjs'

// A bare "--" (as in `pnpm perf -- mobile`) is an end-of-options marker,
// not an option, so drop it before parsing.
const argv = process.argv.slice(2).filter((a) => a !== '--')

// Only these options take a value; every other --flag is a boolean.
const VALUE_OPTIONS = ['runs', 'label', 'base', 'settle', 'dpr', 'block', 'inject-css', 'add-class']

const flag = (name) => argv.includes(`--${name}`)
const opt = (name, fallback) => {
	const i = argv.indexOf(`--${name}`)
	return i !== -1 && argv[i + 1] ? argv[i + 1] : fallback
}
// Every value of a repeatable option.
const opts = (name) => argv.flatMap((a, i) => (a === `--${name}` && argv[i + 1] ? [argv[i + 1]] : []))
const positional = argv.filter((a, i) => {
	if (a.startsWith('--')) return false
	const prev = argv[i - 1]
	return !(prev && VALUE_OPTIONS.includes(prev.replace(/^--/, '')))
})

// Matches the 12 Oct 2026 baseline in PERFORMANCE-PLAN.md. Mobile is
// Lighthouse's slow 4G and Moto G Power screen; throughput is in bytes/s.
const DEVICES = {
	mobile: {
		width: 412,
		height: 823,
		scale: 1.75,
		mobile: true,
		latency: 150,
		download: (1.6 * 1024 * 1024) / 8,
		upload: (750 * 1024) / 8,
		cpu: 4,
	},
	desktop: {
		width: 1440,
		height: 900,
		scale: 1,
		mobile: false,
		latency: 40,
		download: (10 * 1024 * 1024) / 8,
		upload: (10 * 1024 * 1024) / 8,
		cpu: 1,
	},
}

// PERFORMANCE-PLAN.md → Verification: the pages to measure for every item.
const DEFAULT_PAGES = [
	'/',
	'/about/',
	'/services/',
	'/services/shopify-agency/',
	'/works/',
	'/insights/',
	'/insights/2025-is-the-year-of-ai-are-you-ready/',
	'/contact/',
]

// The legacy Understrap stylesheets (#4), also matched in WP Rocket's minified
// copies under /wp-content/cache/min/.
const LEGACY_CSS = /\/themes\/foundry\/(?:css\/theme(?:\.min)?\.css|mainstyle\/[^/?]+\.css)/

const CHROME_PATHS = [
	process.env.CHROME_PATH,
	'/Applications/Google Chrome.app/Contents/MacOS/Google Chrome',
	'/usr/bin/google-chrome',
	'/usr/bin/chromium',
]

const OUT_DIR = 'build/perf'
const LOAD_TIMEOUT = 60_000
const LARGEST = 15
// Full-page screenshots stop here, to keep the files a sane size.
const MAX_SHOT_HEIGHT = 20_000

// Runs in the page before any of its own scripts.
const OBSERVER = `(() => {
	performance.setResourceTimingBufferSize(1000)
	const perf = (window.__fdryPerf = { lcp: [], cls: 0, longTasks: [] })
	const hasClass = (node) => typeof node.className === 'string' && node.className.trim() !== ''
	const name = (node) => node.tagName.toLowerCase() + (node.id ? '#' + node.id : '') + (hasClass(node) ? '.' + node.className.trim().split(/\\s+/)[0] : '')
	const describe = (el) => {
		if (!el) return null
		let label = name(el)
		if (!el.id && !hasClass(el)) {
			const parent = el.parentElement && el.parentElement.closest('[id],[class]')
			if (parent) label += ' in ' + name(parent)
		}
		if (el.closest('[data-fade-first]')) label += ' [fade-first]'
		else if (el.closest('[data-fade-up],[data-fade-down],[data-fade-up-group]')) label += ' [gsap fade]'
		return label
	}
	const observe = (type, fn) => {
		try {
			new PerformanceObserver((list) => list.getEntries().forEach(fn)).observe({ type, buffered: true })
		} catch (e) {}
	}
	observe('largest-contentful-paint', (e) => perf.lcp.push({ time: e.startTime, size: e.size, url: e.url, element: describe(e.element) }))
	observe('longtask', (e) => perf.longTasks.push({ start: e.startTime, duration: e.duration }))
	// CLS is the worst session window: shifts less than 1 s apart, 5 s at most.
	let session = 0
	let sessionStart = 0
	let last = 0
	observe('layout-shift', (e) => {
		if (e.hadRecentInput) return
		if (session && (e.startTime - last > 1000 || e.startTime - sessionStart > 5000)) session = 0
		if (!session) sessionStart = e.startTime
		session += e.value
		last = e.startTime
		perf.cls = Math.max(perf.cls, session)
	})
})()`

const METRICS = `(() => {
	const perf = window.__fdryPerf
	const nav = performance.getEntriesByType('navigation')[0]
	const paint = performance.getEntriesByName('first-contentful-paint')[0]
	const fcp = paint ? paint.startTime : null
	const lcp = perf.lcp[perf.lcp.length - 1] || null
	const tbt = fcp === null ? null : perf.longTasks.filter((t) => t.start >= fcp).reduce((sum, t) => sum + Math.max(0, t.duration - 50), 0)
	const runs = (s) => !s.async && !s.defer && (!s.type || /javascript/i.test(s.type))
	return {
		metrics: {
			ttfb: nav ? nav.responseStart : null,
			fcp,
			lcp: lcp ? lcp.time : null,
			cls: perf.cls,
			tbt,
			domContentLoaded: nav ? nav.domContentLoadedEventStart : null,
			load: nav ? nav.loadEventStart : null,
		},
		lcpElement: lcp ? lcp.element : null,
		lcpUrl: lcp ? lcp.url : null,
		renderBlocking: performance.getEntriesByType('resource').filter((e) => e.renderBlockingStatus === 'blocking').map((e) => e.name),
		syncScripts: [...document.querySelectorAll('script[src]')].filter(runs).map((s) => s.src),
		domNodes: document.getElementsByTagName('*').length,
	}
})()`

// Scrolls in steps so lazy content loads and fades play, then back to the top.
const SCROLL = `(async () => {
	const wait = (ms) => new Promise((r) => setTimeout(r, ms))
	const step = Math.round(innerHeight * 0.8)
	for (let y = 0; y < document.documentElement.scrollHeight; y += step) {
		scrollTo(0, y)
		await wait(120)
	}
	scrollTo(0, document.documentElement.scrollHeight)
	await wait(2500)
	scrollTo(0, 0)
	await wait(1000)
})()`

// Fixed elements (header, cookie banner) would repeat in every screenshot tile.
const HIDE_FIXED = `document.querySelectorAll('body *').forEach((el) => {
	if (getComputedStyle(el).position === 'fixed') el.style.setProperty('visibility', 'hidden', 'important')
})`

const sleep = (ms) => new Promise((resolve) => setTimeout(resolve, ms))

if (flag('help') || flag('h')) {
	console.log('usage: pnpm perf [mobile|desktop|both] [url-or-path ...] [--runs n] [--label name] [--base url] [--settle ms] [--profile] [--legacy-css] [--screenshots] [--no-warm] [--reduced-motion] [--block pattern]... [--inject-css file] [--add-class names]')
	process.exit(0)
}

const deviceArg = positional[0] in DEVICES || positional[0] === 'both' ? positional[0] : 'mobile'
const devices = deviceArg === 'both' ? Object.keys(DEVICES) : [deviceArg]
const paths = positional[0] === deviceArg ? positional.slice(1) : positional
const base = opt('base', 'https://www.fdry.com')
const runs = Math.max(1, parseInt(opt('runs', '1'), 10) || 1)
const settle = Math.max(0, parseInt(opt('settle', '5000'), 10) || 0)
const label = opt('label', 'run').replace(/[^\w.-]+/g, '-')
const withProfile = flag('profile')
const withLegacyCss = flag('legacy-css')
const withScreenshots = flag('screenshots')
const warm = !flag('no-warm')
const reducedMotion = flag('reduced-motion')
const blocked = opts('block')
const injectCssFile = opt('inject-css', '')
const addClass = opt('add-class', '').trim()

const dpr = parseFloat(opt('dpr', '')) || 0
if (dpr > 0) for (const device of Object.values(DEVICES)) device.scale = dpr

let injectCss = ''

if (injectCssFile) {
	try {
		injectCss = readFileSync(injectCssFile, 'utf8')
	} catch {
		console.error(`Can't read --inject-css file: ${injectCssFile}`)
		process.exit(1)
	}
}

// Runs before the page's own scripts. The <style> goes at the end of <head>
// and the classes on <body> as soon as each exists, so they apply from the
// first paint. A simulation's metrics are still approximate; its screenshots
// are what it's for.
const INJECT = `(() => {
	const css = ${JSON.stringify(injectCss)}
	const classes = ${JSON.stringify(addClass ? addClass.split(/\s+/) : [])}
	const apply = () => {
		let done = true
		if (css) {
			if (document.head) {
				if (!document.querySelector('style[data-fdry-inject]')) {
					const style = document.createElement('style')
					style.dataset.fdryInject = ''
					style.textContent = css
					document.head.appendChild(style)
				}
			} else done = false
		}
		if (classes.length) {
			if (document.body) document.body.classList.add(...classes)
			else done = false
		}
		return done
	}
	if (apply()) return
	const observer = new MutationObserver(() => apply() && observer.disconnect())
	observer.observe(document, { childList: true, subtree: true })
	document.addEventListener('DOMContentLoaded', () => { apply(); observer.disconnect() })
})()`

let urls

try {
	urls = (paths.length ? paths : DEFAULT_PAGES).map((p) => new URL(p, base).href)
} catch {
	console.error(`Not a URL or path: ${paths.join(' ')} (base ${base})`)
	process.exit(1)
}

const chromePath = CHROME_PATHS.find((p) => p && existsSync(p))

if (!chromePath) {
	console.error('Google Chrome not found. Set CHROME_PATH to its executable.')
	process.exit(1)
}

// Every Chrome still running, so Ctrl-C doesn't leave any behind.
const running = new Set()

process.on('SIGINT', () => {
	running.forEach((chrome) => chrome.kill('SIGKILL'))
	process.exit(130)
})

async function connect(url) {
	const ws = new WebSocket(url)

	await new Promise((resolve, reject) => {
		ws.addEventListener('open', resolve, { once: true })
		ws.addEventListener('error', () => reject(new Error('DevTools connection failed')), { once: true })
	})

	let nextId = 0
	const pending = new Map()
	const listeners = new Map()

	ws.addEventListener('message', ({ data }) => {
		const message = JSON.parse(data)

		if (message.id) {
			const call = pending.get(message.id)
			pending.delete(message.id)

			if (!call) return
			if (message.error) call.reject(new Error(`${call.method}: ${message.error.message}`))
			else call.resolve(message.result)
		} else {
			listeners.get(message.method)?.forEach((fn) => fn(message.params))
		}
	})

	return {
		send: (method, params = {}) =>
			new Promise((resolve, reject) => {
				const id = ++nextId
				pending.set(id, { method, resolve, reject })
				ws.send(JSON.stringify({ id, method, params }))
			}),
		on: (method, fn) => listeners.set(method, [...(listeners.get(method) || []), fn]),
		close: () => ws.close(),
	}
}

// A fresh profile per load: cold cache, no cookies, so the cookie banner shows.
async function launch() {
	const dir = mkdtempSync(path.join(os.tmpdir(), 'fdry-perf-'))
	const chrome = spawn(chromePath, ['--headless=new', '--no-first-run', '--no-default-browser-check', `--user-data-dir=${dir}`, '--remote-debugging-port=0', 'about:blank'], { stdio: 'ignore' })
	running.add(chrome)

	const close = async () => {
		chrome.kill('SIGKILL')
		running.delete(chrome)
		// Chrome keeps writing to the profile for a moment after the kill.
		await sleep(800)
		rmSync(dir, { recursive: true, force: true, maxRetries: 5, retryDelay: 300 })
	}

	try {
		let port

		for (let i = 0; i < 100 && !port; i++) {
			await sleep(100)
			if (existsSync(`${dir}/DevToolsActivePort`)) port = readFileSync(`${dir}/DevToolsActivePort`, 'utf8').split('\n')[0]
		}

		if (!port) throw new Error('Chrome did not start')

		const targets = await (await fetch(`http://127.0.0.1:${port}/json/list`)).json()
		const cdp = await connect(targets.find((t) => t.type === 'page').webSocketDebuggerUrl)

		return {
			cdp,
			close: async () => {
				cdp.close()
				await close()
			},
		}
	} catch (error) {
		await close()
		throw error
	}
}

async function evaluate(cdp, expression) {
	const { result, exceptionDetails } = await cdp.send('Runtime.evaluate', { expression, returnByValue: true, awaitPromise: true })

	if (exceptionDetails) throw new Error(exceptionDetails.exception?.description || exceptionDetails.text)

	return result.value
}

async function throttle(cdp, device, on) {
	await cdp.send(
		'Network.emulateNetworkConditions',
		on
			? { offline: false, latency: device.latency, downloadThroughput: device.download, uploadThroughput: device.upload }
			: { offline: false, latency: 0, downloadThroughput: -1, uploadThroughput: -1 }
	)
	await cdp.send('Emulation.setCPUThrottlingRate', { rate: on ? device.cpu : 1 })
}

// On mobile, a phone user agent. Both still say HeadlessChrome, so analytics
// can filter these visits out.
async function userAgent(cdp, device) {
	const { product, userAgent: desktop } = await cdp.send('Browser.getVersion')
	const chrome = product.replace(/^(\w+\/\d+)\..*$/, '$1.0.0.0')

	return device.mobile ? `Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) ${chrome} Mobile Safari/537.36` : desktop
}

// Chrome 13x replaced Network.setBlockedURLs' `urls` with `urlPatterns`;
// try the new form first.
async function blockUrls(cdp, patterns) {
	try {
		await cdp.send('Network.setBlockedURLs', { urlPatterns: patterns.map((urlPattern) => ({ urlPattern, block: true })) })
	} catch {
		await cdp.send('Network.setBlockedURLs', { urls: patterns })
	}
}

async function screenshot(cdp, file) {
	const { data } = await cdp.send('Page.captureScreenshot', { format: 'png' })
	await writeFile(file, Buffer.from(data, 'base64'))
}

// Chrome's own full-page capture (captureBeyondViewport) never returns on the
// homepage once it has been scrolled, so this scrolls one viewport at a time
// and stitches the tiles.
async function fullPageScreenshot(cdp, device, file) {
	await cdp.send('Emulation.setDeviceMetricsOverride', { width: device.width, height: device.height, deviceScaleFactor: 1, mobile: device.mobile })
	await cdp.send('Emulation.setScrollbarsHidden', { hidden: true })

	const { total, step } = await evaluate(cdp, `({ total: document.documentElement.scrollHeight, step: innerHeight })`)
	const height = Math.min(total, MAX_SHOT_HEIGHT)
	const rows = []
	let first

	for (let y = 0; y < height; y += step) {
		const scrolled = await evaluate(cdp, `(async () => { scrollTo(0, ${y}); await new Promise((r) => setTimeout(r, 250)); return scrollY })()`)
		const { data } = await cdp.send('Page.captureScreenshot', { format: 'png' })
		const tile = decodePng(Buffer.from(data, 'base64'))

		first ??= tile
		if (tile.width !== first.width || tile.color !== first.color) throw new Error('Screenshot tiles differ in size')

		// At the bottom the page can't scroll a full step, so the tile overlaps the last one.
		rows.push(...tile.rows.slice(y - scrolled, Math.min(tile.height, height - scrolled)))

		if (y === 0) await evaluate(cdp, HIDE_FIXED)
	}

	await writeFile(file, encodePng(first.width, first.color, rows))
}

function slug(url) {
	return new URL(url).pathname.replace(/^\/|\/$/g, '').replace(/[^\w-]+/g, '-') || 'home'
}

function isFirstParty(url, host) {
	const domain = host.replace(/^www\./, '')
	const { hostname } = new URL(url)

	return hostname === domain || hostname.endsWith(`.${domain}`)
}

function summariseRequests(requests, start, host) {
	const list = [...requests.values()]
		.filter((r) => /^https?:/.test(r.url))
		.map((r) => ({
			url: r.url,
			type: r.type,
			priority: r.priority,
			status: r.status ?? null,
			bytes: r.bytes ?? 0,
			start: r.start ? Math.round((r.start - start) * 1000) : null,
			end: r.end ? Math.round((r.end - start) * 1000) : null,
			...(r.failed ? { failed: r.failed } : {}),
		}))

	const weight = { requests: list.length, bytes: 0, firstPartyBytes: 0, thirdPartyBytes: 0, byType: {} }

	for (const r of list) {
		weight.bytes += r.bytes
		weight[isFirstParty(r.url, host) ? 'firstPartyBytes' : 'thirdPartyBytes'] += r.bytes
		weight.byType[r.type] = (weight.byType[r.type] || 0) + r.bytes
	}

	return { list, weight }
}

// Used bytes per stylesheet. Overlapping used ranges (nested rules) count once.
function summariseCoverage(sheets, usage) {
	const used = new Map()

	for (const rule of usage) {
		if (!rule.used) continue
		if (!used.has(rule.styleSheetId)) used.set(rule.styleSheetId, [])
		used.get(rule.styleSheetId).push([rule.startOffset, rule.endOffset])
	}

	return [...sheets.values()]
		.map((sheet) => {
			let usedBytes = 0
			let reach = -1

			for (const [start, end] of (used.get(sheet.styleSheetId) || []).sort((a, b) => a[0] - b[0])) {
				usedBytes += Math.max(0, end - Math.max(start, reach))
				reach = Math.max(reach, end)
			}

			const bytes = sheet.length ?? 0

			return {
				url: sheet.sourceURL || '(inline)',
				inline: Boolean(sheet.isInline),
				bytes,
				usedBytes,
				usedPct: bytes ? Math.round((usedBytes / bytes) * 1000) / 10 : 0,
			}
		})
		.sort((a, b) => b.bytes - a.bytes)
}

// Self time per script, in ms. The script key keeps a tag's ?id= (gtag/js
// loads once per destination) and drops other query strings.
function summariseProfile(profile) {
	const nodes = new Map(profile.nodes.map((node) => [node.id, node.callFrame]))
	const totals = new Map()

	profile.samples.forEach((id, i) => {
		const frame = nodes.get(id)
		let key = frame.functionName.startsWith('(') ? frame.functionName : '(native)'

		if (frame.url) {
			try {
				const url = new URL(frame.url)
				const tag = url.searchParams.get('id')
				key = url.origin + url.pathname + (tag ? `?id=${tag}` : '')
			} catch {
				key = frame.url
			}
		}

		// Each sample lasts until the next one.
		totals.set(key, (totals.get(key) || 0) + (profile.timeDeltas[i + 1] ?? 0))
	})

	totals.delete('(idle)')
	totals.delete('(root)')

	return [...totals]
		.map(([script, us]) => ({ script, ms: Math.round(us / 1000) }))
		.filter((row) => row.ms > 0)
		.sort((a, b) => b.ms - a.ms)
}

// Every @media / @supports block as [start, end, prelude], so each used legacy
// rule can be listed with its condition.
function conditionBlocks(text) {
	const blocks = []
	const stack = []
	let quote = null

	for (let i = 0; i < text.length; i++) {
		const c = text[i]

		if (quote) {
			if (c === '\\') i++
			else if (c === quote) quote = null
			continue
		}

		if (c === '"' || c === "'") {
			quote = c
		} else if (c === '/' && text[i + 1] === '*') {
			const end = text.indexOf('*/', i + 2)
			i = end === -1 ? text.length : end + 1
		} else if (c === '{') {
			let j = i - 1
			while (j >= 0 && !';{}'.includes(text[j])) j--
			stack.push({ start: i, prelude: text.slice(j + 1, i).trim() })
		} else if (c === '}') {
			const open = stack.pop()
			if (open?.prelude.startsWith('@')) blocks.push({ start: open.start, end: i, prelude: open.prelude.replace(/\s+/g, ' ') })
		}
	}

	// Outer blocks first, so nested conditions read outer › inner.
	return blocks.sort((a, b) => a.start - b.start)
}

async function legacyRules(cdp, sheets, usage) {
	const result = []

	for (const sheet of sheets.values()) {
		if (!LEGACY_CSS.test(sheet.sourceURL || '')) continue

		const { text } = await cdp.send('CSS.getStyleSheetText', { styleSheetId: sheet.styleSheetId })
		const blocks = conditionBlocks(text)
		const seen = new Set()
		const rules = usage
			.filter((r) => r.used && r.styleSheetId === sheet.styleSheetId)
			.sort((a, b) => a.startOffset - b.startOffset)
			.filter((r) => !seen.has(r.startOffset) && seen.add(r.startOffset))
			.map((r) => ({
				media: blocks
					.filter((b) => b.start < r.startOffset && r.startOffset < b.end)
					.map((b) => b.prelude)
					.join(' › '),
				rule: text.slice(r.startOffset, r.endOffset).replace(/\s+/g, ' ').trim(),
			}))

		result.push({ sheet: path.basename(new URL(sheet.sourceURL).pathname), rules })
	}

	return result
}

async function measure(url, deviceName, details) {
	const device = DEVICES[deviceName]
	const { cdp, close } = await launch()

	try {
		const requests = new Map()
		const sheets = new Map()
		const usage = []
		let mainRequest = null
		let start = 0
		let loaded = false

		cdp.on('Network.requestWillBeSent', ({ requestId, request, type, timestamp }) => {
			if (!mainRequest && type === 'Document') {
				mainRequest = requestId
				start = timestamp
			}

			// A redirect reuses the requestId: keep the first start.
			requests.set(requestId, { start: timestamp, ...requests.get(requestId), url: request.url, type, priority: request.initialPriority })
		})
		cdp.on('Network.responseReceived', ({ requestId, response }) => {
			if (requests.has(requestId)) requests.get(requestId).status = response.status
		})
		cdp.on('Network.loadingFinished', ({ requestId, encodedDataLength, timestamp }) => {
			if (requests.has(requestId)) Object.assign(requests.get(requestId), { bytes: encodedDataLength, end: timestamp })
		})
		cdp.on('Network.loadingFailed', ({ requestId, errorText, timestamp }) => {
			if (requests.has(requestId)) Object.assign(requests.get(requestId), { failed: errorText, end: timestamp })
		})
		cdp.on('CSS.styleSheetAdded', ({ header }) => sheets.set(header.styleSheetId, header))
		// Uncaught exceptions and console.error calls, e.g. when --block removes a script something still needs.
		const jsErrors = []
		cdp.on('Runtime.exceptionThrown', ({ exceptionDetails: e }) => jsErrors.push((e.exception?.description || e.text || '').split('\n')[0].slice(0, 300)))
		cdp.on('Runtime.consoleAPICalled', ({ type, args }) => type === 'error' && jsErrors.push(`console.error: ${args.map((a) => a.value ?? a.description ?? '').join(' ')}`.slice(0, 300)))
		cdp.on('Page.loadEventFired', () => {
			loaded = true
		})

		await cdp.send('Page.enable')
		await cdp.send('Network.enable')
		await cdp.send('Runtime.enable')

		await cdp.send('Network.setCacheDisabled', { cacheDisabled: true })
		await cdp.send('Emulation.setDeviceMetricsOverride', { width: device.width, height: device.height, deviceScaleFactor: device.scale, mobile: device.mobile })

		const agent = await userAgent(cdp, device)

		if (device.mobile) {
			await cdp.send('Emulation.setTouchEmulationEnabled', { enabled: true })
			await cdp.send('Emulation.setUserAgentOverride', { userAgent: agent })
		}

		if (warm) await (await fetch(url, { headers: { 'user-agent': agent } })).arrayBuffer()

		if (blocked.length) await blockUrls(cdp, blocked)
		if (reducedMotion) await cdp.send('Emulation.setEmulatedMedia', { features: [{ name: 'prefers-reduced-motion', value: 'reduce' }] })

		await throttle(cdp, device, true)
		await cdp.send('Page.addScriptToEvaluateOnNewDocument', { source: OBSERVER })
		if (injectCss || addClass) await cdp.send('Page.addScriptToEvaluateOnNewDocument', { source: INJECT })

		if (withProfile) {
			await cdp.send('Profiler.enable')
			await cdp.send('Profiler.setSamplingInterval', { interval: 1000 })
			await cdp.send('Profiler.start')
		}

		await cdp.send('Page.navigate', { url })

		const deadline = Date.now() + LOAD_TIMEOUT
		while (!loaded && Date.now() < deadline) await sleep(100)
		await sleep(settle)

		const page = await evaluate(cdp, METRICS)
		const status = requests.get(mainRequest)?.status ?? null
		const result = { status, loaded, metrics: page.metrics, lcpElement: page.lcpElement, lcpUrl: page.lcpUrl }

		if (!details) return result

		const profile = withProfile ? summariseProfile((await cdp.send('Profiler.stop')).profile) : undefined
		const host = new URL(url).hostname
		const { list, weight } = summariseRequests(requests, start, host)
		const byUrl = new Map(list.map((r) => [r.url, r]))
		const shotBase = path.join(OUT_DIR, `${stamp}-${label}-${deviceName}-${slug(url)}`)

		if (withScreenshots) await screenshot(cdp, `${shotBase}-first.png`)

		// Coverage starts only now: tracking rule usage during the load made TBT
		// about 5× higher (0.25 → 1.2 s on a service page) and FCP 0.1 s later.
		// Starting it re-matches every rule against the page as it is, and the
		// scroll adds what's below the fold.
		await cdp.send('DOM.enable')
		await cdp.send('CSS.enable')
		await cdp.send('CSS.startRuleUsageTracking')
		await throttle(cdp, device, false)
		await evaluate(cdp, SCROLL)
		usage.push(...(await cdp.send('CSS.takeCoverageDelta')).coverage)

		// Last: it hides the fixed elements.
		if (withScreenshots) await fullPageScreenshot(cdp, device, `${shotBase}-full.png`)

		return {
			...result,
			renderBlocking: page.renderBlocking.map((u) => ({ url: u, bytes: byUrl.get(u)?.bytes ?? null, end: byUrl.get(u)?.end ?? null })),
			syncScripts: page.syncScripts,
			domNodes: page.domNodes,
			weight,
			largest: [...list].sort((a, b) => b.bytes - a.bytes).slice(0, LARGEST),
			coverage: summariseCoverage(sheets, usage),
			...(withLegacyCss ? { legacyCss: await legacyRules(cdp, sheets, usage) } : {}),
			...(profile ? { profile } : {}),
			...(withScreenshots ? { screenshots: { first: `${shotBase}-first.png`, full: `${shotBase}-full.png` } } : {}),
			blockedRequests: list.filter((r) => r.failed === 'net::ERR_BLOCKED_BY_CLIENT').map((r) => r.url),
			jsErrors,
			requests: list,
		}
	} finally {
		await close()
	}
}

function median(values) {
	const sorted = values.filter((v) => typeof v === 'number').sort((a, b) => a - b)
	if (!sorted.length) return null
	const mid = sorted.length >> 1

	return sorted.length % 2 ? sorted[mid] : (sorted[mid - 1] + sorted[mid]) / 2
}

function roundMetrics(metrics) {
	return Object.fromEntries(Object.entries(metrics).map(([key, value]) => [key, value === null ? null : key === 'cls' ? Math.round(value * 1000) / 1000 : Math.round(value)]))
}

function pageResult(url, results) {
	const ok = results.filter((r) => !r.error)
	const errors = results.filter((r) => r.error).map((r) => r.error)

	if (!ok.length) return { url, errors }

	const metrics = roundMetrics(Object.fromEntries(Object.keys(ok[0].metrics).map((key) => [key, median(ok.map((r) => r.metrics[key]))])))
	// The LCP element of the load whose LCP is closest to the median.
	const typical = ok.reduce((best, r) => (Math.abs(r.metrics.lcp - metrics.lcp) < Math.abs(best.metrics.lcp - metrics.lcp) ? r : best))
	const { status, loaded, metrics: _, lcpElement, lcpUrl, ...details } = ok.find((r) => r.coverage) || ok[0]

	return {
		url,
		status,
		...(loaded ? {} : { warning: `no load event within ${LOAD_TIMEOUT / 1000} s` }),
		metrics,
		lcpElement: typical.lcpElement,
		lcpUrl: typical.lcpUrl,
		runs: ok.map((r) => roundMetrics(r.metrics)),
		...(errors.length ? { errors } : {}),
		...details,
	}
}

// Rules → pages that use them, across the run (Appendix A).
function legacySummary(pages) {
	const rules = new Map()

	for (const page of pages) {
		for (const { sheet, rules: used } of page.legacyCss || []) {
			for (const { media, rule } of used) {
				const key = `${sheet}\n${media}\n${rule}`
				if (!rules.has(key)) rules.set(key, { sheet, media, rule, pages: [] })
				rules.get(key).pages.push(new URL(page.url).pathname)
			}
		}
	}

	return [...rules.values()].sort((a, b) => b.pages.length - a.pages.length || a.sheet.localeCompare(b.sheet))
}

const seconds = (ms) => (ms === null ? '–' : `${(ms / 1000).toFixed(2)} s`)
const kb = (bytes) => (bytes >= 1024 * 1024 ? `${(bytes / 1024 / 1024).toFixed(2)} MB` : `${Math.round(bytes / 1024)} KB`)
const pad = (value, width) => String(value).padEnd(width)
const shortUrl = (url) => (url.length > 70 ? `…${url.slice(-69)}` : url)

function printSummary(deviceName, pages, file) {
	const widths = [34, 9, 9, 40, 7, 8, 9, 10, 8]
	const row = (cells) => console.log('  ' + cells.map((cell, i) => pad(cell, widths[i])).join(' '))

	console.log('')
	row(['page', 'FCP', 'LCP', 'LCP element', 'CLS', 'TBT', 'weight', '3rd party', 'blocking'])

	for (const page of pages) {
		const name = new URL(page.url).pathname

		if (!page.metrics) {
			row([name, `failed: ${page.errors.join('; ')}`])
			continue
		}

		const m = page.metrics
		row([
			name.length > 34 ? `…${name.slice(-33)}` : name,
			seconds(m.fcp),
			seconds(m.lcp),
			(page.lcpElement || '–').slice(0, 40),
			m.cls ?? '–',
			m.tbt === null ? '–' : `${m.tbt} ms`,
			page.weight ? kb(page.weight.bytes) : '–',
			page.weight ? kb(page.weight.thirdPartyBytes) : '–',
			page.renderBlocking?.length ?? '–',
		])
	}

	console.log('\n  CSS used (stylesheets of 5 KB or more):')

	for (const page of pages) {
		if (!page.coverage) continue
		const name = (s) => (s.inline && s.url === page.url ? 'inline <style>' : path.basename(new URL(s.url, page.url).pathname))
		const sheets = page.coverage.filter((s) => s.bytes >= 5 * 1024).map((s) => `${name(s)} ${s.usedPct}% of ${kb(s.bytes)}`)
		console.log(`  ${pad(new URL(page.url).pathname, 34)} ${sheets.join(' · ')}`)
	}

	if (withProfile) {
		console.log('\n  Main-thread JS self time (top 6):')

		for (const page of pages) {
			if (!page.profile) continue
			console.log(`  ${new URL(page.url).pathname}`)
			page.profile.slice(0, 6).forEach(({ script, ms }) => console.log(`    ${pad(`${ms} ms`, 9)} ${shortUrl(script)}`))
		}
	}

	if (withLegacyCss) {
		console.log('\n  Legacy CSS rules used:')

		for (const page of pages) {
			if (!page.legacyCss) continue
			const counts = page.legacyCss.map(({ sheet, rules }) => `${sheet} ${rules.length}`).join(' · ')
			console.log(`  ${pad(new URL(page.url).pathname, 34)} ${counts || 'no legacy stylesheets'}`)
		}

		console.log(`  ${legacySummary(pages).length} distinct rules in all (see legacyCssSummary in the JSON)`)
	}

	const withErrors = pages.filter((page) => page.jsErrors?.length)

	if (withErrors.length) {
		console.log('\n  JS errors:')
		withErrors.forEach((page) => page.jsErrors.forEach((error) => console.log(`  ${pad(new URL(page.url).pathname, 34)} ${error.slice(0, 160)}`)))
	}

	console.log(`\n  ${deviceName}: ${file}`)
}

const now = new Date()
const stamp = [now.getFullYear(), now.getMonth() + 1, now.getDate()].map((n) => String(n).padStart(2, '0')).join('-') + '-' + [now.getHours(), now.getMinutes()].map((n) => String(n).padStart(2, '0')).join('')

await mkdir(OUT_DIR, { recursive: true })

for (const deviceName of devices) {
	console.log(`\n${deviceName}: ${urls.length} page${urls.length === 1 ? '' : 's'}, ${runs} load${runs === 1 ? '' : 's'} each`)

	const pages = []

	for (const url of urls) {
		process.stdout.write(`  ${new URL(url).pathname} `)
		const results = []

		for (let run = 0; run < runs; run++) {
			try {
				// The first load that succeeds also collects the details.
				results.push(await measure(url, deviceName, !results.some((r) => !r.error)))
				process.stdout.write('.')
			} catch (error) {
				results.push({ error: error.message })
				process.stdout.write('x')
			}
		}

		process.stdout.write('\n')
		pages.push(pageResult(url, results))
	}

	const file = path.join(OUT_DIR, `${stamp}-${label}-${deviceName}.json`)
	const report = {
		tool: 'scripts/perf-measure.mjs',
		date: now.toISOString(),
		label,
		device: deviceName,
		settings: { ...DEVICES[deviceName], runs, settle, warm, reducedMotion, block: blocked, injectCss: injectCssFile || null, addClass: addClass || null },
		pages,
		...(withLegacyCss ? { legacyCssSummary: legacySummary(pages) } : {}),
	}

	await writeFile(file, JSON.stringify(report, null, '\t'))
	printSummary(deviceName, pages, file)
}
