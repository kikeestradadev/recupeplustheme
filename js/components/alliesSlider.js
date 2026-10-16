import { swiperRoots } from './swiperRoots';
import { markExistingSlider, withSliderLoader } from './sliderLoader';

const alliesSlider = (scope = document) => {
	const instances = [];
	const roots = swiperRoots(scope, '.allies-slider');

	if (roots.length > 0 && typeof Swiper !== 'undefined') {
		roots.forEach((root) => {
			const swiperEl = root.querySelector('.allies-slider__swiper');
			if (!swiperEl) return;

			if (swiperEl.swiper) {
				markExistingSlider(root);
				instances.push(swiperEl.swiper);
				return;
			}

			const paginationEl = root.querySelector('.allies-slider__pagination');
			const slideCount = swiperEl.querySelectorAll('.swiper-slide').length;
			// loop needs enough slides; with few logos Swiper can stall imagesReady
			// and leave .slider-loader without --ready (opacity: 0).
			const canLoop = slideCount > 4;
			const instance = new Swiper(
				swiperEl,
				withSliderLoader(root, {
					slidesPerView: 'auto',
					loop: canLoop,
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
				})
			);

			instances.push(instance);
		});
	}

	return instances;
};

export default alliesSlider;
