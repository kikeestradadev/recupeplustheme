const storeLocatorInstances = new WeakMap();

const storeLocator = (scope = document) => {
	/** @type {Array<() => void>} */
	const initialized = [];
	const roots = scope.querySelectorAll('[data-store-locator], .store-locator');

	if (roots.length > 0) {
		roots.forEach((root) => {
			if (storeLocatorInstances.has(root)) return;

			const items = Array.from(root.querySelectorAll('.store-locator__item[data-store]'));
			const pins = Array.from(root.querySelectorAll('.store-locator__pin[data-store]'));
			const form = root.querySelector('[data-store-locator-search], .store-locator__search');
			const input = form?.querySelector('.store-locator__input');
			const status = root.querySelector('[data-store-locator-status]');
			const controller = new AbortController();
			const { signal } = controller;

			/** @param {string | undefined} storeId */
			const setActive = (storeId) => {
				items.forEach((item) => {
					const active = item.getAttribute('data-store') === storeId;
					item.classList.toggle('store-locator__item--active', active);
					item.querySelector('.store-locator__item-btn')?.setAttribute(
						'aria-pressed',
						String(active)
					);
				});

				pins.forEach((pin) => {
					pin.classList.toggle(
						'store-locator__pin--active',
						pin.getAttribute('data-store') === storeId
					);
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

			form?.addEventListener(
				'submit',
				(event) => {
					event.preventDefault();
					const query = input?.value.trim().toLocaleLowerCase('es') || '';
					const matches = items.filter(
						(item) => !query || item.textContent.toLocaleLowerCase('es').includes(query)
					);

					items.forEach((item) => {
						item.hidden = !matches.includes(item);
					});

					const firstMatch = matches[0];
					setActive(firstMatch?.getAttribute('data-store') || undefined);
					if (status) {
						status.textContent =
							matches.length === 1
								? 'Se encontró una tienda.'
								: `Se encontraron ${matches.length} tiendas.`;
					}
				},
				{ signal }
			);

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
