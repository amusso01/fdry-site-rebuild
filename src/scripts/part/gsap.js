import gsap from 'gsap'
import { ScrollTrigger } from 'gsap/ScrollTrigger'
import { initFade } from './gsapFade'
import { initParallax } from './gsapParallax'

gsap.registerPlugin(ScrollTrigger)

function prefersReducedMotion() {
	return window.matchMedia('(prefers-reduced-motion: reduce)').matches
}

const REFRESH_DEBOUNCE = 200

// Parallax is the only ScrollTrigger user (fades run on IntersectionObserver).
// ScrollTrigger measures its ranges once and only re-measures on window resize,
// but on a cold cache the layout keeps moving after load (runtime Tailwind CSS,
// late fonts, lazy images without dimensions), so the ranges drift. Re-measure
// whenever the body height settles on a new value.
function refreshOnLayoutChange() {
	const refresh = () => ScrollTrigger.refresh()

	if (document.readyState === 'complete') {
		refresh()
	} else {
		window.addEventListener('load', refresh, { once: true })
	}

	document.fonts?.ready.then(refresh)

	if (!('ResizeObserver' in window)) return

	let height = document.body.offsetHeight
	let timer = null

	// Width changes come with a window resize, which ScrollTrigger handles.
	new ResizeObserver(() => {
		const next = document.body.offsetHeight

		if (next === height) return

		height = next
		clearTimeout(timer)
		timer = setTimeout(refresh, REFRESH_DEBOUNCE)
	}).observe(document.body)
}

function init() {
	if (prefersReducedMotion()) {
		return
	}

	initFade()
	initParallax()

	// Every refresh jumps the page to the top and back to measure, so skip it on
	// pages with nothing to measure.
	if (ScrollTrigger.getAll().length) {
		refreshOnLayoutChange()
	}
}

export default { init }
