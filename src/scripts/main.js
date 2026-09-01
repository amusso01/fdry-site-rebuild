import '../styles/main.scss'

// JS IMPORT
import smoothscroll from 'smoothscroll-polyfill'
import gsapMotion from './part/gsap'
import hamburger from './part/hamburger'
import marquee from './part/marquee'
import navMenu from './part/navMenu'

document.addEventListener('DOMContentLoaded', () => {
	smoothscroll.polyfill()
	gsapMotion.init()
	hamburger()
	navMenu()
	marquee()
})
