#!/usr/bin/env node
/**
 * Compare the full-page screenshots of two `pnpm perf --screenshots` runs
 * (PERFORMANCE-PLAN.md #4).
 *
 * Usage:
 *   pnpm perf:diff <before.json> <after.json> [--threshold n]
 *
 *   --threshold <n>  A pixel counts as changed when a colour channel differs by
 *                    more than this (0–255, default 24), which ignores text
 *                    anti-aliasing noise
 *
 * Pages are matched by URL. For each one it prints the share of changed
 * pixels, the page heights and the bands of changed rows (CSS px from the top,
 * since full-page screenshots are taken at DPR 1). It writes a diff PNG to
 * build/perf/diff/: the after screenshot faded, with changed pixels in red.
 * Rows that exist in only one screenshot (the page got taller or shorter)
 * count as changed.
 *
 * A layout shift high up moves everything below it, so read the first band:
 * it shows where the difference starts. Run the same baseline twice to see
 * the noise floor (marquee, sliders, third-party pop-ups).
 */

import { existsSync, readFileSync } from 'node:fs'
import { mkdir, writeFile } from 'node:fs/promises'
import path from 'node:path'
import { decodePng, encodePng } from './lib/png.mjs'

const argv = process.argv.slice(2).filter((a) => a !== '--')
const thresholdIndex = argv.indexOf('--threshold')
const threshold = thresholdIndex !== -1 ? parseInt(argv[thresholdIndex + 1], 10) || 24 : 24
const files = argv.filter((a, i) => !a.startsWith('--') && (thresholdIndex === -1 || i !== thresholdIndex + 1))

if (files.length !== 2 || argv.includes('--help')) {
	console.log('usage: pnpm perf:diff <before.json> <after.json> [--threshold n]')
	process.exit(files.length === 2 ? 0 : 1)
}

const OUT_DIR = 'build/perf/diff'
// A row only counts as changed with at least this many changed pixels.
const MIN_PIXELS_PER_ROW = 3
// Changed rows closer than this are reported as one band.
const BAND_GAP = 24
const MAX_BANDS = 8

const [before, after] = files.map((file) => JSON.parse(readFileSync(file, 'utf8')))

if (before.device !== after.device) {
	console.error(`Different devices: ${before.device} vs ${after.device}`)
	process.exit(1)
}

const name = (file) => path.basename(file, '.json')
const slug = (url) => new URL(url).pathname.replace(/^\/|\/$/g, '').replace(/[^\w-]+/g, '-') || 'home'
const pad = (value, width) => String(value).padEnd(width)

function bands(rows) {
	const out = []

	for (const y of rows) {
		const last = out[out.length - 1]
		if (last && y - last[1] <= BAND_GAP) last[1] = y
		else out.push([y, y])
	}

	return out
}

function compare(a, b) {
	const width = Math.min(a.width, b.width)
	const height = Math.max(a.height, b.height)
	const bppA = a.color === 6 ? 4 : 3
	const bppB = b.color === 6 ? 4 : 3
	const out = []
	const changedRows = []
	let changed = 0

	for (let y = 0; y < height; y++) {
		const rowA = a.rows[y]
		const rowB = b.rows[y]
		const row = Buffer.alloc(width * 3)
		let rowChanged = 0

		for (let x = 0; x < width; x++) {
			const base = rowB || rowA
			const bpp = rowB ? bppB : bppA
			let diff = !rowA || !rowB

			if (!diff) {
				for (let c = 0; c < 3; c++) {
					if (Math.abs(rowA[x * bppA + c] - rowB[x * bppB + c]) > threshold) {
						diff = true
						break
					}
				}
			}

			const o = x * 3

			if (diff) {
				rowChanged++
				row[o] = 255
				row[o + 1] = 0
				row[o + 2] = 0
			} else {
				// Faded toward white, so the red stands out.
				for (let c = 0; c < 3; c++) row[o + c] = 255 - ((255 - base[x * bpp + c]) >> 2)
			}
		}

		if (rowChanged >= MIN_PIXELS_PER_ROW) {
			changed += rowChanged
			changedRows.push(y)
		}

		out.push(row)
	}

	return { width, height, changedPct: (changed / (width * height)) * 100, bands: bands(changedRows), image: encodePng(width, 2, out) }
}

await mkdir(OUT_DIR, { recursive: true })

const afterPages = new Map(after.pages.map((p) => [p.url, p]))
const widths = [34, 9, 15, 0]

console.log(`\n${name(files[0])} → ${name(files[1])} (${before.device}, threshold ${threshold})\n`)
console.log('  ' + ['page', 'changed', 'height', 'changed rows (px from top)'].map((c, i) => pad(c, widths[i])).join(' '))

for (const pageA of before.pages) {
	const pageB = afterPages.get(pageA.url)
	const label = new URL(pageA.url).pathname
	const shotA = pageA.screenshots?.full
	const shotB = pageB?.screenshots?.full

	if (!shotA || !shotB || !existsSync(shotA) || !existsSync(shotB)) {
		console.log(`  ${pad(label, widths[0])} no full-page screenshot in ${!shotA || !existsSync(shotA) ? 'before' : 'after'}`)
		continue
	}

	const a = decodePng(readFileSync(shotA))
	const b = decodePng(readFileSync(shotB))
	const result = compare(a, b)
	const file = path.join(OUT_DIR, `${before.label}-vs-${after.label}-${before.device}-${slug(pageA.url)}.png`)

	await writeFile(file, result.image)

	const shown = result.bands.slice(0, MAX_BANDS).map(([from, to]) => (from === to ? `${from}` : `${from}–${to}`))
	const more = result.bands.length > MAX_BANDS ? ` (+${result.bands.length - MAX_BANDS} more)` : ''
	const height = a.height === b.height ? `${a.height}` : `${a.height}→${b.height}`

	console.log('  ' + [pad(label.length > 34 ? `…${label.slice(-33)}` : label, widths[0]), pad(`${result.changedPct.toFixed(2)}%`, widths[1]), pad(height, widths[2]), (shown.join(', ') || 'none') + more].join(' '))
}

console.log(`\n  Diff images: ${OUT_DIR}/${before.label}-vs-${after.label}-${before.device}-*.png`)
