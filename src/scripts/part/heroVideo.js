const MOBILE_QUERY = '(max-width: 768px)'
const NEAR_VIEWPORT_MARGIN = '200px'

function prefersReducedMotion() {
	return window.matchMedia('(prefers-reduced-motion: reduce)').matches
}

function isSmallScreen() {
	return window.matchMedia(MOBILE_QUERY).matches
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

/** Load now for the above-the-fold hero, or on approach for the inline variant. */
function startWhenReady(root, video) {
	const begin = () => {
		attachSources(video)

		video.addEventListener(
			'loadeddata',
			() => {
				video.classList.add('is-playing')
			},
			{ once: true }
		)

		initMediaObserver(root, video, playHtml5Video, pauseHtml5Video)
	}

	const isInline = root.classList.contains('hero-video--inline')

	if (!isInline || !('IntersectionObserver' in window)) {
		begin()
		return
	}

	const loader = new IntersectionObserver(
		(entries, observer) => {
			entries.forEach((entry) => {
				if (!entry.isIntersecting) {
					return
				}

				observer.disconnect()
				begin()
			})
		},
		{
			root: null,
			rootMargin: NEAR_VIEWPORT_MARGIN,
		}
	)

	loader.observe(root)
}

function initAutoplayMedia(root) {
	const video = root.querySelector('.hero-video__media')

	if (!(video instanceof HTMLVideoElement)) {
		return
	}

	// The poster is already painted from markup. Leaving the video unloaded
	// here is the whole mobile/reduced-motion saving: zero video bytes.
	if (isSmallScreen() || prefersReducedMotion() || prefersReducedData()) {
		return
	}

	startWhenReady(root, video)
}

export default function heroVideo() {
	const roots = document.querySelectorAll('.hero-video')

	if (!roots.length) {
		return
	}

	roots.forEach((root) => {
		initAutoplayMedia(root)
	})
}
