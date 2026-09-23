import Swiper from 'swiper'
import { Navigation } from 'swiper/modules'

export default function teamSlider() {
	const sliders = document.querySelectorAll('.team__slider')

	if (!sliders.length) {
		return
	}

	sliders.forEach((el) => {
		const prevEl = el.querySelector('.team__nav--prev')
		const nextEl = el.querySelector('.team__nav--next')

		new Swiper(el, {
			modules: [Navigation],
			grabCursor: true,
			spaceBetween: 12,
			slidesPerView: 1.15,
			breakpoints: {
				640: {
					slidesPerView: 2,
				},
				1140: {
					slidesPerView: 3,
				},
			},
			navigation:
				prevEl && nextEl
					? {
							prevEl,
							nextEl,
						}
					: false,
		})
	})
}
