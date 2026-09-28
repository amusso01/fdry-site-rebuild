// "Join our newsletter" in the site footer opens the Klaviyo sign-up form.
// Klaviyo's onsite script reads commands queued on window._klOnsite, so the
// click works whether or not Klaviyo has finished loading.

const KLAVIYO_FORM_ID = 'RdsGcP'

export default function newsletterForm() {
	const button = document.querySelector('.site-footer__newsletter')

	if (!button) {
		return
	}

	button.addEventListener('click', () => {
		window._klOnsite = window._klOnsite || []
		window._klOnsite.push(['openForm', KLAVIYO_FORM_ID])
	})
}
