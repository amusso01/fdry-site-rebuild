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
import headerScroll from './part/headerScroll'
import workArchive from './part/workArchive'
import workRowSlider from './part/workRowSlider'
import teamSlider from './part/teamSlider'

restoreGlobalGsap()

document.addEventListener('DOMContentLoaded', () => {
	smoothscroll.polyfill()
	smoothScroll()
	gsapMotion.init()
	hamburger()
	headerScroll()
	navMenu()
	navAccordion()
	marquee()
	heroVideo()
	showreelModal()
	workArchive()
	workRowSlider()
	teamSlider()
})
