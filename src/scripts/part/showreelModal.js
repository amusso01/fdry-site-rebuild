import gsap from 'gsap'

const DEFAULT_MOBILE_MAX = 768
const SLOW_CONNECTIONS = ['slow-2g', '2g', '3g']

// The fully open overlay. Same number count as buttonShape(), so GSAP can
// tween between the two strings.
const FULL_SHAPE = 'inset(0px 0px 0px 0px round 0px)'

// Seconds the reel sits fully visible before it starts.
const PLAY_DELAY = 0.1

function prefersReducedMotion() {
	return window.matchMedia('(prefers-reduced-motion: reduce)').matches
}

// smoothScroll.js stops Lenis while the reel is open; overflow: hidden alone
// does not hold the page still.
function dispatchShowreelToggle(isOpen) {
	document.dispatchEvent(
		new CustomEvent('fdry:showreel-toggle', {
			detail: { isOpen },
		})
	)
}

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

/**
 * The button's pill as a clip-path on the dialog, so the overlay can grow out
 * of it. Measured against the dialog rather than the window, so a scrollbar
 * removed by the scroll lock does not throw it off.
 */
function buttonShape(button, dialog) {
	const shape = button.getBoundingClientRect()
	const frame = dialog.getBoundingClientRect()

	const top = shape.top - frame.top
	const right = frame.right - shape.right
	const bottom = frame.bottom - shape.bottom
	const left = shape.left - frame.left

	return `inset(${top}px ${right}px ${bottom}px ${left}px round ${shape.height / 2}px)`
}

function initShowreel(button) {
	const dialog = document.getElementById(button.getAttribute('aria-controls'))

	if (!(dialog instanceof HTMLDialogElement)) {
		return
	}

	const inner = dialog.querySelector('.showreel-modal__inner')
	const video = dialog.querySelector('.showreel-modal__video')

	if (!inner || !(video instanceof HTMLVideoElement)) {
		return
	}

	const root = button.closest('.hero-video')
	const loop = root?.querySelector('.hero-video__media')
	const closeButton = dialog.querySelector('[data-showreel-close]')
	const errorMessage = dialog.querySelector('.showreel-modal__error')

	let resumeLoop = false
	let timeline = null
	let playTimer = null

	const pauseLoop = () => {
		if (loop instanceof HTMLVideoElement && !loop.paused) {
			loop.pause()
			resumeLoop = true
		}
	}

	const resumeLoopIfPaused = () => {
		if (resumeLoop && loop instanceof HTMLVideoElement) {
			playQuietly(loop)
		}

		resumeLoop = false
	}

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
		dispatchShowreelToggle(true)
		attach('auto')

		// Must run inside the click handler: iOS only allows playback with sound
		// from within the user gesture. If it is refused anyway, the native
		// controls are there to start it.
		playQuietly(video)

		if (prefersReducedMotion()) {
			pauseLoop()
			return
		}

		// The reel waits for the overlay to finish growing. Pausing straight away
		// keeps the element unlocked for that later play(): WebKit lifts the
		// gesture restriction on the play() call itself. The download carries
		// on meanwhile, so the animation doubles as buffering time.
		video.pause()

		// Built after showModal(), so the shape is read once the scroll lock has
		// applied. The hero loop keeps playing until the overlay covers it.
		timeline = gsap
			.timeline({ onReverseComplete: () => dialog.close() })
			.fromTo(
				dialog,
				{ clipPath: buttonShape(button, dialog) },
				{ clipPath: FULL_SHAPE, duration: 0.6, ease: 'power3.inOut', onComplete: pauseLoop }
			)
			// Opacity, not autoAlpha: visibility: hidden would take focus off the
			// close button that showModal() just focused.
			.fromTo(
				inner,
				{ opacity: 0, y: 12 },
				{
					opacity: 1,
					y: 0,
					duration: 0.4,
					ease: 'power2.out',
					// A delayed call rather than a step in the timeline, so
					// reversing on close does not first sit through the wait.
					onComplete: () => {
						playTimer = gsap.delayedCall(PLAY_DELAY, () => playQuietly(video))
					},
				},
				'-=0.1'
			)
	})

	// The close button, the backdrop and Esc come through here: the reel fades
	// out and the overlay collapses back into the button. With reduced motion
	// there is no timeline and it closes at once.
	const closeModal = () => {
		playTimer?.kill()
		video.pause()

		if (!timeline) {
			dialog.close()
			return
		}

		resumeLoopIfPaused()
		timeline.timeScale(1.5).reverse()
	}

	closeButton?.addEventListener('click', closeModal)

	// The dialog fills the viewport; a click that lands on it rather than on
	// its content is a click on the backdrop.
	dialog.addEventListener('click', (event) => {
		if (event.target === dialog) {
			closeModal()
		}
	})

	// Esc. Chrome only lets this be cancelled after a fresh user activation;
	// when it can't be, the dialog closes at once and skips the collapse.
	dialog.addEventListener('cancel', (event) => {
		if (timeline) {
			event.preventDefault()
			closeModal()
		}
	})

	// Fires for every way out, including Esc.
	dialog.addEventListener('close', () => {
		timeline?.kill()
		timeline = null
		playTimer?.kill()
		playTimer = null
		gsap.set([dialog, inner], { clearProps: 'clipPath,opacity,transform' })

		video.pause()

		// A paused video keeps buffering. Dropping the source aborts the
		// download, and reopening starts the reel from the beginning.
		video.removeAttribute('src')
		video.load()

		resumeLoopIfPaused()
		dispatchShowreelToggle(false)
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
