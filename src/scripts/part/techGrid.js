import gsap from 'gsap'

// The comets are drawn on a <canvas>, never as animated DOM: Hotjar and the
// Tailwind browser runtime both watch DOM changes, and per-frame style or
// attribute writes (~1,000 a second) froze tabs left open on the footer.

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
// Least time between two comets starting, so they never pop in together,
// and the shortest wait when a comet is held back by it.
const MIN_GAP = 0.8
const MIN_RETRY = 0.05
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
// Length and width of the bright dash, and width and opacity of the faint
// trail it draws behind it, in px.
const DASH = 20
const DASH_WIDTH = 1.25
const TRAIL_WIDTH = 1
const TRAIL_ALPHA = 0.2
// The glow: an ellipse (radii in px) at GLOW_ALPHA, blurred like a Gaussian
// with a GLOW_BLUR px standard deviation (the old SVG feGaussianBlur).
const GLOW_RX = 14
const GLOW_RY = 3
const GLOW_BLUR = 4
const GLOW_ALPHA = 0.5
// How far the glow can reach past its centre: the ellipse plus 3 standard
// deviations of blur.
const GLOW_PAD = GLOW_RX + GLOW_BLUR * 3
// Spacing of the points sampled along a route for the glow, in px.
const SAMPLE_STEP = 4
// Sharper canvas on high-density screens, capped to keep it cheap.
const MAX_DPR = 2
// Seconds to fade a comet in, and out as it reaches the edge.
const FADE_IN = 0.3
const FADE_OUT = 0.5
// Radius of the rounded turns, in px.
const CORNER = 12
// Half the width of a plus mark (the old 15px icon).
const CROSS = 7.44
const RESIZE_DEBOUNCE = 200

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

// The accent colour from the CSS custom property, as [r, g, b], so the
// canvas can fade it. Letting the canvas parse it accepts any CSS colour.
function accentRgb(grid, ctx) {
	const value = getComputedStyle(grid).getPropertyValue('--tech-grid-accent').trim()

	ctx.fillStyle = '#4951f2'
	ctx.fillStyle = value || ctx.fillStyle

	// The canvas reports "#rrggbb", or "rgba(r, g, b, a)" for colours with alpha.
	const color = ctx.fillStyle

	if (color.startsWith('#')) {
		return [1, 3, 5].map((i) => parseInt(color.slice(i, i + 2), 16))
	}

	return color.match(/[\d.]+/g).slice(0, 3).map(Number)
}

// The glow, blurred once into an off-screen canvas (never added to the page)
// and stamped each frame. Only the ellipse's shadow is kept: it is drawn far
// to the side and its shadow offset back into view. A canvas shadow blur of B
// is a Gaussian with a standard deviation of B / 2, so this matches the old
// SVG feGaussianBlur exactly. Shadow blur and offset ignore the canvas
// transform, hence the scaling by dpr.
function glowSprite([r, g, b], dpr) {
	const width = GLOW_PAD * 2
	const height = (GLOW_RY + GLOW_BLUR * 3) * 2
	const sprite = document.createElement('canvas')
	const ctx = sprite.getContext('2d')
	const shift = width * 2

	sprite.width = Math.ceil(width * dpr)
	sprite.height = Math.ceil(height * dpr)
	ctx.scale(dpr, dpr)
	ctx.shadowColor = `rgba(${r}, ${g}, ${b}, ${GLOW_ALPHA})`
	ctx.shadowBlur = GLOW_BLUR * 2 * dpr
	ctx.shadowOffsetX = shift * dpr
	ctx.fillStyle = '#000'
	ctx.beginPath()
	ctx.ellipse(width / 2 - shift, height / 2, GLOW_RX, GLOW_RY, 0, 0, Math.PI * 2)
	ctx.fill()

	return { sprite, width, height }
}

// The overlay: a static SVG with the plus marks, written once, and a canvas
// on top that the comets are drawn on.
function buildScene(grid) {
	const lines = measure(grid)
	const svg = svgElement('svg', {
		class: 'tech-grid__svg',
		viewBox: `0 0 ${lines.width} ${lines.height}`,
		'aria-hidden': 'true',
		focusable: 'false',
	})
	const canvas = document.createElement('canvas')
	const ctx = canvas.getContext('2d')
	const dpr = Math.min(window.devicePixelRatio || 1, MAX_DPR)

	svg.appendChild(
		svgElement('path', { class: 'tech-grid__cross', d: crossesPath(lines) }),
	)
	// The canvas reaches GLOW_PAD past the grid on every side so a glow
	// entering or leaving at an outer edge isn't cut off; its origin is the
	// grid's top-left corner.
	canvas.className = 'tech-grid__canvas'
	canvas.setAttribute('aria-hidden', 'true')
	canvas.style.inset = `${-GLOW_PAD}px`
	canvas.style.width = `calc(100% + ${GLOW_PAD * 2}px)`
	canvas.style.height = `calc(100% + ${GLOW_PAD * 2}px)`
	canvas.width = Math.round((lines.width + GLOW_PAD * 2) * dpr)
	canvas.height = Math.round((lines.height + GLOW_PAD * 2) * dpr)
	ctx.setTransform(dpr, 0, 0, dpr, GLOW_PAD * dpr, GLOW_PAD * dpr)
	grid.append(svg, canvas)

	const rgb = accentRgb(grid, ctx)

	return {
		svg,
		canvas,
		ctx,
		lines,
		accent: `rgb(${rgb.join(', ')})`,
		glow: glowSprite(rgb, dpr),
		cols: lines.x.length - 1,
		rows: lines.y.length - 1,
		dirty: false,
	}
}

