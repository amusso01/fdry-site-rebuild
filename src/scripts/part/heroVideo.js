const DEFAULT_MOBILE_MAX = 768
const NEAR_VIEWPORT_MARGIN = '200px'

function prefersReducedMotion() {
	return window.matchMedia('(prefers-reduced-motion: reduce)').matches
}

/**
 * Read the cutoff from the markup so it always matches the <picture>
 * art-direction switch and the poster preload, which are both driven by
 * FDRY_HERO_MOBILE_MAX_PX in PHP.
 */
function isSmallScreen(root) {
	const declared = Number.parseInt(root.dataset.mobileMax, 10)
	const max = Number.isFinite(declared) ? declared : DEFAULT_MOBILE_MAX

	return window.matchMedia(`(max-width: ${max}px)`).matches
}

function prefersReducedData() {
	return Boolean(navigator.connection && navigator.connection.saveData)
}

function playHtml5Video(video) {
	const playPromise = video.play()

	if (playPromise !== undefined) {
		playPromise.catch(() => {})
	}
}

function pauseHtml5Video(video) {
	video.pause()
}

/**
 * Attach sources and begin loading.
 *
 * The markup ships no src and preload="none" so the browser fetches nothing
 * until this runs. WebM is offered first; the browser picks the first type
 * it supports.
 */
function attachSources(video) {
	if (video.dataset.sourcesAttached === 'true') {
		return
	}

	const candidates = [
		{ src: video.dataset.srcWebm, type: 'video/webm' },
		{ src: video.dataset.srcMp4, type: 'video/mp4' },
	]

	let attached = false

	candidates.forEach(({ src, type }) => {
		if (!src) {
			return
		}

		const source = document.createElement('source')

		source.src = src
		source.type = type
		video.appendChild(source)
		attached = true
	})

	if (!attached) {
		return
	}

	video.dataset.sourcesAttached = 'true'
	video.load()
}

function initMediaObserver(root, media, play, pause) {
	if (!('IntersectionObserver' in window)) {
		play(media)
		return
	}

	const observer = new IntersectionObserver(
		(entries) => {
			entries.forEach((entry) => {
				if (entry.isIntersecting) {
					play(entry.target)
				} else {
					pause(entry.target)
				}
			})
		},
		{
			root: null,
			threshold: 0.25,
		}
	)

	observer.observe(media)

	if (root.getBoundingClientRect().top < window.innerHeight) {
		play(media)
	}
}

/**
 * Run now for the above-the-fold hero, or once the root comes near the
 * viewport for the inline variant and [data-hero-video-lazy] roots.
 */
function onApproach(root, callback) {
	const isLazy =
		root.classList.contains('hero-video--inline') || root.hasAttribute('data-hero-video-lazy')

	if (!isLazy || !('IntersectionObserver' in window)) {
		callback()
		return
	}

	const loader = new IntersectionObserver(
		(entries, observer) => {
			entries.forEach((entry) => {
				if (!entry.isIntersecting) {
					return
				}

				observer.disconnect()
				callback()
			})
		},
		{
			root: null,
			rootMargin: NEAR_VIEWPORT_MARGIN,
		}
	)

	loader.observe(root)
}

function startWhenReady(root, video) {
	onApproach(root, () => {
		attachSources(video)

		video.addEventListener(
			'loadeddata',
			() => {
				video.classList.add('is-playing')
			},
			{ once: true }
		)

		initMediaObserver(root, video, playHtml5Video, pauseHtml5Video)
	})
}

/**
 * Reduced motion or save-data on a root with no still: load just enough to
 * paint the first frame, paused, rather than leave an empty box. iOS Safari
 * paints nothing before playback, which is why a still is recommended.
 */
function showFirstFrame(root, video) {
	onApproach(root, () => {
		video.preload = 'metadata'
		attachSources(video)
	})
}

function initAutoplayMedia(root) {
	const video = root.querySelector('.hero-video__media, [data-hero-video-media]')

	if (!(video instanceof HTMLVideoElement)) {
		return
	}

	// [data-hero-video-all-screens] roots (work image rows) play on phones too.
	if (isSmallScreen(root) && !root.hasAttribute('data-hero-video-all-screens')) {
		return
	}

	// The poster is already painted from markup. Leaving the video unloaded
	// here is the whole reduced-motion/data-saver saving: zero video bytes.
	if (prefersReducedMotion() || prefersReducedData()) {
		if (root.hasAttribute('data-hero-video-no-still')) {
			showFirstFrame(root, video)
		}

		return
	}

	startWhenReady(root, video)
}

/**
 * .hero-video (homepage hero, service showreel) and [data-hero-video] roots
 * with their own markup: the single work hero (work/hero-media.php) and image
 * rows (work/image-row.php). Opt-in attributes on the root:
 * - data-hero-video-lazy: wait until it comes near the viewport.
 * - data-hero-video-all-screens: load on small screens too.
 * - data-hero-video-no-still: with reduced motion or save-data, show the
 *   paused first frame, as there is no still to stand in.
 */
export default function heroVideo() {
	const roots = document.querySelectorAll('.hero-video, [data-hero-video]')

	if (!roots.length) {
		return
	}

	roots.forEach((root) => {
		initAutoplayMedia(root)
	})
}
