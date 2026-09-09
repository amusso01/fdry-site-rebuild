import '../styles/main.scss'

// JS IMPORT
import smoothscroll from 'smoothscroll-polyfill'
import gsapMotion from './part/gsap'
import hamburger from './part/hamburger'
import marquee from './part/marquee'
import heroVideo from './part/heroVideo'
import navMenu from './part/navMenu'
import navAccordion from './part/navAccordion'

document.addEventListener('DOMContentLoaded', () => {
	smoothscroll.polyfill()
	gsapMotion.init()
	hamburger()
	navMenu()
	navAccordion()
	marquee()
	heroVideo()
})
