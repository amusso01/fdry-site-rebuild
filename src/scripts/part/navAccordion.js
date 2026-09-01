import Accordion from 'accordion-js'

export default function navAccordion() {
	const container = document.querySelector(
		'.site-nav-mobile .accordion-container'
	)

	if (!container) {
		return
	}

	new Accordion(container, {
		duration: 300,
		showMultiple: false,
	})
}
