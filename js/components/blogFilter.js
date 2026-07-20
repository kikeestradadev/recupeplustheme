const blogFilterInstances = new WeakMap();

const blogFilter = (scope = document) => {
	/** @type {Array<() => void>} */
	const initialized = [];
	const roots = scope.querySelectorAll('[data-blog-filter], .blog-filter');

	if (roots.length > 0) {
		roots.forEach((root) => {
			if (blogFilterInstances.has(root)) return;

			const requestedPerPage = Number(root.dataset.perPage);
			const perPage =
				Number.isFinite(requestedPerPage) && requestedPerPage > 0
					? Math.floor(requestedPerPage)
					: 6;
			const chips = Array.from(root.querySelectorAll('.blog-filter__chip[data-filter]'));
			const gridCards = Array.from(
				root.querySelectorAll('[data-blog-grid] .blog-filter__card')
			);
			const mobileTrack = root.querySelector('[data-blog-mobile-track]');
			const mobileSlides = Array.from(
				root.querySelectorAll('[data-blog-mobile-track] .blog-filter__slide')
			);
			const paginationEl = root.querySelector('[data-blog-pagination]');
			const filtersSwiperEl = root.querySelector('.blog-filter__filters-swiper');
			const filtersPrev = root.querySelector('.blog-filter__filters-nav--prev');
			const filtersNext = root.querySelector('.blog-filter__filters-nav--next');
			const postsSwiperEl = root.querySelector('[data-blog-swiper]');
			const bulletsEl = root.querySelector('.blog-filter__bullets');
			const desktopQuery = window.matchMedia('(min-width: 960px)');
			const controller = new AbortController();
			const { signal } = controller;

			let activeFilter =
				chips.find((chip) => chip.classList.contains('blog-filter__chip--active'))?.dataset
					.filter || 'all';
			let currentPage = 1;
			let filtersSwiper = null;
			let postsSwiper = null;

			const getFiltered = (items) =>
				items.filter(
					(item) => activeFilter === 'all' || item.dataset.category === activeFilter
				);

			const setChipState = () => {
				chips.forEach((chip) => {
					const active = chip.dataset.filter === activeFilter;
					chip.classList.toggle('blog-filter__chip--active', active);
					chip.setAttribute('aria-pressed', String(active));
				});
			};

			const renderPagination = (totalPages) => {
				if (!paginationEl) return;
				paginationEl.innerHTML = '';
				if (totalPages <= 1) return;

				const addButton = (label, page, options = {}) => {
					const button = document.createElement('button');
					button.type = 'button';
					button.className = `blog-filter__page${options.active ? ' blog-filter__page--active' : ''}${options.nav ? ' blog-filter__page--nav' : ''}`;
					button.textContent = label;
					button.disabled = Boolean(options.disabled || options.active);
					if (options.label) button.setAttribute('aria-label', options.label);
					if (options.active) button.setAttribute('aria-current', 'page');

					if (!options.active && !options.disabled) {
						button.addEventListener('click', () => {
							currentPage = page;
							updateBaseView();
						});
					}
					paginationEl.appendChild(button);
				};

				addButton('‹', Math.max(1, currentPage - 1), {
					nav: true,
					disabled: currentPage === 1,
					label: 'Página anterior',
				});
				for (let page = 1; page <= totalPages; page += 1) {
					addButton(String(page), page, {
						active: page === currentPage,
						label: `Página ${page}`,
					});
				}
				addButton('›', Math.min(totalPages, currentPage + 1), {
					nav: true,
					disabled: currentPage === totalPages,
					label: 'Página siguiente',
				});
			};

			const updateDesktopGrid = () => {
				const filtered = getFiltered(gridCards);
				const totalPages = Math.max(1, Math.ceil(filtered.length / perPage));
				if (currentPage > totalPages) currentPage = totalPages;
				const start = (currentPage - 1) * perPage;
				const visibleCards = new Set(filtered.slice(start, start + perPage));

				gridCards.forEach((card) => {
					card.hidden = !visibleCards.has(card);
				});
				renderPagination(totalPages);
			};

			const updateMobileItems = () => {
				mobileSlides.forEach((slide) => {
					slide.hidden = !getFiltered([slide]).length;
				});
			};

			const updateBaseView = () => {
				setChipState();
				updateDesktopGrid();
				updateMobileItems();
			};

			const destroyPostsSwiper = () => {
				if (!postsSwiper) return;
				postsSwiper.destroy(true, true);
				postsSwiper = null;
			};

			const destroyFiltersSwiper = () => {
				if (!filtersSwiper) return;
				filtersSwiper.destroy(true, true);
				filtersSwiper = null;
			};

			const enhanceMobilePosts = () => {
				if (typeof Swiper === 'undefined' || !postsSwiperEl || !mobileTrack) return;
				destroyPostsSwiper();

				const filtered = getFiltered(mobileSlides);
				mobileTrack.innerHTML = '';
				filtered.forEach((slide) => {
					slide.hidden = false;
					mobileTrack.appendChild(slide);
				});
				if (!filtered.length) return;

				try {
					postsSwiper = new Swiper(postsSwiperEl, {
						slidesPerView: 'auto',
						loop: false,
						spaceBetween: 16,
						grabCursor: true,
						watchOverflow: true,
						pagination: {
							el: bulletsEl,
							clickable: true,
						},
						breakpoints: {
							640: {
								spaceBetween: 20,
							},
						},
					});
				} catch (error) {
					postsSwiper = null;
				}
			};

			const enhanceMobileFilters = () => {
				if (typeof Swiper === 'undefined' || !filtersSwiperEl || filtersSwiper) return;

				try {
					filtersSwiper = new Swiper(filtersSwiperEl, {
						slidesPerView: 'auto',
						loop: false,
						spaceBetween: 10,
						freeMode: true,
						grabCursor: true,
						watchOverflow: true,
						navigation: {
							prevEl: filtersPrev,
							nextEl: filtersNext,
						},
					});
				} catch (error) {
					filtersSwiper = null;
				}
			};

			const updateEnhancements = () => {
				if (desktopQuery.matches) {
					destroyPostsSwiper();
					destroyFiltersSwiper();
					return;
				}
				enhanceMobileFilters();
				enhanceMobilePosts();
			};

			chips.forEach((chip) => {
				chip.addEventListener(
					'click',
					() => {
						activeFilter = chip.dataset.filter || 'all';
						currentPage = 1;
						updateBaseView();
						if (!desktopQuery.matches) enhanceMobilePosts();
					},
					{ signal }
				);
			});

			const onViewportChange = () => {
				updateBaseView();
				updateEnhancements();
			};

			if (typeof desktopQuery.addEventListener === 'function') {
				desktopQuery.addEventListener('change', onViewportChange, { signal });
			} else {
				desktopQuery.addListener(onViewportChange);
			}

			updateBaseView();
			updateEnhancements();

			const destroy = () => {
				controller.abort();
				if (typeof desktopQuery.removeEventListener !== 'function') {
					desktopQuery.removeListener(onViewportChange);
				}
				destroyPostsSwiper();
				destroyFiltersSwiper();
				if (mobileTrack) {
					mobileTrack.innerHTML = '';
					mobileSlides.forEach((slide) => {
						slide.hidden = false;
						mobileTrack.appendChild(slide);
					});
				}
				blogFilterInstances.delete(root);
			};

			blogFilterInstances.set(root, destroy);
			initialized.push(destroy);
		});
	}

	return () => initialized.forEach((destroy) => destroy());
};

export default blogFilter;
