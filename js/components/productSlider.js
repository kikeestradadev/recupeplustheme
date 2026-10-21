import { swiperRoots } from './swiperRoots';
import { markExistingSlider, withSliderLoader } from './sliderLoader';

export const productSlideMatches = (presentacion, filter) => presentacion === filter;

const hiddenClassFor = (slide) => (
	slide.classList.contains('product-showcase__related-card')
		? 'product-showcase__related-card--hidden'
		: 'product-slider__slide--hidden'
);

export const applyProductFilter = (root, value) => {
	root.querySelectorAll('[data-product-filter]').forEach((button) => {
		const active = button.getAttribute('data-product-filter') === value;
		button.classList.toggle('product-slider__filter--active', active);
		button.setAttribute('aria-pressed', active ? 'true' : 'false');
	});

	root.querySelectorAll('[data-presentacion]').forEach((slide) => {
		const show = productSlideMatches(slide.getAttribute('data-presentacion'), value);
		slide.classList.toggle(hiddenClassFor(slide), !show);
	});

	const groups = root.matches('.product-showcase__related')
		? [root]
		: [...root.querySelectorAll('.product-slider__group')];

	groups.forEach((group) => {
		const slides = group.querySelectorAll('[data-presentacion]');
		if (!slides.length) return;
		const visible = [...slides].some((slide) => !slide.classList.contains(hiddenClassFor(slide)));
		const empty = group.querySelector('[data-product-filter-empty]');
		const slider = group.querySelector('.product-slider__slider, .product-showcase__related-slider');
		if (empty) empty.hidden = visible;
		if (slider) slider.hidden = !visible;
	});

	root.querySelectorAll('.product-slider__swiper, .product-showcase__related-swiper').forEach((el) => {
		if (!el.swiper) return;
		el.swiper.update();
		el.swiper.slideTo(0, 0);
	});
};

const bindProductFilter = (root) => {
	if (root.dataset.productFilterBound === 'true') return;
	root.dataset.productFilterBound = 'true';
	root.querySelectorAll('[data-product-filter]').forEach((button) => {
		button.addEventListener('click', () => {
			applyProductFilter(root, button.getAttribute('data-product-filter'));
		});
	});
};

const productSlider = (scope = document) => {
	const instances = [];
	const roots = [
		...swiperRoots(scope, '.product-slider'),
		...swiperRoots(scope, '.product-showcase__related')
	];

	roots.forEach((root) => {
		bindProductFilter(root);
	});

	if (roots.length > 0 && typeof Swiper !== 'undefined') {
		roots.forEach((root) => {
			root.querySelectorAll('.product-slider__slider').forEach((group) => {
				const swiperEl = group.querySelector('.product-slider__swiper');
				if (!swiperEl) return;

				if (swiperEl.swiper) {
					markExistingSlider(group);
					instances.push(swiperEl.swiper);
					return;
				}

				const prevEl = group.querySelector('.product-slider__nav--prev');
				const nextEl = group.querySelector('.product-slider__nav--next');
				const paginationEl = group.querySelector('.product-slider__pagination');
				const instance = new Swiper(
					swiperEl,
					withSliderLoader(group, {
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
					})
				);

				instances.push(instance);
			});
			applyProductFilter(root, 'tarro');
		});
	}

	return instances;
};

export default productSlider;
