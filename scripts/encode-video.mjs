#!/usr/bin/env node
/**
 * Encode a master video into web-ready hero assets.
 *
 * Usage:
 *   pnpm encode <input> <output-basename> [options]
 *
 * Options:
 *   --outdir <dir>    Output directory (default: build/video)
 *   --crf <n>         x264 CRF, lower = higher quality (default: 18)
 *   --vp9-crf <n>     libvpx-vp9 CRF (default: 24)
 *   --skip-webm       Do not encode VP9
 *   --skip-mp4        Do not encode H.264
 *   --poster-only     Only extract the poster
 *
 * Produces <name>.mp4, <name>.webm and <name>-poster.webp.
 *
 * Settings are deliberately high quality: the source grading is the desired
 * quality, so these are transparency thresholds rather than efficiency
 * sweet spots. See the hero video plan for the reasoning.
 */

import { execFile } from 'node:child_process'
import { promisify } from 'node:util'
import { mkdir, stat, unlink } from 'node:fs/promises'
import path from 'node:path'
import ffmpegPath from 'ffmpeg-static'
import sharp from 'sharp'

const run = promisify(execFile)

const argv = process.argv.slice(2)
const flag = (name) => argv.includes(`--${name}`)
const opt = (name, fallback) => {
	const i = argv.indexOf(`--${name}`)
	return i !== -1 && argv[i + 1] ? argv[i + 1] : fallback
}
const positional = argv.filter((a, i) => {
	if (a.startsWith('--')) return false
	const prev = argv[i - 1]
	return !(prev && prev.startsWith('--') && !['skip-webm', 'skip-mp4', 'poster-only'].includes(prev.slice(2)))
})

const [input, basename] = positional

if (!input || !basename) {
	console.error('usage: pnpm encode <input> <output-basename> [--outdir dir] [--crf 18] [--vp9-crf 24]')
	process.exit(1)
}

const outDir = opt('outdir', 'build/video')
const crf = opt('crf', '18')
const vp9Crf = opt('vp9-crf', '24')

const mb = (bytes) => (bytes / 1e6).toFixed(2)

async function size(file) {
	try {
		return (await stat(file)).size
	} catch {
		return null
	}
}

/** Read duration/resolution by parsing `ffmpeg -i` stderr — ffmpeg-static ships no ffprobe. */
async function probe(file) {
	let text = ''
	try {
		await run(ffmpegPath, ['-hide_banner', '-i', file])
	} catch (err) {
		text = `${err.stderr || ''}`
	}
	const dur = text.match(/Duration:\s*(\d+):(\d+):([\d.]+)/)
	const dims = text.match(/,\s*(\d{2,5})x(\d{2,5})[\s,]/)
	const fps = text.match(/([\d.]+)\s*fps/)
	const codec = text.match(/Video:\s*([a-z0-9]+)/i)
	return {
		duration: dur ? +dur[1] * 3600 + +dur[2] * 60 + parseFloat(dur[3]) : null,
		width: dims ? +dims[1] : null,
		height: dims ? +dims[2] : null,
		fps: fps ? parseFloat(fps[1]) : null,
		codec: codec ? codec[1] : null,
		hasAudio: /Stream #\d+:\d+.*: Audio:/.test(text),
	}
}

function report(label, file, bytes, duration) {
	const bitrate = duration ? ((bytes * 8) / duration / 1e6).toFixed(2) : '?'
	console.log(`  ${label.padEnd(8)} ${mb(bytes).padStart(8)} MB   ${String(bitrate).padStart(6)} Mbps   ${path.basename(file)}`)
}

const ffmpeg = async (args) => run(ffmpegPath, ['-hide_banner', '-loglevel', 'error', '-y', ...args], { maxBuffer: 1 << 26 })

await mkdir(outDir, { recursive: true })

const src = await probe(input)
console.log(`\nsource: ${path.basename(input)}`)
console.log(`  ${src.width}x${src.height}  ${src.duration?.toFixed(2)}s  ${src.fps ?? '?'}fps  ${src.codec}  audio: ${src.hasAudio ? 'yes' : 'none'}`)
const srcBytes = await size(input)
if (srcBytes) report('source', input, srcBytes, src.duration)

const mp4 = path.join(outDir, `${basename}.mp4`)
const webm = path.join(outDir, `${basename}.webm`)
const posterPng = path.join(outDir, `${basename}-poster.png`)
const posterWebp = path.join(outDir, `${basename}-poster.webp`)

console.log('\noutputs:')

if (!flag('poster-only') && !flag('skip-mp4')) {
	// No scale filter: source is already at target resolution, so scaling
	// would be a pointless resample. No fps filter: forcing a rate would
	// drop or duplicate frames.
	await ffmpeg([
		'-i', input,
		'-an',
		'-c:v', 'libx264',
		'-profile:v', 'high',
		'-level', '4.2',
		'-preset', 'veryslow',
		'-crf', crf,
		'-pix_fmt', 'yuv420p',
		'-g', '50',
		'-movflags', '+faststart',
		mp4,
	])
	report(`mp4 c${crf}`, mp4, await size(mp4), src.duration)
}

if (!flag('poster-only') && !flag('skip-webm')) {
	await ffmpeg([
		'-i', input,
		'-an',
		'-c:v', 'libvpx-vp9',
		'-crf', vp9Crf,
		'-b:v', '0',
		'-row-mt', '1',
		'-deadline', 'good',
		'-cpu-used', '1',
		webm,
	])
	report(`webm c${vp9Crf}`, webm, await size(webm), src.duration)
}

// Poster: extract frame one losslessly, then convert with sharp rather than
// ffmpeg's libwebp for finer control.
await ffmpeg(['-i', input, '-frames:v', '1', '-q:v', '1', posterPng])
await sharp(posterPng).webp({ quality: 90 }).toFile(posterWebp)
await unlink(posterPng).catch(() => {})
report('poster', posterWebp, await size(posterWebp), null)

console.log('')
