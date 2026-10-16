import { swiperRoots } from './swiperRoots';
import { markExistingSlider, withSliderLoader } from './sliderLoader';

const momentSlider = (scope = document) => {
	const instances = [];
	const roots = swiperRoots(scope, '.moment-slider');

	if (roots.length > 0 && typeof Swiper !== 'undefined') {
		roots.forEach((root) => {
			const swiperEl = root.querySelector('.moment-slider__swiper');
			if (!swiperEl) return;

			if (swiperEl.swiper) {
				markExistingSlider(root);
				instances.push(swiperEl.swiper);
				return;
			}

			const prevEl = root.querySelector('.moment-slider__nav--prev');
			const nextEl = root.querySelector('.moment-slider__nav--next');
			const paginationEl = root.querySelector('.moment-slider__pagination');
			const instance = new Swiper(
				swiperEl,
				withSliderLoader(root, {
					slidesPerView: 'auto',
					loop: false,
					spaceBetween: 16,
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
							spaceBetween: 20,
						},
						960: {
							spaceBetween: 24,
						},
					},
				})
			);

			instances.push(instance);
		});
	}

	return instances;
};

export default momentSlider;
