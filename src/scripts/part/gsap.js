import gsap from 'gsap'
import { ScrollTrigger } from 'gsap/ScrollTrigger'
import { initReveal } from './gsapReveal'
import { initParallax } from './gsapParallax'

gsap.registerPlugin(ScrollTrigger)

function prefersReducedMotion() {
	return window.matchMedia('(prefers-reduced-motion: reduce)').matches
}

function refreshOnLoad() {
	const refresh = () => ScrollTrigger.refresh()

	if (document.readyState === 'complete') {
		refresh()
		return
	}

	window.addEventListener('load', refresh, { once: true })
}

function init() {
	if (prefersReducedMotion()) {
		return
	}

	initReveal()
	initParallax()
	refreshOnLoad()
}

export default { init }
