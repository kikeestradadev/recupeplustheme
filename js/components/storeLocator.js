const storeLocatorInstances = new WeakMap();

const storeLocator = (scope = document) => {
	/** @type {Array<() => void>} */
	const initialized = [];
	const roots = scope.querySelectorAll('[data-store-locator], .store-locator');

	if (roots.length > 0) {
		roots.forEach((root) => {
			if (storeLocatorInstances.has(root)) return;

			const items = Array.from(root.querySelectorAll('.store-locator__item[data-store]'));
			const panels = Array.from(root.querySelectorAll('.store-locator__map-panel[data-store]'));
			const controller = new AbortController();
			const { signal } = controller;

			/** @param {string | undefined} storeId */
			const setActive = (storeId) => {
				items.forEach((item) => {
					const active = item.getAttribute('data-store') === storeId;
					const button = item.querySelector('.store-locator__item-btn');
					item.classList.toggle('store-locator__item--active', active);
					if (button) {
						button.setAttribute('aria-selected', String(active));
						button.setAttribute('tabindex', active ? '0' : '-1');
					}
				});

				panels.forEach((panel) => {
					const active = panel.getAttribute('data-store') === storeId;
					panel.classList.toggle('store-locator__map-panel--active', active);
					panel.setAttribute('aria-hidden', String(!active));
				});
			};

			items.forEach((item) => {
				const button = item.querySelector('.store-locator__item-btn');
				if (!button) return;

				button.addEventListener(
					'click',
					() => setActive(item.getAttribute('data-store') || undefined),
					{ signal }
				);
			});

			const initialItem =
				items.find((item) => item.classList.contains('store-locator__item--active')) ||
				items[0];
			if (initialItem) setActive(initialItem.getAttribute('data-store') || undefined);

			const destroy = () => {
				controller.abort();
				storeLocatorInstances.delete(root);
			};

			storeLocatorInstances.set(root, destroy);
			initialized.push(destroy);
		});
	}

	return () => initialized.forEach((destroy) => destroy());
};

export default storeLocator;
