import { addToCartAjax, openCartDrawer } from './cartDrawer';
import { applyProductFilter } from './productSlider';

const productShowcaseInstances = new WeakMap();

/** Clamp a numeric value into [min, max] (used by qty controls). */
export const clamp = (value, min, max) => Math.min(max, Math.max(min, value));

const productShowcase = (scope = document) => {
	const roots = scope.querySelectorAll('[data-product-showcase], .product-showcase');

	roots.forEach((root) => {
		if (productShowcaseInstances.has(root)) return;

		const thumbs = Array.from(root.querySelectorAll('[data-product-thumb]'));
		const stage = root.querySelector('[data-product-stage]');
		const zoom = root.querySelector('[data-product-zoom]');
		const qtyInput = root.querySelector('[data-product-qty-input]');
		const qtyDec = root.querySelector('[data-product-qty-dec]');
		const qtyInc = root.querySelector('[data-product-qty-inc]');
		const cartForm = root.querySelector('[data-product-cart]');
		const addButton = root.querySelector('.product-showcase__add, [name="add-to-cart"]');
		const productId = Number(
			root.getAttribute('data-product-id') || addButton?.value || 0
		);
		const tabsSwiperEl = root.querySelector('[data-product-tabs-swiper]');
		const tabsPrev = root.querySelector('.product-showcase__tabs-nav--prev');
		const tabsNext = root.querySelector('.product-showcase__tabs-nav--next');
		const relatedSwiperEl = root.querySelector('[data-product-related-swiper]');
		const relatedPrev = root.querySelector('[data-product-related-prev]');
		const relatedNext = root.querySelector('[data-product-related-next]');
		const relatedPagination = root.querySelector('[data-product-related-pagination]');
		const tabs = Array.from(root.querySelectorAll('[data-product-tab]'));
		const panels = Array.from(root.querySelectorAll('[data-product-panel]'));
		const desktopQuery = window.matchMedia('(min-width: 960px)');
		const controller = new AbortController();
		const { signal } = controller;
		let tabsSwiper = null;
		let relatedSwiper = null;

		const setActiveThumb = (thumb) => {
			if (!stage || !thumb) return;

			const nextSrc = thumb.dataset.src;
			if (!nextSrc) return;

			stage.src = nextSrc;
			stage.alt = thumb.getAttribute('aria-label')?.replace(/^Ver\s+/i, '') || stage.alt;

			thumbs.forEach((item) => {
				const isActive = item === thumb;
				item.classList.toggle('product-showcase__thumb--active', isActive);
				item.setAttribute('aria-pressed', isActive ? 'true' : 'false');
			});
		};

		const updateQty = (nextValue) => {
			if (!qtyInput) return;

			const min = Number(qtyInput.min) || 1;
			const max = Number(qtyInput.max) || 99;
			qtyInput.value = String(clamp(nextValue, min, max));
		};

		const activateTab = (tabId) => {
			tabs.forEach((tab) => {
				const isActive = tab.dataset.productTab === tabId;
				tab.classList.toggle('product-showcase__tab--active', isActive);
				tab.setAttribute('aria-selected', isActive ? 'true' : 'false');
				tab.tabIndex = isActive ? 0 : -1;
			});

			panels.forEach((panel) => {
				const isActive = panel.dataset.productPanel === tabId;
				panel.classList.toggle('product-showcase__panel--active', isActive);
				panel.hidden = !isActive;
			});
		};

		const destroyTabsSwiper = () => {
			if (!tabsSwiper) return;
			tabsSwiper.destroy(true, true);
			tabsSwiper = null;
		};

		const destroyRelatedSwiper = () => {
			if (!relatedSwiper) return;
			relatedSwiper.destroy(true, true);
			relatedSwiper = null;
		};

		const initRelatedSwiper = () => {
			if (typeof window.Swiper === 'undefined' || !relatedSwiperEl || relatedSwiper) return;

			try {
				relatedSwiper = new window.Swiper(relatedSwiperEl, {
					slidesPerView: 'auto',
					loop: false,
					spaceBetween: 16,
					grabCursor: true,
					watchOverflow: true,
					pagination: {
						el: relatedPagination,
						clickable: true
					},
					navigation: {
						prevEl: relatedPrev,
						nextEl: relatedNext
					},
					breakpoints: {
						640: {
							spaceBetween: 20
						},
						960: {
							spaceBetween: 24
						}
					}
				});
				const relatedRoot = root.querySelector('.product-showcase__related');
				if (relatedRoot) {
					const activeFilter = relatedRoot.querySelector('[data-product-filter][aria-pressed="true"]');
					applyProductFilter(
						relatedRoot,
						activeFilter ? activeFilter.getAttribute('data-product-filter') : 'tarro'
					);
				}
			} catch (error) {
				relatedSwiper = null;
			}
		};

		const enhanceMobileTabs = () => {
			if (typeof window.Swiper === 'undefined' || !tabsSwiperEl || tabsSwiper) return;

			try {
				tabsSwiper = new window.Swiper(tabsSwiperEl, {
					slidesPerView: 'auto',
					loop: false,
					spaceBetween: 30,
					freeMode: true,
					grabCursor: true,
					watchOverflow: true,
					navigation: {
						prevEl: tabsPrev,
						nextEl: tabsNext
					}
				});
			} catch (error) {
				tabsSwiper = null;
			}
		};

		const updateTabsEnhancement = () => {
			if (desktopQuery.matches) {
				destroyTabsSwiper();
				return;
			}
			enhanceMobileTabs();
		};

		const onZoomMove = (event) => {
			if (!zoom || !stage || !desktopQuery.matches) return;

			const rect = zoom.getBoundingClientRect();
			if (!rect.width || !rect.height) return;

			const x = ((event.clientX - rect.left) / rect.width) * 100;
			const y = ((event.clientY - rect.top) / rect.height) * 100;

			stage.style.transformOrigin = `${clamp(x, 0, 100)}% ${clamp(y, 0, 100)}%`;
		};

		const onZoomLeave = () => {
			if (!stage) return;
			stage.style.transformOrigin = 'center center';
		};

		thumbs.forEach((thumb) => {
			thumb.addEventListener(
				'click',
				() => {
					setActiveThumb(thumb);
				},
				{ signal }
			);
		});

		if (zoom) {
			zoom.addEventListener('mousemove', onZoomMove, { signal });
			zoom.addEventListener('mouseleave', onZoomLeave, { signal });
		}

		qtyDec?.addEventListener(
			'click',
			() => {
				updateQty(Number(qtyInput?.value || 1) - 1);
			},
			{ signal }
		);

		qtyInc?.addEventListener(
			'click',
			() => {
				updateQty(Number(qtyInput?.value || 1) + 1);
			},
			{ signal }
		);

		qtyInput?.addEventListener(
			'change',
			() => {
				updateQty(Number(qtyInput.value) || 1);
			},
			{ signal }
		);

		cartForm?.addEventListener(
			'submit',
			async (event) => {
				event.preventDefault();
				if (!productId || !addButton) return;

				const quantity = clamp(Number(qtyInput?.value || 1), 1, Number(qtyInput?.max) || 99);
				addButton.disabled = true;
				addButton.classList.add('loading');

				try {
					const data = await addToCartAjax({ productId, quantity });
					if (data?.error && data?.product_url) {
						window.location = data.product_url;
						return;
					}
					openCartDrawer();
				} catch (error) {
					console.error(error);
				} finally {
					addButton.disabled = false;
					addButton.classList.remove('loading');
				}
			},
			{ signal }
		);

		tabs.forEach((tab) => {
			tab.addEventListener(
				'click',
				() => {
					activateTab(tab.dataset.productTab);
					if (tabsSwiper) {
						const slide = tab.closest('.swiper-slide');
						const slideIndex = slide
							? Array.from(slide.parentElement.children).indexOf(slide)
							: tabs.indexOf(tab);
						tabsSwiper.slideTo(slideIndex);
					}
				},
				{ signal }
			);

			tab.addEventListener(
				'keydown',
				(event) => {
					const currentIndex = tabs.indexOf(tab);
					if (currentIndex < 0) return;

					let nextIndex = currentIndex;
					if (event.key === 'ArrowRight' || event.key === 'ArrowDown') {
						nextIndex = (currentIndex + 1) % tabs.length;
					} else if (event.key === 'ArrowLeft' || event.key === 'ArrowUp') {
						nextIndex = (currentIndex - 1 + tabs.length) % tabs.length;
					} else if (event.key === 'Home') {
						nextIndex = 0;
					} else if (event.key === 'End') {
						nextIndex = tabs.length - 1;
					} else {
						return;
					}

					event.preventDefault();
					const nextTab = tabs[nextIndex];
					activateTab(nextTab.dataset.productTab);
					nextTab.focus();
					if (tabsSwiper) {
						const slide = nextTab.closest('.swiper-slide');
						const slideIndex = slide
							? Array.from(slide.parentElement.children).indexOf(slide)
							: nextIndex;
						tabsSwiper.slideTo(slideIndex);
					}
				},
				{ signal }
			);
		});

		const filterScroller = root.querySelector('.product-showcase__related .product-slider__filters');
		const filterPrev = root.querySelector('[data-product-filter-prev]');
		const filterNext = root.querySelector('[data-product-filter-next]');

		const updateFilterArrows = () => {
			if (!filterScroller || !filterPrev || !filterNext) return;

			const maxScroll = filterScroller.scrollWidth - filterScroller.clientWidth;
			filterPrev.disabled = maxScroll <= 1 || filterScroller.scrollLeft <= 1;
			filterNext.disabled = maxScroll <= 1 || filterScroller.scrollLeft >= maxScroll - 1;
		};

		const scrollFilters = (direction) => {
			if (!filterScroller) return;

			const amount = Math.max(120, Math.round(filterScroller.clientWidth * 0.75));
			filterScroller.scrollBy({ left: direction * amount, behavior: 'smooth' });
		};

		filterPrev?.addEventListener('click', () => scrollFilters(-1), { signal });
		filterNext?.addEventListener('click', () => scrollFilters(1), { signal });
		filterScroller?.addEventListener('scroll', updateFilterArrows, { signal, passive: true });

		if (typeof ResizeObserver !== 'undefined' && filterScroller) {
			const filterObserver = new ResizeObserver(updateFilterArrows);
			filterObserver.observe(filterScroller);
			signal.addEventListener('abort', () => filterObserver.disconnect(), { once: true });
		}

		updateFilterArrows();
		updateTabsEnhancement();
		initRelatedSwiper();
		if (typeof desktopQuery.addEventListener === 'function') {
			desktopQuery.addEventListener('change', updateTabsEnhancement, { signal });
		} else if (typeof desktopQuery.addListener === 'function') {
			const onChange = () => updateTabsEnhancement();
			desktopQuery.addListener(onChange);
			signal.addEventListener('abort', () => desktopQuery.removeListener(onChange), { once: true });
		}

		productShowcaseInstances.set(root, {
			destroy() {
				destroyTabsSwiper();
				destroyRelatedSwiper();
				controller.abort();
				productShowcaseInstances.delete(root);
			}
		});
	});
};

export default productShowcase;
