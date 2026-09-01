function prefersReducedMotion() {
	return window.matchMedia('(prefers-reduced-motion: reduce)').matches
}

function playVideo(video) {
	const playPromise = video.play()

	if (playPromise !== undefined) {
		playPromise.catch(() => {})
	}
}

function pauseVideo(video) {
	video.pause()
}

function initAutoplayVideo(root) {
	const video = root.querySelector('.hero-video__media')

	if (!(video instanceof HTMLVideoElement)) {
		return
	}

	if (prefersReducedMotion()) {
		pauseVideo(video)
		return
	}

	if (!('IntersectionObserver' in window)) {
		playVideo(video)
		return
	}

	const observer = new IntersectionObserver(
		(entries) => {
			entries.forEach((entry) => {
				if (!(entry.target instanceof HTMLVideoElement)) {
					return
				}

				if (entry.isIntersecting) {
					playVideo(entry.target)
				} else {
					pauseVideo(entry.target)
				}
			})
		},
		{
			root: null,
			threshold: 0.25,
		}
	)

	observer.observe(video)

	if (root.getBoundingClientRect().top < window.innerHeight) {
		playVideo(video)
	}
}

export default function heroVideo() {
	const roots = document.querySelectorAll('.hero-video')

	if (!roots.length) {
		return
	}

	roots.forEach((root) => {
		initAutoplayVideo(root)
	})
}
