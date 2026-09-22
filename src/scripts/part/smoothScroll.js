import Lenis from 'lenis'
import gsap from 'gsap'
import { ScrollTrigger } from 'gsap/ScrollTrigger'

// easeOutExpo over a fixed 1.2s per wheel step, the same curve as
// lionandmason.com. With duration set, Lenis ignores lerp.
const SCROLL_DURATION = 1.2
const easeOutExpo = (t) => Math.min(1, 1.001 - Math.pow(2, -10 * t))

let lenis = null

function prefersReducedMotion() {
	return window.matchMedia('(prefers-reduced-motion: reduce)').matches
}

/**
 * The running instance, or null where smooth scroll is off (legacy header,
 * reduced motion).
 */
export function getLenis() {
	return lenis
}

/**
 * Same-page hash links glide to their target below the fixed header instead of
 * jumping. Anything this does not handle falls through to the browser.
 */
function initAnchors(header) {
	document.addEventListener('click', (event) => {
		if (event.defaultPrevented || event.button !== 0) return
		if (event.metaKey || event.ctrlKey || event.shiftKey || event.altKey) return
		if (!(event.target instanceof Element)) return

		const link = event.target.closest('a[href*="#"]')

		// The skip link keeps the native jump, which also moves focus.
		if (!link || link.target === '_blank' || link.classList.contains('skip-link')) return
		if (link.hash.length < 2 || link.href.split('#')[0] !== location.href.split('#')[0]) return

		// A stopped Lenis ignores scrollTo, e.g. for a hash link in the open menu.
		if (lenis.isStopped) return

		const target = document.getElementById(decodeURIComponent(link.hash.slice(1)))

		if (!target) return

		event.preventDefault()

		lenis.scrollTo(target, { offset: -(header?.offsetHeight || 0) })
		history.pushState(null, '', link.hash)

		// Move focus with the scroll, as a native jump would, so the next Tab
		// carries on from the target.
		if (!target.matches('a[href], button, input, select, textarea, [tabindex]')) {
			target.setAttribute('tabindex', '-1')
		}

		target.focus({ preventScroll: true })
	})
}

export default function smoothScroll() {
	if (!document.body.classList.contains('fdry-new-header') || prefersReducedMotion()) {
		return
	}

	gsap.registerPlugin(ScrollTrigger)

	// Nested scroll areas keep native scrolling: the nav overlay, and the
	// cookie and newsletter pop-ups, whose markup we cannot tag with
	// data-lenis-prevent. Touch stays native too (syncTouch is off).
	lenis = new Lenis({
		duration: SCROLL_DURATION,
		easing: easeOutExpo,
		allowNestedScroll: true,
	})

	// One clock: Lenis steps on GSAP's ticker and updates ScrollTrigger in the
	// same frame, so scroll-driven tweens never trail the scroll.
	lenis.on('scroll', ScrollTrigger.update)
	gsap.ticker.add((time) => lenis.raf(time * 1000))
	gsap.ticker.lagSmoothing(0)

	// overflow: hidden does not stop Lenis, since it scrolls with
	// window.scrollTo(). It has to be stopped while an overlay holds the page.
	const toggle = (event) => {
		if (event.detail?.isOpen) {
			lenis.stop()
		} else {
			lenis.start()
		}
	}

	document.addEventListener('fdry:nav-toggle', toggle)
	document.addEventListener('fdry:showreel-toggle', toggle)

	initAnchors(document.querySelector('.site-header'))
}
