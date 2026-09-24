import gsap from 'gsap'
import { MotionPathPlugin } from 'gsap/MotionPathPlugin'

gsap.registerPlugin(MotionPathPlugin)

const SVG_NS = 'http://www.w3.org/2000/svg'

// Seconds each slot waits before its first comet (one range per slot, so
// they don't start together), then between comets. The number of ranges is
// the most comets on screen at once.
const FIRST_DELAY = [
	[0.2, 1.2],
	[1.5, 4],
	[3, 6],
]
const DELAY = [0.8, 3.5]
// Least time between two comets starting, so they never pop in together.
const MIN_GAP = 0.8
// Travel speed in px per second, picked per comet, and the least difference
// from every other comet on screen so no two move alike.
const SPEED_MIN = 140
const SPEED_MAX = 350
const SPEED_GAP = 40
// Chance of turning onto the crossing line at each inner crossing, and the
// most turns one comet takes.
const TURN_CHANCE = 0.35
const MAX_TURNS = 2
// Tries to find a route that shares no line with the other comets.
const ROUTE_TRIES = 5
// Length of the bright dash, in px.
const DASH = 20
// Glow ellipse radii and blur, in px.
const GLOW_RX = 14
const GLOW_RY = 3
const GLOW_BLUR = 4
// Seconds to fade a comet in, and out as it reaches the edge.
const FADE_IN = 0.3
const FADE_OUT = 0.5
// Radius of the rounded turns, in px.
const CORNER = 12
// Half the width of a plus mark (the old 15px icon).
const CROSS = 7.44
const RESIZE_DEBOUNCE = 200
const FILTER_ID = 'tech-grid-glow'

function svgElement(name, attributes = {}) {
	const element = document.createElementNS(SVG_NS, name)

	Object.entries(attributes).forEach(([key, value]) => {
		element.setAttribute(key, value)
	})

	return element
}

const round = (value) => Math.round(value * 100) / 100
const chance = (probability) => Math.random() < probability
const randomInt = (min, max) => gsap.utils.random(min, max, 1)

// Positions of the grid lines along one axis: the outer edges plus the
// centre of every gap between tracks.
function gridLines(tracks, gap, total) {
	const sizes = tracks.split(' ').map(parseFloat)
	const lines = [0]
	let edge = 0

	sizes.slice(0, -1).forEach((size) => {
		edge += size
		lines.push(edge + gap / 2)
		edge += gap
	})

	lines.push(total)

	return lines
}

function measure(grid) {
	const style = getComputedStyle(grid)
	const width = grid.clientWidth
	const height = grid.clientHeight

	return {
		width,
		height,
		x: gridLines(
			style.gridTemplateColumns,
			parseFloat(style.columnGap) || 0,
			width,
		),
		y: gridLines(style.gridTemplateRows, parseFloat(style.rowGap) || 0, height),
	}
}

// A random walk in grid-line indices, [column line, row line], where line 0
// is the outer edge. It comes in from an outer edge on a drawn line, steps
// from crossing to crossing, sometimes turns 90°, and leaves at an outer edge.
// Every point is a crossing of two drawn lines, so it never runs off them.
function randomRoute(cols, rows) {
	const horizontal = cols < 2 || (rows > 1 && chance(0.5))
	const forward = chance(0.5)
	let point = horizontal
		? [forward ? 0 : cols, randomInt(1, rows - 1)]
		: [randomInt(1, cols - 1), forward ? 0 : rows]
	let step = horizontal ? [forward ? 1 : -1, 0] : [0, forward ? 1 : -1]
	const points = [point]
	let turns = 0

	for (;;) {
		point = [point[0] + step[0], point[1] + step[1]]

		if (
			point[0] <= 0 ||
			point[0] >= cols ||
			point[1] <= 0 ||
			point[1] >= rows
		) {
			points.push(point)

			return points
		}

		if (turns < MAX_TURNS && chance(TURN_CHANCE)) {
			const sign = chance(0.5) ? 1 : -1

			step = step[0] ? [0, sign] : [sign, 0]
			points.push(point)
			turns += 1
		}
	}
}

