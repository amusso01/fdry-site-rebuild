import '../styles/main.scss'

// JS IMPORT
// Must stay the first JS import: it hides a page-level window.gsap until the
// modules below have registered ScrollTrigger with our own GSAP.
import { restoreGlobalGsap } from './part/isolateGsap'
import smoothscroll from 'smoothscroll-polyfill'
import smoothScroll from './part/smoothScroll'
import gsapMotion from './part/gsap'
import hamburger from './part/hamburger'
import marquee from './part/marquee'
import heroVideo from './part/heroVideo'
import showreelModal from './part/showreelModal'
import navMenu from './part/navMenu'
import navAccordion from './part/navAccordion'
import footerAccordion from './part/footerAccordion'
import headerScroll from './part/headerScroll'
import workArchive from './part/workArchive'
import workRowSlider from './part/workRowSlider'
import teamSlider from './part/teamSlider'

restoreGlobalGsap()

// One module throwing must not stop the ones after it.
const run = (init) => {
	try {
		init()
	} catch (error) {
		console.error(error)
	}
}

document.addEventListener('DOMContentLoaded', () => {
	run(() => smoothscroll.polyfill())
	run(smoothScroll)
	run(gsapMotion.init)
	run(hamburger)
	run(headerScroll)
	run(navMenu)
	run(navAccordion)
	run(footerAccordion)
	run(marquee)
	run(heroVideo)
	run(showreelModal)
	run(workArchive)
	run(workRowSlider)
	run(teamSlider)
})
