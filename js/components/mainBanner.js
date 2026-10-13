import { swiperRoots } from './swiperRoots';

const mainBanner = (scope = document) => {
	const instances = [];
	const roots = swiperRoots(scope, '.main-banner');

	if (roots.length > 0 && typeof Swiper !== 'undefined') {
		roots.forEach((root) => {
			const swiperEl = root.querySelector('.main-banner__swiper');
			if (!swiperEl) return;

			if (swiperEl.swiper) {
				instances.push(swiperEl.swiper);
				return;
			}

			const prevEl = root.querySelector('.main-banner__nav--prev');
			const nextEl = root.querySelector('.main-banner__nav--next');
			const paginationEl = root.querySelector('.main-banner__pagination');
			const prefersReducedMotion = window.matchMedia(
				'(prefers-reduced-motion: reduce)'
			).matches;

			const instance = new Swiper(swiperEl, {
				effect: 'fade',
				fadeEffect: {
					crossFade: true,
				},
				speed: prefersReducedMotion ? 0 : 800,
				loop: true,
				autoplay: prefersReducedMotion
					? false
					: {
							delay: 6000,
							disableOnInteraction: false,
						},
				pagination: {
					el: paginationEl,
					clickable: true,
				},
				navigation: {
					prevEl,
					nextEl,
				},
			});

			instances.push(instance);
		});
	}

	return instances;
};

export default mainBanner;