// A random speed at least SPEED_GAP away from every speed in `taken`. It
// picks from what is left of the range once those bands are cut out.
function randomSpeed(taken) {
	let ranges = [[SPEED_MIN, SPEED_MAX]]

	taken.forEach((speed) => {
		ranges = ranges
			.flatMap(([min, max]) => [
				[min, Math.min(max, speed - SPEED_GAP)],
				[Math.max(min, speed + SPEED_GAP), max],
			])
			.filter(([min, max]) => max > min)
	})

	const total = ranges.reduce((sum, [min, max]) => sum + max - min, 0)
	let offset = Math.random() * total

	for (const [min, max] of ranges) {
		if (offset <= max - min) {
			return min + offset
		}

		offset -= max - min
	}

	// Nothing left (only if the constants leave no room): any speed.
	return gsap.utils.random(SPEED_MIN, SPEED_MAX)
}

// The lines a route travels on, e.g. ["row-1", "col-4"].
function routeLines(route) {
	return route
		.slice(1)
		.map((point, i) =>
			route[i][1] === point[1] ? `row-${point[1]}` : `col-${point[0]}`,
		)
}

function pointTowards(from, to, distance) {
	const length = Math.hypot(to.x - from.x, to.y - from.y)

	return {
		x: from.x + ((to.x - from.x) * distance) / length,
		y: from.y + ((to.y - from.y) * distance) / length,
	}
}

// Straight segments through the points, with each turn rounded off.
function routePath(points) {
	const [first] = points
	const last = points[points.length - 1]
	let d = `M${round(first.x)},${round(first.y)}`

	for (let i = 1; i < points.length - 1; i++) {
		const [previous, corner, next] = [points[i - 1], points[i], points[i + 1]]
		const radius = Math.min(
			CORNER,
			Math.hypot(corner.x - previous.x, corner.y - previous.y) / 2,
			Math.hypot(next.x - corner.x, next.y - corner.y) / 2,
		)
		const start = pointTowards(corner, previous, radius)
		const end = pointTowards(corner, next, radius)

		d += ` L${round(start.x)},${round(start.y)}`
		d += ` Q${round(corner.x)},${round(corner.y)} ${round(end.x)},${round(end.y)}`
	}

	return `${d} L${round(last.x)},${round(last.y)}`
}

function crossesPath(lines) {
	let d = ''

	lines.x.slice(1, -1).forEach((x) => {
		lines.y.slice(1, -1).forEach((y) => {
			d += `M${round(x - CROSS)},${round(y)}H${round(x + CROSS)}`
			d += `M${round(x)},${round(y - CROSS)}V${round(y + CROSS)}`
		})
	})

	return d
}

// The overlay: plus marks at every inner crossing. Comets are added and
// removed as they run.
function buildScene(grid) {
	const lines = measure(grid)
	const svg = svgElement('svg', {
		class: 'tech-grid__svg',
		viewBox: `0 0 ${lines.width} ${lines.height}`,
		'aria-hidden': 'true',
		focusable: 'false',
	})
	const defs = svgElement('defs')
	const filter = svgElement('filter', {
		id: FILTER_ID,
		x: '-100%',
		y: '-300%',
		width: '300%',
		height: '700%',
	})

	filter.appendChild(svgElement('feGaussianBlur', { stdDeviation: GLOW_BLUR }))
	defs.appendChild(filter)
	svg.append(
		defs,
		svgElement('path', { class: 'tech-grid__cross', d: crossesPath(lines) }),
	)
	grid.appendChild(svg)

	return { svg, lines, cols: lines.x.length - 1, rows: lines.y.length - 1 }
}

// One comet: a faint trail that draws in, a short bright dash, and a
// blurred glow at its head.
function buildComet(svg, route, lines) {
	const d = routePath(
		route.map(([column, row]) => ({ x: lines.x[column], y: lines.y[row] })),
	)
	const group = svgElement('g', { class: 'tech-grid__route' })
	const trail = svgElement('path', { class: 'tech-grid__trail', d })
	const comet = svgElement('path', { class: 'tech-grid__comet', d })
	const glow = svgElement('ellipse', {
		class: 'tech-grid__glow',
		rx: GLOW_RX,
		ry: GLOW_RY,
		filter: `url(#${FILTER_ID})`,
	})

	group.append(trail, comet, glow)
	svg.appendChild(group)

	const length = comet.getTotalLength()

	trail.setAttribute('stroke-dasharray', `${length} ${length}`)
	comet.setAttribute('stroke-dasharray', `${DASH} ${length + DASH}`)

	return { group, trail, comet, glow, length }
}

