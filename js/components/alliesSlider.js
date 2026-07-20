const alliesSlider = (scope = document) => {
	const instances = [];
	const roots = scope.querySelectorAll('.allies-slider');

	if (roots.length > 0 && typeof Swiper !== 'undefined') {
		roots.forEach((root) => {
			const swiperEl = root.querySelector('.allies-slider__swiper');
			if (!swiperEl) return;

			if (swiperEl.swiper) {
				instances.push(swiperEl.swiper);
				return;
			}

			const paginationEl = root.querySelector('.allies-slider__pagination');
			const instance = new Swiper(swiperEl, {
				slidesPerView: 'auto',
				loop: true,
				spaceBetween: 12,
				grabCursor: true,
				autoplay: {
					delay: 2500,
					disableOnInteraction: false,
					pauseOnMouseEnter: true,
				},
				pagination: {
					el: paginationEl,
					clickable: true,
				},
				breakpoints: {
					640: {
						spaceBetween: 16,
					},
					960: {
						spaceBetween: 20,
					},
				},
			});

			instances.push(instance);
		});
	}

	return instances;
};

export default alliesSlider;
