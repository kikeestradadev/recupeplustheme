const mushroomsSplitInstances = new WeakMap();

const mushroomsSplit = (scope = document) => {
	const roots = scope.querySelectorAll('.mushrooms-split');

	roots.forEach((root) => {
		if (mushroomsSplitInstances.has(root)) return;

		const swiperEl = root.querySelector('.mushrooms-split__swiper');
		const paginationEl = root.querySelector('.mushrooms-split__pagination');
		const gridQuery = window.matchMedia('(min-width: 640px)');
		const controller = new AbortController();
		const { signal } = controller;
		let swiper = null;

		const destroySwiper = () => {
			if (!swiper) return;
			swiper.destroy(true, true);
			swiper = null;
		};

		const initSwiper = () => {
			if (typeof Swiper === 'undefined' || !swiperEl || swiper) return;

			swiper = new Swiper(swiperEl, {
				slidesPerView: 1,
				spaceBetween: 16,
				grabCursor: true,
				watchOverflow: true,
				pagination: {
					el: paginationEl,
					clickable: true,
				},
			});
		};

		const updateEnhancement = () => {
			if (gridQuery.matches) {
				destroySwiper();
				return;
			}

			initSwiper();
		};

		updateEnhancement();

		if (typeof gridQuery.addEventListener === 'function') {
			gridQuery.addEventListener('change', updateEnhancement, { signal });
		} else if (typeof gridQuery.addListener === 'function') {
			const onChange = () => updateEnhancement();
			gridQuery.addListener(onChange);
			signal.addEventListener('abort', () => gridQuery.removeListener(onChange), {
				once: true,
			});
		}

		mushroomsSplitInstances.set(root, {
			destroy() {
				destroySwiper();
				controller.abort();
				mushroomsSplitInstances.delete(root);
			},
		});
	});
};

export default mushroomsSplit;