function cometTimeline({ group, trail, comet, glow, length }, speed) {
	const duration = length / speed

	return gsap
		.timeline()
		.fromTo(
			group,
			{ opacity: 0 },
			{ opacity: 1, duration: FADE_IN, ease: 'none' },
			0,
		)
		.fromTo(
			trail,
			{ strokeDashoffset: length },
			{ strokeDashoffset: 0, duration, ease: 'none' },
			0,
		)
		.fromTo(
			comet,
			{ strokeDashoffset: DASH + 2 },
			{ strokeDashoffset: -(length + 2), duration, ease: 'none' },
			0,
		)
		.to(
			glow,
			{
				motionPath: {
					path: comet,
					align: comet,
					alignOrigin: [0.5, 0.5],
					autoRotate: true,
				},
				duration,
				ease: 'none',
			},
			0,
		)
		.to(
			group,
			{ opacity: 0, duration: FADE_OUT, ease: 'none' },
			Math.max(FADE_IN, duration - FADE_OUT),
		)
}

export default function techGrid() {
	const grid = document.querySelector('[data-tech-grid]')

	if (!grid) {
		return
	}

	const reduceMotion = window.matchMedia(
		'(prefers-reduced-motion: reduce)',
	).matches
	// Every comet timeline and pending delay, so they can be paused together.
	const live = new Set()
	// The lines and speed of each slot's comet on screen.
	const lanes = new Map()
	const speeds = new Map()
	let lastLaunch = -Infinity
	let scene = null
	let visible = false

	const track = (animation) => {
		live.add(animation)

		if (!visible) {
			animation.pause()
		}

		return animation
	}

	const launch = (slot) => {
		const gap = MIN_GAP - (gsap.ticker.time - lastLaunch)

		if (gap > 0) {
			wait(slot, [gap, gap])

			return
		}

		lastLaunch = gsap.ticker.time

		const taken = [...lanes.values()].flat()
		const clashes = (route) =>
			routeLines(route).some((line) => taken.includes(line))
		let route = randomRoute(scene.cols, scene.rows)

		for (let tries = 1; tries < ROUTE_TRIES && clashes(route); tries++) {
			route = randomRoute(scene.cols, scene.rows)
		}

		const parts = buildComet(scene.svg, route, scene.lines)
		const speed = randomSpeed([...speeds.values()])
		const timeline = track(cometTimeline(parts, speed))

		lanes.set(slot, routeLines(route))
		speeds.set(slot, speed)
		timeline.eventCallback('onComplete', () => {
			live.delete(timeline)
			lanes.delete(slot)
			speeds.delete(slot)
			parts.group.remove()
			wait(slot, DELAY)
		})
	}

	const wait = (slot, [min, max]) => {
		const call = track(
			gsap.delayedCall(gsap.utils.random(min, max), () => {
				live.delete(call)
				launch(slot)
			}),
		)
	}

	const build = () => {
		live.forEach((animation) => animation.kill())
		live.clear()
		lanes.clear()
		speeds.clear()
		scene?.svg.remove()
		scene = null

		if (!grid.clientWidth) {
			return
		}

		scene = buildScene(grid)

		if (!reduceMotion) {
			FIRST_DELAY.forEach((range, slot) => wait(slot, range))
		}
	}

	build()

	// The grid sits in the footer on every page; only animate it on screen.
	new IntersectionObserver(([entry]) => {
		visible = entry.isIntersecting
		live.forEach((animation) =>
			visible ? animation.resume() : animation.pause(),
		)
	}).observe(grid)

	// Rebuild on the new lines when the grid changes size, including the
	// switch between 8 and 2 columns.
	let width = grid.clientWidth
	let height = grid.clientHeight
	let timer = null

	new ResizeObserver(() => {
		if (grid.clientWidth === width && grid.clientHeight === height) {
			return
		}

		width = grid.clientWidth
		height = grid.clientHeight
		clearTimeout(timer)
		timer = setTimeout(build, RESIZE_DEBOUNCE)
	}).observe(grid)
}
