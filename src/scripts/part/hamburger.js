export default function hamburger() {
	const burger = document.getElementById('hamburger')

	if (!burger) {
		return
	}

	const htmlElement = document.documentElement

	burger.addEventListener('click', () => {
		const isOpen = burger.classList.toggle('is-active')
		burger.setAttribute('aria-expanded', isOpen ? 'true' : 'false')
		htmlElement.classList.toggle('menu-open', isOpen)
	})
}
