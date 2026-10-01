export default function navMenu() {
	const overlay = document.getElementById('site-nav-overlay')

	if (!overlay) {
		return
	}

	const parentTriggers = overlay.querySelectorAll('[data-nav-parent]')
	const childlessLinks = overlay.querySelectorAll(
		'.site-nav-overlay__parent--link:not([data-nav-parent])'
	)
	const panels = overlay.querySelectorAll('[data-nav-panel]')

	const pausePanelVideos = (panel) => {
		panel.querySelectorAll('video').forEach((video) => {
			video.pause()
		})
	}

	// The videos ship with data-src only (secondary.php), so a closed menu
	// downloads nothing. The file is attached the first time its panel is
	// shown. Below the tablet breakpoint the desktop panels are display: none,
	// so the video has no box and phones never fetch it.
	const playPanelVideos = (panel) => {
		panel.querySelectorAll('video').forEach((video) => {
			if (!video.getClientRects().length) {
				return
			}

			if (!video.getAttribute('src') && video.dataset.src) {
				video.src = video.dataset.src
			}

			const playPromise = video.play()

			if (playPromise !== undefined) {
				playPromise.catch(() => {})
			}
		})
	}

	const pauseAllPanelVideos = () => {
		panels.forEach((panel) => {
			pausePanelVideos(panel)
		})
	}

	const playActivePanelVideo = () => {
		const activePanel = overlay.querySelector(
			'.site-nav-overlay__panel.is-active:not([hidden])'
		)

		if (activePanel) {
			playPanelVideos(activePanel)
		}
	}

	const activatePanel = (parentId) => {
		parentTriggers.forEach((trigger) => {
			const isActive = trigger.dataset.navParent === parentId

			trigger.classList.toggle('is-active', isActive)
		})

		pauseAllPanelVideos()

		panels.forEach((panel) => {
			const isActive = panel.dataset.navPanel === parentId

			panel.classList.toggle('is-active', isActive)

			if (isActive) {
				panel.removeAttribute('hidden')
				playPanelVideos(panel)
			} else {
				panel.setAttribute('hidden', '')
			}
		})
	}

	const deactivatePanels = () => {
		parentTriggers.forEach((trigger) => {
			trigger.classList.remove('is-active')
		})

		panels.forEach((panel) => {
			panel.classList.remove('is-active')
			panel.setAttribute('hidden', '')
		})

		pauseAllPanelVideos()
	}

	document.addEventListener('fdry:nav-toggle', (event) => {
		if (event.detail?.isOpen) {
			playActivePanelVideo()
		} else {
			pauseAllPanelVideos()
		}
	})

	if (!parentTriggers.length || !panels.length) {
		return
	}

	parentTriggers.forEach((trigger) => {
		trigger.addEventListener('mouseenter', () => {
			activatePanel(trigger.dataset.navParent)
		})

		trigger.addEventListener('focus', () => {
			activatePanel(trigger.dataset.navParent)
		})
	})

	childlessLinks.forEach((link) => {
		link.addEventListener('mouseenter', deactivatePanels)
		link.addEventListener('focus', deactivatePanels)
	})
}
