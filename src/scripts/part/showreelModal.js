const DEFAULT_MOBILE_MAX = 768
const SLOW_CONNECTIONS = ['slow-2g', '2g', '3g']

function playQuietly(video) {
	const playPromise = video.play()

	if (playPromise !== undefined) {
		playPromise.catch(() => {})
	}
}

/**
 * Serve the 720p file below the same cutoff heroVideo.js uses (read from
 * data-mobile-max, driven by FDRY_HERO_MOBILE_MAX_PX), with save-data on, or
 * on a slow connection. navigator.connection is Chromium-only, so other
 * browsers decide on screen size alone.
 */
function pickSource(root, video) {
	const { src, srcSd } = video.dataset

	if (!srcSd || srcSd === src) {
		return src
	}

	const declared = Number.parseInt(root?.dataset.mobileMax, 10)
	const max = Number.isFinite(declared) ? declared : DEFAULT_MOBILE_MAX
	const connection = navigator.connection
	const isSmallScreen = window.matchMedia(`(max-width: ${max}px)`).matches
	const isConstrained = Boolean(
		connection && (connection.saveData || SLOW_CONNECTIONS.includes(connection.effectiveType))
	)

	return isSmallScreen || isConstrained ? srcSd : src
}

function initShowreel(button) {
	const dialog = document.getElementById(button.getAttribute('aria-controls'))

	if (!(dialog instanceof HTMLDialogElement)) {
		return
	}

	const video = dialog.querySelector('.showreel-modal__video')

	if (!(video instanceof HTMLVideoElement)) {
		return
	}

	const root = button.closest('.hero-video')
	const loop = root?.querySelector('.hero-video__media')
	const closeButton = dialog.querySelector('[data-showreel-close]')
	const errorMessage = dialog.querySelector('.showreel-modal__error')

	let resumeAt = 0
	let resumeLoop = false

	// The markup ships no src, so nothing downloads until this runs.
	const attach = (preload) => {
		video.preload = preload

		if (!video.getAttribute('src')) {
			video.src = pickSource(root, video)
		}
	}

	// Intent: fetch the index and the first bytes while the pointer travels to
	// the button, so the click starts from a warm connection. Only people about
	// to click pay for it. pointerenter also fires for touch, before the tap.
	const prime = () => attach('metadata')

	button.addEventListener('pointerenter', prime)
	button.addEventListener('focus', prime)

	button.addEventListener('click', () => {
		if (errorMessage) {
			errorMessage.hidden = true
		}

		dialog.showModal()

		if (loop instanceof HTMLVideoElement && !loop.paused) {
			loop.pause()
			resumeLoop = true
		}

		attach('auto')

		if (resumeAt > 0) {
			if (video.readyState >= HTMLMediaElement.HAVE_METADATA) {
				video.currentTime = resumeAt
			} else {
				video.addEventListener(
					'loadedmetadata',
					() => {
						video.currentTime = resumeAt
					},
					{ once: true }
				)
			}
		}

		// Must run inside the click handler: iOS only allows playback with sound
		// from within the user gesture. If it is refused anyway, the native
		// controls are there to start it.
		playQuietly(video)
	})

	closeButton?.addEventListener('click', () => {
		dialog.close()
	})

	// The dialog fills the viewport; a click that lands on it rather than on
	// its content is a click on the backdrop.
	dialog.addEventListener('click', (event) => {
		if (event.target === dialog) {
			dialog.close()
		}
	})

	// Fires for every way out, including Esc.
	dialog.addEventListener('close', () => {
		resumeAt = video.ended ? 0 : video.currentTime
		video.pause()

		// A paused video keeps buffering. Dropping the source aborts the
		// download; reopening fetches from the saved position.
		video.removeAttribute('src')
		video.load()

		if (resumeLoop && loop instanceof HTMLVideoElement) {
			playQuietly(loop)
		}

		resumeLoop = false
	})

	video.addEventListener('error', () => {
		// Ignore errors from the source being dropped on close.
		if (errorMessage && video.getAttribute('src')) {
			errorMessage.hidden = false
		}
	})
}

export default function showreelModal() {
	const buttons = document.querySelectorAll('[data-hero-showreel][aria-controls]')

	if (!buttons.length) {
		return
	}

	buttons.forEach((button) => {
		initShowreel(button)
	})
}
