// GSAP plugins (ScrollTrigger, MotionPath) register themselves with
// window.gsap the moment they are imported. If a third party (GTM, a
// WordPress plugin, an old inline script) has already put its own GSAP there,
// they would tie to that copy instead of ours: every scrollTrigger in this
// bundle would be ignored and parallax would play on load. Hide the global
// while our modules load; main.js restores it.
const foreignGsap = window.gsap

if (foreignGsap) {
	window.gsap = undefined
}

export function restoreGlobalGsap() {
	if (foreignGsap) {
		window.gsap = foreignGsap
	}
}
