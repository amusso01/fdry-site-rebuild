const VIMEO_ORIGIN = 'https://player.vimeo.com'

function prefersReducedMotion() {
	return window.matchMedia('(prefers-reduced-motion: reduce)').matches
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

function vimeoPostMessage(iframe, method) {
	if (!(iframe instanceof HTMLIFrameElement) || !iframe.contentWindow) {
		return
	}

	iframe.contentWindow.postMessage(JSON.stringify({ method }), VIMEO_ORIGIN)
}

function playVimeo(iframe) {
	vimeoPostMessage(iframe, 'play')
}

function pauseVimeo(iframe) {
	vimeoPostMessage(iframe, 'pause')
}

function mountVimeoIframe(mount) {
	const src = mount.dataset.vimeoSrc

	if (!src) {
		return null
	}

	const existing = mount.querySelector('.hero-video__media--vimeo')

	if (existing instanceof HTMLIFrameElement) {
		return existing
	}

	const iframe = document.createElement('iframe')

	iframe.className = 'hero-video__media hero-video__media--vimeo'
	iframe.src = src
	iframe.title = ''
	iframe.tabIndex = -1
	iframe.setAttribute('allow', 'autoplay; fullscreen; picture-in-picture')
	iframe.setAttribute('aria-hidden', 'true')

	mount.appendChild(iframe)

	return iframe
}

function initMediaObserver(root, media, play, pause) {
	if (prefersReducedMotion()) {
		pause(media)
		return
	}

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

function initAutoplayMedia(root) {
	const vimeoMount = root.querySelector('[data-vimeo-src]')

	if (vimeoMount) {
		const iframe = mountVimeoIframe(vimeoMount)

		if (iframe) {
			initMediaObserver(root, iframe, playVimeo, pauseVimeo)
		}

		return
	}

	const media = root.querySelector('.hero-video__media')

	if (!media) {
		return
	}

	if (media.classList.contains('hero-video__media--vimeo')) {
		initMediaObserver(root, media, playVimeo, pauseVimeo)
		return
	}

	if (media instanceof HTMLVideoElement) {
		initMediaObserver(root, media, playHtml5Video, pauseHtml5Video)
	}
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
