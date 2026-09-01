import gsap from 'gsap'
import { ScrollTrigger } from 'gsap/ScrollTrigger'

const DEFAULT_START = 'top 85%'
const DEFAULT_DURATION = 0.65

const FROM = {
	'fade-up': { autoAlpha: 0, y: 40 },
	'fade-in': { autoAlpha: 0 },
	'fade-left': { autoAlpha: 0, x: 40 },
	'fade-right': { autoAlpha: 0, x: -40 },
}

function revealVars(el) {
	const type = el.dataset.reveal || 'fade-up'
	const from = FROM[type] || FROM['fade-up']

	return {
		from,
		delay: parseFloat(el.dataset.revealDelay) || 0,
		duration: parseFloat(el.dataset.revealDuration) || DEFAULT_DURATION,
		start: el.dataset.revealStart || DEFAULT_START,
	}
}

function playReveal(el) {
	const { from, delay, duration } = revealVars(el)

	gsap.fromTo(
		el,
		{ ...from },
		{
			autoAlpha: 1,
			x: 0,
			y: 0,
			duration,
			delay,
			ease: 'power2.out',
			overwrite: true,
		},
	)
}

function initStagger(el) {
	const { delay, duration, start } = revealVars(el)
	const stagger = parseFloat(el.dataset.revealStagger) || 0.08
	const children = el.children

	if (!children.length) return

	gsap.fromTo(
		children,
		{ autoAlpha: 0, y: 40 },
		{
			autoAlpha: 1,
			y: 0,
			duration,
			delay,
			stagger,
			ease: 'power2.out',
			scrollTrigger: {
				trigger: el,
				start,
				once: true,
			},
		},
	)
}

export function initReveal() {
	const els = gsap.utils.toArray('[data-reveal]')
	if (!els.length) return

	const batchTargets = []
	const customStart = []
	const staggerTargets = []

	els.forEach((el) => {
		if (el.dataset.reveal === 'stagger-up') {
			staggerTargets.push(el)
		} else if (el.dataset.revealStart) {
			customStart.push(el)
		} else {
			batchTargets.push(el)
		}
	})

	if (batchTargets.length) {
		ScrollTrigger.batch(batchTargets, {
			start: DEFAULT_START,
			once: true,
			onEnter: (batch) => {
				batch.forEach(playReveal)
			},
		})
	}

	customStart.forEach((el) => {
		ScrollTrigger.create({
			trigger: el,
			start: revealVars(el).start,
			once: true,
			onEnter: () => playReveal(el),
		})
	})

	staggerTargets.forEach(initStagger)
}
