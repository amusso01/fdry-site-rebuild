/**
 * Minimal PNG read/write for Chrome screenshots, shared by perf-measure.mjs
 * and perf-diff.mjs. No npm packages: node:zlib only.
 */

import { crc32, deflateSync, inflateSync } from 'node:zlib'

const PNG_SIGNATURE = Buffer.from([0x89, 0x50, 0x4e, 0x47, 0x0d, 0x0a, 0x1a, 0x0a])

const paeth = (left, up, upLeft) => {
	const p = left + up - upLeft
	const [a, b, c] = [Math.abs(p - left), Math.abs(p - up), Math.abs(p - upLeft)]

	return a <= b && a <= c ? left : b <= c ? up : upLeft
}

// Reads the 8-bit, non-interlaced RGB(A) PNGs Chrome writes, as unfiltered rows.
export function decodePng(buffer) {
	const idat = []
	let header

	for (let offset = 8; offset < buffer.length; ) {
		const length = buffer.readUInt32BE(offset)
		const type = buffer.toString('ascii', offset + 4, offset + 8)
		const body = buffer.subarray(offset + 8, offset + 8 + length)

		if (type === 'IHDR') header = { width: body.readUInt32BE(0), height: body.readUInt32BE(4), depth: body[8], color: body[9], interlace: body[12] }
		else if (type === 'IDAT') idat.push(body)

		offset += length + 12
	}

	if (!header || header.depth !== 8 || header.interlace || ![2, 6].includes(header.color)) throw new Error('Unexpected screenshot format')

	const bpp = header.color === 6 ? 4 : 3
	const stride = header.width * bpp
	const raw = inflateSync(Buffer.concat(idat))
	const rows = []
	let prev = Buffer.alloc(stride)

	for (let y = 0; y < header.height; y++) {
		const start = y * (stride + 1)
		const filter = raw[start]
		const row = Buffer.from(raw.subarray(start + 1, start + 1 + stride))

		for (let i = 0; i < stride; i++) {
			const left = i >= bpp ? row[i - bpp] : 0

			if (filter === 1) row[i] += left
			else if (filter === 2) row[i] += prev[i]
			else if (filter === 3) row[i] += (left + prev[i]) >> 1
			else if (filter === 4) row[i] += paeth(left, prev[i], i >= bpp ? prev[i - bpp] : 0)
		}

		rows.push(row)
		prev = row
	}

	return { ...header, rows }
}

export function encodePng(width, color, rows) {
	const chunk = (type, body) => {
		const typed = Buffer.concat([Buffer.from(type, 'ascii'), body])
		const out = Buffer.alloc(typed.length + 8)
		out.writeUInt32BE(body.length, 0)
		typed.copy(out, 4)
		out.writeUInt32BE(crc32(typed), typed.length + 4)
		return out
	}
	const ihdr = Buffer.alloc(13)
	ihdr.writeUInt32BE(width, 0)
	ihdr.writeUInt32BE(rows.length, 4)
	ihdr[8] = 8
	ihdr[9] = color

	return Buffer.concat([PNG_SIGNATURE, chunk('IHDR', ihdr), chunk('IDAT', deflateSync(Buffer.concat(rows.flatMap((row) => [Buffer.from([0]), row])))), chunk('IEND', Buffer.alloc(0))])
}
