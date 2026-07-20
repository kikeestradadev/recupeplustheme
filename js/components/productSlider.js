const productSlider = (scope = document) => {
	const instances = [];
	const roots = scope.querySelectorAll('.product-slider');

	if (roots.length > 0 && typeof Swiper !== 'undefined') {
		roots.forEach((root) => {
			root.querySelectorAll('.product-slider__slider').forEach((group) => {
				const swiperEl = group.querySelector('.product-slider__swiper');
				if (!swiperEl) return;

				if (swiperEl.swiper) {
					instances.push(swiperEl.swiper);
					return;
				}

				const prevEl = group.querySelector('.product-slider__nav--prev');
				const nextEl = group.querySelector('.product-slider__nav--next');
				const paginationEl = group.querySelector('.product-slider__pagination');
				const instance = new Swiper(swiperEl, {
					slidesPerView: 'auto',
					loop: false,
					spaceBetween: 12,
					grabCursor: true,
					freeMode: true,
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
							spaceBetween: 16,
						},
						960: {
							spaceBetween: 20,
						},
					},
				});

				instances.push(instance);
			});
		});
	}

	return instances;
};

export default productSlider;
