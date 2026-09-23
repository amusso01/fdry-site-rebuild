import gsap from 'gsap'

const DEFAULT_DISTANCE = 50
const BASE_DURATION = 2

// Pulls the viewport's bottom edge up by 10%, so a fade starts once the
// element's top passes 90% of the viewport height (ScrollTrigger's 'top 90%').
const ROOT_MARGIN = '0px 0px -10% 0px'

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
// Paused, the tween still renders its start state straight away, so nothing
// flashes before the element is reached.
function fade(el, key, sign) {
	const data = el.dataset
	const distance = parseFloat(data[`${key}Distance`]) || DEFAULT_DISTANCE

	return gsap.fromTo(
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
			// Opacity stays inline at 1, or the CSS hide rule would cover it again.
			clearProps: 'transform',
			paused: true,
		},
	)
}

export function initFade() {
	const root = document.documentElement

	// fdry_fade_gate() sets this class from <head>, and removes it on load if
	// this never ran. Without it the content is already showing: leave it be.
	if (!root.classList.contains('fdry-fade')) return

	prepareGroups()

	const tweens = new Map()

	// Not ScrollTrigger: its stored positions can go stale, or stop updating,
	// and an element left at its start state is invisible. The browser checks
	// the element's real box on every scroll and reflow, whatever scrolls it.
	const observer = new IntersectionObserver(
		(entries) => {
			entries.forEach((entry) => {
				// Already above the viewport counts too, e.g. after a reload lower down.
				if (!entry.isIntersecting && entry.boundingClientRect.top > 0) return

				observer.unobserve(entry.target)
				tweens.get(entry.target)?.play()
				tweens.delete(entry.target)
			})
		},
		{ rootMargin: ROOT_MARGIN },
	)

	const watch = (el, key, sign) => {
		tweens.set(el, fade(el, key, sign))
		observer.observe(el)
	}

	gsap.utils.toArray('[data-fade-up]').forEach((el) => watch(el, 'fadeUp', 1))
	gsap.utils.toArray('[data-fade-down]').forEach((el) => watch(el, 'fadeDown', -1))

	root.classList.add('fdry-fade-ready')
}
