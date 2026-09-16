export default function workArchive() {
	const root = document.querySelector('.work-archive')
	const config = typeof window.fdryWorkArchive === 'object' ? window.fdryWorkArchive : null

	if (!root || !config) {
		return
	}

	const { ajaxUrl, nonce } = config

	if (!ajaxUrl || !nonce) {
		return
	}

	const grid = root.querySelector('.work-archive__grid')
	const moreWrap = root.querySelector('.work-archive__more')
	const loadMore = root.querySelector('.work-archive__load-more')
	const filters = root.querySelectorAll('.work-archive__filter')

	if (!grid || !moreWrap || !loadMore) {
		return
	}

	let isLoading = false

	const setLoading = (next) => {
		isLoading = next
		root.classList.toggle('is-loading', next)
		root.setAttribute('aria-busy', next ? 'true' : 'false')
		loadMore.disabled = next
		filters.forEach((filter) => {
			filter.disabled = next
		})
		loadMore.textContent = next ? 'Loading...' : 'Load more'
	}

	const setActiveFilter = (category) => {
		filters.forEach((filter) => {
			const isActive = filter.dataset.category === category
			filter.classList.toggle('is-active', isActive)
			filter.setAttribute('aria-pressed', isActive ? 'true' : 'false')
		})
	}

	const setHasMore = (hasMore) => {
		if (hasMore) {
			moreWrap.removeAttribute('hidden')
		} else {
			moreWrap.setAttribute('hidden', '')
		}
	}

	const requestWorks = async (category, page) => {
		const body = new FormData()
		body.append('action', 'fdry_load_more_works')
		body.append('nonce', nonce)
		body.append('category', category)
		body.append('page', String(page))

		const response = await fetch(ajaxUrl, {
			method: 'POST',
			credentials: 'same-origin',
			body,
		})

		if (!response.ok) {
			throw new Error('Work archive request failed')
		}

		const json = await response.json()

		if (!json || json.success !== true || !json.data) {
			throw new Error('Work archive response invalid')
		}

		return json.data
	}

	const loadCategory = async (category) => {
		if (isLoading) {
			return
		}

		setLoading(true)
		setActiveFilter(category)
		root.dataset.category = category
		root.dataset.page = '1'

		try {
			const data = await requestWorks(category, 1)
			grid.innerHTML = data.html || ''
			setHasMore(Boolean(data.has_more))
		} catch (error) {
			setHasMore(false)
		} finally {
			setLoading(false)
		}
	}

	const loadNextPage = async () => {
		if (isLoading) {
			return
		}

		const category = root.dataset.category || 'all'
		const nextPage = (parseInt(root.dataset.page, 10) || 1) + 1

		setLoading(true)

		try {
			const data = await requestWorks(category, nextPage)
			if (data.html) {
				grid.insertAdjacentHTML('beforeend', data.html)
			}
			root.dataset.page = String(nextPage)
			setHasMore(Boolean(data.has_more))
		} catch (error) {
			setHasMore(false)
		} finally {
			setLoading(false)
		}
	}

	filters.forEach((filter) => {
		filter.addEventListener('click', (event) => {
			event.preventDefault()

			const category = filter.dataset.category || 'all'

			if (category === (root.dataset.category || 'all') && filter.classList.contains('is-active')) {
				return
			}

			loadCategory(category)
		})
	})

	loadMore.addEventListener('click', (event) => {
		event.preventDefault()
		loadNextPage()
	})
}
