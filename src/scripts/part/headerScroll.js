const HIDE_AFTER_PX = 300
const SHOW_AFTER_UP_PX = 200
const SCROLL_THROTTLE_MS = 100

function throttle(fn, wait) {
	let timeoutId = 0
	let lastRun = 0

	return function throttled(...args) {
		const now = Date.now()

		if (now - lastRun >= wait) {
			lastRun = now
			fn.apply(this, args)
			return
		}

		if (timeoutId) {
			window.clearTimeout(timeoutId)
		}

		timeoutId = window.setTimeout(() => {
			lastRun = Date.now()
			timeoutId = 0
			fn.apply(this, args)
		}, wait - (now - lastRun))
	}
}

export default function headerScroll() {
	const header = document.querySelector('.site-header')

	if (!header) {
		return
	}

	const htmlElement = document.documentElement
	let lastScrollY = window.scrollY
	let scrollUpDistance = 0
	let isHidden = false

	const isMenuOpen = () =>
		htmlElement.classList.contains('menu-open') ||
		htmlElement.classList.contains('menu-closing')

	const setHidden = (hidden) => {
		if (isHidden === hidden) {
			return
		}

		isHidden = hidden
		header.classList.toggle('is-hidden', hidden)
	}

	const showHeader = () => {
		scrollUpDistance = 0
		setHidden(false)
	}

	const onScroll = () => {
		const currentScrollY = window.scrollY

		if (isMenuOpen()) {
			showHeader()
			lastScrollY = currentScrollY
			return
		}

		if (currentScrollY < HIDE_AFTER_PX) {
			showHeader()
			lastScrollY = currentScrollY
			return
		}

		if (currentScrollY > lastScrollY) {
			scrollUpDistance = 0
			setHidden(true)
		} else if (currentScrollY < lastScrollY && isHidden) {
			scrollUpDistance += lastScrollY - currentScrollY

			if (scrollUpDistance >= SHOW_AFTER_UP_PX) {
				showHeader()
			}
		}

		lastScrollY = currentScrollY
	}

	document.addEventListener('fdry:nav-toggle', (event) => {
		if (event.detail?.isOpen) {
			showHeader()
		}
	})

	window.addEventListener('scroll', throttle(onScroll, SCROLL_THROTTLE_MS), {
		passive: true,
	})

	onScroll()
}
