import gsap from 'gsap'

export function initParallax() {
	gsap.utils.toArray('[data-parallax]').forEach((el) => {
		const value = parseFloat(el.dataset.parallax)

		if (!value) return

		gsap.fromTo(
			el,
			{ yPercent: value * 100 },
			{
				yPercent: -(value * 100),
				ease: 'none',
				scrollTrigger: {
					trigger: el,
					start: 'top bottom',
					end: 'bottom top',
					scrub: true,
				},
			},
		)
	})
}
