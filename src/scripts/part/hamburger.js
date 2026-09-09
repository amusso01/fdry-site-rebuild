export default function hamburger() {
	const burger = document.getElementById('hamburger')
	const overlay = document.getElementById('site-nav-overlay')
	const backdrop = document.getElementById('site-nav-backdrop')

	if (!burger) {
		return
	}

	const htmlElement = document.documentElement
	const menuLabel = burger.getAttribute('aria-label') || 'Menu'
	const closeLabel = 'Close menu'
	const overlayDurationMs = 350
	let closeTimeoutId = 0

	const prefersReducedMotion = () =>
		window.matchMedia('(prefers-reduced-motion: reduce)').matches

	const clearCloseTimeout = () => {
		if (closeTimeoutId) {
			window.clearTimeout(closeTimeoutId)
			closeTimeoutId = 0
		}

		htmlElement.classList.remove('menu-closing')
	}

	const dispatchNavToggle = (isOpen) => {
		document.dispatchEvent(
			new CustomEvent('fdry:nav-toggle', {
				detail: { isOpen },
			})
		)
	}

	const applyClosedChrome = () => {
		burger.classList.remove('is-active')
		burger.setAttribute('aria-expanded', 'false')
		burger.setAttribute('aria-label', menuLabel)

		if (overlay) {
			overlay.setAttribute('aria-hidden', 'true')
			overlay.setAttribute('inert', '')
		}
	}

	const finishClose = () => {
		closeTimeoutId = 0
		htmlElement.classList.remove('menu-open', 'noscroll', 'menu-closing')

		if (backdrop) {
			backdrop.setAttribute('hidden', '')
		}
	}

	const setMenuOpen = (isOpen) => {
		if (!isOpen && htmlElement.classList.contains('menu-closing')) {
			return
		}

		clearCloseTimeout()

		if (!isOpen) {
			if (!htmlElement.classList.contains('menu-open')) {
				return
			}

			applyClosedChrome()
			dispatchNavToggle(false)

			if (prefersReducedMotion()) {
				finishClose()
				return
			}

			htmlElement.classList.add('menu-closing')
			closeTimeoutId = window.setTimeout(finishClose, overlayDurationMs)
			return
		}

		burger.classList.add('is-active')
		burger.setAttribute('aria-expanded', 'true')
		burger.setAttribute('aria-label', closeLabel)
		htmlElement.classList.add('menu-open', 'noscroll')

		if (backdrop) {
			backdrop.removeAttribute('hidden')
		}

		if (overlay) {
			overlay.setAttribute('aria-hidden', 'false')
			overlay.removeAttribute('inert')
		}

		dispatchNavToggle(true)
	}

	burger.addEventListener('click', () => {
		setMenuOpen(!burger.classList.contains('is-active'))
	})

	if (backdrop) {
		backdrop.addEventListener('click', () => {
			setMenuOpen(false)
		})
	}

	document.addEventListener('keydown', (event) => {
		if (event.key === 'Escape' && burger.classList.contains('is-active')) {
			setMenuOpen(false)
			burger.focus()
		}
	})
}
