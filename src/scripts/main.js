import '../styles/main.scss'

// JS IMPORT
import smoothscroll from 'smoothscroll-polyfill'
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

document.addEventListener('DOMContentLoaded', () => {
	smoothscroll.polyfill()
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
})
