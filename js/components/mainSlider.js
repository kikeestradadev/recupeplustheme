const mainSlider = (scope = document) => {
	const instances = [];
	const roots = scope.querySelectorAll('[data-main-slider], .main-slider');

	if (roots.length > 0 && typeof Swiper !== 'undefined') {
		roots.forEach((root) => {
			const swiperEl = root.querySelector('.main-slider__swiper');
			if (!swiperEl) return;

			if (swiperEl.swiper) {
				instances.push(swiperEl.swiper);
				return;
			}

			const prevEl = root.querySelector('.main-slider__nav--prev');
			const nextEl = root.querySelector('.main-slider__nav--next');
			const paginationEl = root.querySelector('.main-slider__pagination');
			const autoplayToggle = root.querySelector('[data-main-slider-autoplay]');
			const prefersReducedMotion = window.matchMedia(
				'(prefers-reduced-motion: reduce)'
			).matches;

			const instance = new Swiper(swiperEl, {
				effect: 'fade',
				fadeEffect: {
					crossFade: true,
				},
				speed: prefersReducedMotion ? 0 : 900,
				loop: true,
				grabCursor: true,
				keyboard: {
					enabled: true,
					onlyInViewport: true,
				},
				autoplay: prefersReducedMotion
					? false
					: {
							delay: 6000,
							disableOnInteraction: false,
							pauseOnMouseEnter: true,
						},
				pagination: {
					el: paginationEl,
					clickable: true,
				},
				navigation: {
					prevEl,
					nextEl,
				},
				a11y: {
					enabled: true,
					prevSlideMessage: 'Diapositiva anterior',
					nextSlideMessage: 'Diapositiva siguiente',
					firstSlideMessage: 'Esta es la primera diapositiva',
					lastSlideMessage: 'Esta es la última diapositiva',
					paginationBulletMessage: 'Ir a la diapositiva {{index}}',
				},
				on: {
					init: () => root.classList.add('main-slider--ready'),
				},
			});

			if (autoplayToggle) {
				if (prefersReducedMotion || !instance.autoplay) {
					autoplayToggle.hidden = true;
				} else {
					let isPaused = false;
					const label = autoplayToggle.querySelector(
						'.main-slider__autoplay-label'
					);
					const icon = autoplayToggle.querySelector(
						'.main-slider__autoplay-icon'
					);

					const updateAutoplayControl = () => {
						autoplayToggle.setAttribute('aria-pressed', String(isPaused));
						autoplayToggle.setAttribute(
							'aria-label',
							isPaused
								? 'Reanudar reproducción automática'
								: 'Pausar reproducción automática'
						);
						if (label) label.textContent = isPaused ? 'Reanudar' : 'Pausar';
						if (icon) icon.textContent = isPaused ? '▶' : 'Ⅱ';
					};

					autoplayToggle.addEventListener('click', () => {
						isPaused = !isPaused;
						if (isPaused) {
							instance.autoplay.stop();
						} else {
							instance.autoplay.start();
						}
						updateAutoplayControl();
					});

					updateAutoplayControl();
				}
			}

			instances.push(instance);
		});
	}

	return instances;
};

export default mainSlider;
