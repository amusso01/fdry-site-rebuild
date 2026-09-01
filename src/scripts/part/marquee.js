export default function marquee() {
	const roots = document.querySelectorAll('.marquee')

	if (!roots.length) {
		return
	}

	roots.forEach((root) => {
		const track = root.querySelector('.marquee__track')
		const source = track?.querySelector('.marquee__list')

		if (!track || !source || !source.children.length) {
			return
		}

		;[...track.querySelectorAll('.marquee__list')].slice(1).forEach((list) => {
			list.remove()
		})

		let safety = 0
		while (source.scrollWidth < root.clientWidth && safety < 10) {
			;[...source.children].forEach((item) => {
				const clone = item.cloneNode(true)
				clone.setAttribute('aria-hidden', 'true')
				source.appendChild(clone)
			})
			safety += 1
		}

		const duplicate = source.cloneNode(true)
		duplicate.setAttribute('aria-hidden', 'true')
		track.appendChild(duplicate)
	})
}
