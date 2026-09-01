export default function hamburger() {
	const burger = document.getElementById('hamburger')
	const overlay = document.getElementById('site-nav-overlay')

	if (!burger) {
		return
	}

	const htmlElement = document.documentElement
	const menuLabel = burger.getAttribute('aria-label') || 'Menu'
	const closeLabel = 'Close menu'

	const setMenuOpen = (isOpen) => {
		burger.classList.toggle('is-active', isOpen)
		burger.setAttribute('aria-expanded', isOpen ? 'true' : 'false')
		burger.setAttribute('aria-label', isOpen ? closeLabel : menuLabel)
		htmlElement.classList.toggle('menu-open', isOpen)
		htmlElement.classList.toggle('noscroll', isOpen)

		if (overlay) {
			overlay.setAttribute('aria-hidden', isOpen ? 'false' : 'true')

			if (isOpen) {
				overlay.removeAttribute('inert')
			} else {
				overlay.setAttribute('inert', '')
			}
		}

		document.dispatchEvent(
			new CustomEvent('fdry:nav-toggle', {
				detail: { isOpen },
			})
		)
	}

	burger.addEventListener('click', () => {
		setMenuOpen(!burger.classList.contains('is-active'))
	})

	document.addEventListener('keydown', (event) => {
		if (event.key === 'Escape' && burger.classList.contains('is-active')) {
			setMenuOpen(false)
			burger.focus()
		}
	})
}
