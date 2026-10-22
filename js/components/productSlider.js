import { swiperRoots } from './swiperRoots';
import { markExistingSlider, withSliderLoader } from './sliderLoader';

export const productSlideMatches = (presentacion, filter) => presentacion === filter;

const hiddenClassFor = (slide) => (
	slide.classList.contains('product-showcase__related-card')
		? 'product-showcase__related-card--hidden'
		: 'product-slider__slide--hidden'
);

const slideFilterKey = (slide) => (
	slide.getAttribute('data-product-group') || slide.getAttribute('data-presentacion')
);

export const applyProductFilter = (root, value) => {
	root.querySelectorAll('[data-product-filter]').forEach((button) => {
		const active = button.getAttribute('data-product-filter') === value;
		button.classList.toggle('product-slider__filter--active', active);
		button.setAttribute('aria-pressed', active ? 'true' : 'false');
	});

	root.querySelectorAll('[data-presentacion], [data-product-group]').forEach((slide) => {
		const show = productSlideMatches(slideFilterKey(slide), value);
		slide.classList.toggle(hiddenClassFor(slide), !show);
	});

	const groups = root.matches('.product-showcase__related')
		? [root]
		: [...root.querySelectorAll('.product-slider__group')];

	groups.forEach((group) => {
		const slides = group.querySelectorAll('[data-presentacion], [data-product-group]');
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

const bindFilterArrows = (root) => {
	if (root.dataset.filterArrowsBound === 'true') return;

	const scroller = root.querySelector('.product-slider__filter-bar .product-slider__filters');
	const prev = root.querySelector('[data-slider-filter-prev]');
	const next = root.querySelector('[data-slider-filter-next]');
	if (!scroller || !prev || !next) return;

	root.dataset.filterArrowsBound = 'true';

	const updateFilterArrows = () => {
		const maxScroll = scroller.scrollWidth - scroller.clientWidth;
		prev.disabled = maxScroll <= 1 || scroller.scrollLeft <= 1;
		next.disabled = maxScroll <= 1 || scroller.scrollLeft >= maxScroll - 1;
	};

	const scrollFilters = (direction) => {
		const amount = Math.max(120, Math.round(scroller.clientWidth * 0.75));
		scroller.scrollBy({ left: direction * amount, behavior: 'smooth' });
	};

	prev.addEventListener('click', () => scrollFilters(-1));
	next.addEventListener('click', () => scrollFilters(1));
	scroller.addEventListener('scroll', updateFilterArrows, { passive: true });

	if (typeof ResizeObserver !== 'undefined') {
		const observer = new ResizeObserver(updateFilterArrows);
		observer.observe(scroller);
	}

	updateFilterArrows();
};

const productSlider = (scope = document) => {
	const instances = [];
	const roots = [
		...swiperRoots(scope, '.product-slider'),
		...swiperRoots(scope, '.product-showcase__related')
	];

	roots.forEach((root) => {
		bindProductFilter(root);
		bindFilterArrows(root);
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
			const activeFilter = root.querySelector('[data-product-filter][aria-pressed="true"]');
			applyProductFilter(root, activeFilter ? activeFilter.getAttribute('data-product-filter') : 'tarro');
		});
	}

	return instances;
};

export default productSlider;
