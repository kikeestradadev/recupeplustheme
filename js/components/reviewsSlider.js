const reviewsSlider = (scope = document) => {
	const instances = [];
	const roots = scope.querySelectorAll('.reviews-slider');

	if (roots.length > 0 && typeof Swiper !== 'undefined') {
		roots.forEach((root) => {
			const swiperEl = root.querySelector('.reviews-slider__swiper');
			if (!swiperEl) return;

			if (swiperEl.swiper) {
				instances.push(swiperEl.swiper);
				return;
			}

			const prevEl = root.querySelector('.reviews-slider__nav--prev');
			const nextEl = root.querySelector('.reviews-slider__nav--next');
			const paginationEl = root.querySelector('.reviews-slider__pagination');
			const instance = new Swiper(swiperEl, {
				slidesPerView: 'auto',
				loop: false,
				spaceBetween: 16,
				grabCursor: true,
				pagination: {
					el: paginationEl,
					clickable: true,
				},
				navigation: {
					prevEl,
					nextEl,
				},
				breakpoints: {
					640: {
						spaceBetween: 20,
					},
					960: {
						spaceBetween: 24,
					},
				},
			});

			instances.push(instance);
		});
	}

	return instances;
};

export default reviewsSlider;
