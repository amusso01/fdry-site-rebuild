import gsap from 'gsap'

const DEFAULT_DISTANCE = 50
const BASE_DURATION = 2
const START = 'top 90%'

// Children of [data-fade-up-group] that fade up on their own.
const GROUP_CHILDREN = 'p, h1, h2, h3, h4, h5, h6, ul, ol, img, figure, blockquote, hr'

function prepareGroups() {
	document.querySelectorAll('[data-fade-up-group]').forEach((group) => {
		const stagger = parseFloat(group.dataset.fadeUpGroup) || 0

		Array.from(group.children)
			.filter((child) => child.matches(GROUP_CHILDREN) && !child.hasAttribute('data-fade-up'))
			.forEach((child, index) => {
				child.setAttribute('data-fade-up', '')

				if (stagger) {
					child.dataset.fadeUpDelay = (index * stagger).toFixed(2)
				}
			})
	})
}

// key is the dataset prefix ('fadeUp' / 'fadeDown'); sign 1 starts below, -1 above.
function fade(el, key, sign) {
	const data = el.dataset
	const distance = parseFloat(data[`${key}Distance`]) || DEFAULT_DISTANCE

	gsap.fromTo(
		el,
		{ y: sign * distance, opacity: 0 },
		{
			y: 0,
			opacity: 1,
			// As on lionandmason: the attribute adds to the base, so ".2" is 2.2s.
			duration: BASE_DURATION + (parseFloat(data[`${key}Duration`]) || 0),
			delay: parseFloat(data[`${key}Delay`]) || 0,
			ease: 'power3.out',
			// translate(0, 0) looks like no transform, but left inline it would
			// override hover transforms and trap position: fixed children.
			clearProps: 'transform',
			scrollTrigger: {
				trigger: el,
				start: START,
				once: true,
			},
		},
	)
}

export function initFade() {
	prepareGroups()

	gsap.utils.toArray('[data-fade-up]').forEach((el) => fade(el, 'fadeUp', 1))
	gsap.utils.toArray('[data-fade-down]').forEach((el) => fade(el, 'fadeDown', -1))
}
