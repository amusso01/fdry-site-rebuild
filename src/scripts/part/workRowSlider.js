import Swiper from 'swiper'
import { Pagination } from 'swiper/modules'

const TABLET_MQ = '(max-width: 1139px)'

export default function workRowSlider() {
	const sliders = document.querySelectorAll('.work-row__slider')

	if (!sliders.length) {
		return
	}

	const instances = new Map()
	const mq = window.matchMedia(TABLET_MQ)

	const init = (el) => {
		if (instances.has(el)) {
			return
		}

		const pagination = el.querySelector('.work-row__pagination')

		const swiper = new Swiper(el, {
			modules: [Pagination],
			slidesPerView: 1,
			spaceBetween: 0,
			pagination: pagination
				? {
						el: pagination,
						clickable: true,
					}
				: false,
		})

		instances.set(el, swiper)
	}

	const destroy = (el) => {
		const swiper = instances.get(el)

		if (!swiper) {
			return
		}

		swiper.destroy(true, true)
		instances.delete(el)
	}

	const sync = () => {
		sliders.forEach((el) => {
			if (mq.matches) {
				init(el)
			} else {
				destroy(el)
			}
		})
	}

	sync()
	mq.addEventListener('change', sync)
}
