import Accordion from 'accordion-js'

export default function footerAccordion() {
	const container = document.querySelector('.footer-accordion')

	if (!container) {
		return
	}

	new Accordion(container, {
		duration: 500,
		showMultiple: false,
	})
}
