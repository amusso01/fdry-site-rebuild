import '../styles/main.scss'

// JS IMPORT
import smoothscroll from 'smoothscroll-polyfill'
import gsapMotion from './part/gsap'
import hamburger from './part/hamburger'
import marquee from './part/marquee'

document.addEventListener('DOMContentLoaded', () => {
	smoothscroll.polyfill()
	gsapMotion.init()
	hamburger()
	marquee()
})
