// footer.php includes loop-templates/tech-banner.php, which loads GSAP 3.12.5
// from a CDN onto window.gsap. ScrollTrigger registers itself with window.gsap
// the moment it is imported, which would tie it to that copy instead of ours:
// every scrollTrigger in this bundle would then be ignored and parallax would
// play on load. Hide the global while our modules load; main.js restores it.
const foreignGsap = window.gsap

if (foreignGsap) {
	window.gsap = undefined
}

export function restoreGlobalGsap() {
	if (foreignGsap) {
		window.gsap = foreignGsap
	}
}