// One comet's geometry: the route as a Path2D to stroke, and points sampled
// along it for placing and turning the glow. The samples come from a
// throwaway <path> (added, measured, removed), since Path2D can't be measured.
function buildComet(scene, route) {
	const d = routePath(
		route.map(([column, row]) => ({
			x: scene.lines.x[column],
			y: scene.lines.y[row],
		})),
	)
	const probe = svgElement('path', { d, fill: 'none' })

	scene.svg.appendChild(probe)

	const length = probe.getTotalLength()
	const count = Math.max(1, Math.ceil(length / SAMPLE_STEP))
	const points = []

	for (let i = 0; i <= count; i++) {
		const { x, y } = probe.getPointAtLength((length * i) / count)

		points.push({ x, y })
	}

	probe.remove()

	return {
		path: new Path2D(d),
		length,
		points,
		step: length / count,
		// Animated by GSAP; only these numbers change while it runs.
		state: { alpha: 0, progress: 0 },
	}
}

// The glow's position and heading at a distance along the route.
function pointAt({ points, step }, distance) {
	const index = Math.min(points.length - 2, Math.floor(distance / step))
	const from = points[Math.max(0, index)]
	const to = points[Math.max(0, index) + 1]
	const t = Math.min(1, Math.max(0, distance / step - index))

	return {
		x: from.x + (to.x - from.x) * t,
		y: from.y + (to.y - from.y) * t,
		angle: Math.atan2(to.y - from.y, to.x - from.x),
	}
}

// Trail, dash and glow for one comet. The dash's head travels from the start
// of the route to DASH past its end, so it slides fully out. The glow sits on
// the middle of the dash, and the trail ends there too.
function drawComet(scene, comet) {
	const { ctx, accent, glow } = scene
	const { path, length, state } = comet
	const head = state.progress * (length + DASH)
	const centre = Math.min(length, Math.max(0, head - DASH / 2))

	if (state.alpha <= 0) {
		return
	}

	ctx.strokeStyle = accent

	ctx.globalAlpha = TRAIL_ALPHA * state.alpha
	ctx.lineWidth = TRAIL_WIDTH
	ctx.lineCap = 'butt'
	ctx.setLineDash([centre, length + 1])
	ctx.lineDashOffset = 0
	ctx.stroke(path)

	ctx.globalAlpha = state.alpha
	ctx.lineWidth = DASH_WIDTH
	ctx.lineCap = 'round'
	ctx.setLineDash([DASH, length + DASH])
	ctx.lineDashOffset = DASH - head
	ctx.stroke(path)

	const { x, y, angle } = pointAt(comet, centre)

	ctx.save()
	ctx.globalAlpha = state.alpha
	ctx.translate(x, y)
	ctx.rotate(angle)
	ctx.drawImage(glow.sprite, -glow.width / 2, -glow.height / 2, glow.width, glow.height)
	ctx.restore()
}

// Tweens the comet's plain state object: nothing is written to the DOM.
function cometTimeline({ length, state }, speed) {
	const duration = (length + DASH) / speed

	return gsap
		.timeline()
		.fromTo(
			state,
			{ alpha: 0 },
			{ alpha: 1, duration: FADE_IN, ease: 'none' },
			0,
		)
		.fromTo(
			state,
			{ progress: 0 },
			{ progress: 1, duration, ease: 'none' },
			0,
		)
		.to(
			state,
			{ alpha: 0, duration: FADE_OUT, ease: 'none' },
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
	// The comets being drawn, and the lines and speed of each slot's comet.
	const comets = new Set()
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

		// Never retry after a near-zero delay: rounding can leave `gap` at
		// ~1e-15, GSAP rounds that delay to 0 and can run the call again in the
		// same tick, where `gap` is unchanged, and the page loops forever.
		if (gap > 0) {
			const delay = Math.max(gap, MIN_RETRY)

			wait(slot, [delay, delay])

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

		const comet = buildComet(scene, route)
		const speed = randomSpeed([...speeds.values()])
		const timeline = track(cometTimeline(comet, speed))

		comets.add(comet)
		lanes.set(slot, routeLines(route))
		speeds.set(slot, speed)
		timeline.eventCallback('onComplete', () => {
			live.delete(timeline)
			comets.delete(comet)
			lanes.delete(slot)
			speeds.delete(slot)
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

	// Redraws the canvas each frame while comets are on screen, and clears it
	// once after the last one goes.
	const draw = () => {
		if (!scene || !visible || (!comets.size && !scene.dirty)) {
			return
		}

		scene.ctx.clearRect(
			-GLOW_PAD,
			-GLOW_PAD,
			scene.lines.width + GLOW_PAD * 2,
			scene.lines.height + GLOW_PAD * 2,
		)
		comets.forEach((comet) => drawComet(scene, comet))
		scene.dirty = comets.size > 0
	}

	const build = () => {
		live.forEach((animation) => animation.kill())
		live.clear()
		comets.clear()
		lanes.clear()
		speeds.clear()
		scene?.svg.remove()
		scene?.canvas.remove()
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

	if (!reduceMotion) {
		gsap.ticker.add(draw)
	}

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
