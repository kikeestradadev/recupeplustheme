import { swiperRoots } from './swiperRoots';
import { markExistingSlider, withSliderLoader } from './sliderLoader';

const guideSlider = (scope = document) => {
	const instances = [];
	const roots = swiperRoots(scope, '.guide-slider');

	if (roots.length > 0 && typeof Swiper !== 'undefined') {
		roots.forEach((root) => {
			const swiperEl = root.querySelector('.guide-slider__swiper');
			if (!swiperEl) return;

			if (swiperEl.swiper) {
				markExistingSlider(root);
				instances.push(swiperEl.swiper);
				return;
			}

			const prevEl = root.querySelector('.guide-slider__nav--prev');
			const nextEl = root.querySelector('.guide-slider__nav--next');
			const paginationEl = root.querySelector('.guide-slider__pagination');
			const instance = new Swiper(
				swiperEl,
				withSliderLoader(root, {
					slidesPerView: 'auto',
					loop: false,
					spaceBetween: 12,
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
							spaceBetween: 16,
						},
						960: {
							spaceBetween: 20,
						},
					},
				})
			);

			instances.push(instance);
		});
	}

	return instances;
};

export default guideSlider;
