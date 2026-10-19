const cartDrawerInstances = new WeakMap();

const getCartConfig = () => window.wpbCart || {};

const wcAjaxUrl = (endpoint) => {
	const cfg = getCartConfig();
	if (cfg.ajaxUrl && cfg.ajaxUrl.includes('%%endpoint%%')) {
		return cfg.ajaxUrl.replace('%%endpoint%%', endpoint);
	}
	return `${cfg.wcAjaxUrl || '/?wc-ajax='}${endpoint}`;
};

const parseProductQtys = (content) => {
	try {
		const raw = content?.getAttribute('data-cart-product-qtys') || '{}';
		return JSON.parse(raw);
	} catch (error) {
		return {};
	}
};

const syncShowcaseWithCart = () => {
	const content = document.querySelector('[data-cart-drawer-content]');
	const total =
		Number(content?.getAttribute('data-cart-total-count')) ||
		Number(document.querySelector('[data-cart-drawer-badge]')?.textContent) ||
		0;
	const productQtys = parseProductQtys(content);

	document.querySelectorAll('[data-cart-count]').forEach((el) => {
		el.textContent = String(total);
		el.hidden = total < 1;
	});

	document.querySelectorAll('[data-product-showcase]').forEach((root) => {
		const productId = Number(root.getAttribute('data-product-id') || 0);
		const qtyInput = root.querySelector('[data-product-qty-input]');
		if (!qtyInput || !productId) return;

		const inCart = Number(productQtys[productId] || productQtys[String(productId)] || 0);
		if (inCart > 0) {
			qtyInput.value = String(inCart);
		}
	});

	if (window.wpbCart) {
		window.wpbCart.cartCount = total;
		window.wpbCart.productQtys = productQtys;
	}
};

const applyFragments = (fragments) => {
	if (!fragments || typeof fragments !== 'object') return;

	Object.keys(fragments).forEach((selector) => {
		if (!selector || selector === 'wpb_cart_product_qtys') return;
		const html = fragments[selector];
		if (typeof html !== 'string') return;
		document.querySelectorAll(selector).forEach((node) => {
			const template = document.createElement('template');
			template.innerHTML = html.trim();
			const next = template.content.firstElementChild;
			if (next) {
				node.replaceWith(next);
			}
		});
	});

	syncShowcaseWithCart();
	document.dispatchEvent(new CustomEvent('wpb_cart_updated', { detail: { fragments } }));
};

const updateCartItemQty = async (cartItemKey, quantity) => {
	const cfg = getCartConfig();
	const body = new URLSearchParams();
	body.set('cart_item_key', cartItemKey);
	body.set('quantity', String(quantity));
	body.set('nonce', cfg.cartNonce || '');

	const response = await fetch(wcAjaxUrl('wpb_update_cart_item'), {
		method: 'POST',
		credentials: 'same-origin',
		headers: {
			'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8'
		},
		body: body.toString()
	});

	const data = await response.json();
	if (data && data.fragments) {
		applyFragments(data.fragments);
	}
	return data;
};

export const addToCartAjax = async ({ productId, quantity = 1 }) => {
	const body = new URLSearchParams();
	body.set('product_id', String(productId));
	body.set('quantity', String(quantity));

	const response = await fetch(wcAjaxUrl('add_to_cart'), {
		method: 'POST',
		credentials: 'same-origin',
		headers: {
			'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8'
		},
		body: body.toString()
	});

	const data = await response.json();
	if (data && data.fragments) {
		applyFragments(data.fragments);
		if (window.jQuery) {
			window.jQuery(document.body).trigger('added_to_cart', [
				data.fragments,
				data.cart_hash,
				null
			]);
		}
	}
	return data;
};

export const openCartDrawer = () => {
	const root = document.querySelector('[data-cart-drawer]');
	if (!root) return;
	root.classList.add('cart-drawer--open');
	root.setAttribute('aria-hidden', 'false');
	document.body.classList.add('cart-drawer-open');
	document.querySelectorAll('[data-cart-drawer-open]').forEach((trigger) => {
		trigger.setAttribute('aria-expanded', 'true');
	});
};

export const closeCartDrawer = () => {
	const root = document.querySelector('[data-cart-drawer]');
	if (!root) return;
	root.classList.remove('cart-drawer--open');
	root.setAttribute('aria-hidden', 'true');
	document.body.classList.remove('cart-drawer-open');
	document.querySelectorAll('[data-cart-drawer-open]').forEach((trigger) => {
		trigger.setAttribute('aria-expanded', 'false');
	});
};

const cartDrawer = (scope = document) => {
	const roots = scope.querySelectorAll('[data-cart-drawer], .cart-drawer');
	if (!roots.length) return;

	roots.forEach((root) => {
		if (cartDrawerInstances.has(root)) return;

		const panel = root.querySelector('[data-cart-drawer-panel], .cart-drawer__panel');
		const controller = new AbortController();
		const { signal } = controller;
		let lastFocus = null;
		let qtyBusy = false;

		const setOpen = (open) => {
			root.classList.toggle('cart-drawer--open', open);
			root.setAttribute('aria-hidden', String(!open));
			document.body.classList.toggle('cart-drawer-open', open);

			document.querySelectorAll('[data-cart-drawer-open], .main-menu__cart').forEach((trigger) => {
				trigger.setAttribute('aria-expanded', String(open));
				if (!trigger.getAttribute('aria-controls') && root.id) {
					trigger.setAttribute('aria-controls', root.id);
				}
			});

			if (open) {
				lastFocus = document.activeElement;
				const focusTarget =
					root.querySelector('[data-cart-drawer-close].cart-drawer__close') || panel;
				window.requestAnimationFrame(() => focusTarget?.focus?.());
			} else if (lastFocus && typeof lastFocus.focus === 'function') {
				lastFocus.focus();
				lastFocus = null;
			}
		};

		const onOpenClick = (event) => {
			event.preventDefault();
			setOpen(true);
		};

		const onCloseClick = (event) => {
			if (event.target.closest('[data-cart-drawer-close]')) {
				event.preventDefault();
				setOpen(false);
			}
		};

		const onKeyDown = (event) => {
			if (event.key === 'Escape' && root.classList.contains('cart-drawer--open')) {
				setOpen(false);
			}
		};

		const onBodyClick = async (event) => {
			const decrease = event.target.closest('[data-cart-item-decrease]');
			const increase = event.target.closest('[data-cart-item-increase]');
			const remove = event.target.closest('[data-cart-item-remove]');
			if (!decrease && !increase && !remove) return;

			event.preventDefault();
			const item = event.target.closest('[data-cart-item]');
			if (!item || qtyBusy) return;

			const key = item.getAttribute('data-cart-item-key');
			if (!key) return;

			const input = item.querySelector('[data-cart-item-input]');
			let qty = Number(input?.value || 1);

			try {
				qtyBusy = true;
				if (remove) {
					await updateCartItemQty(key, 0);
					return;
				}
				if (decrease) qty = Math.max(0, qty - 1);
				if (increase) qty = Math.min(99, qty + 1);
				await updateCartItemQty(key, qty);
			} catch (error) {
				console.error(error);
			} finally {
				qtyBusy = false;
			}
		};

		document.querySelectorAll('[data-cart-drawer-open], .main-menu__cart').forEach((trigger) => {
			trigger.setAttribute('aria-haspopup', 'dialog');
			trigger.setAttribute('aria-expanded', 'false');
			if (root.id) trigger.setAttribute('aria-controls', root.id);
			trigger.addEventListener('click', onOpenClick, { signal });
		});

		root.addEventListener('click', onCloseClick, { signal });
		root.addEventListener('click', onBodyClick, { signal });
		document.addEventListener('keydown', onKeyDown, { signal });

		const onGridAddClick = async (event) => {
			const btn = event.target.closest('[data-wpb-add-to-cart]');
			if (!btn || btn.disabled) return;

			event.preventDefault();
			event.stopPropagation();

			const productId = Number(btn.getAttribute('data-product-id') || btn.getAttribute('data-product_id') || 0);
			const quantity = Math.max(1, Number(btn.getAttribute('data-quantity') || 1));
			if (!productId) return;

			btn.disabled = true;
			btn.classList.add('loading');
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
				btn.disabled = false;
				btn.classList.remove('loading');
			}
		};

		document.addEventListener('click', onGridAddClick, { signal, capture: true });

		if (window.jQuery) {
			const $ = window.jQuery;
			const onAdded = () => {
				syncShowcaseWithCart();
				openCartDrawer();
			};
			const onRefreshed = () => {
				syncShowcaseWithCart();
			};
			$(document.body).on('added_to_cart', onAdded);
			$(document.body).on('removed_from_cart wc_fragments_refreshed', onRefreshed);
			signal.addEventListener(
				'abort',
				() => {
					$(document.body).off('added_to_cart', onAdded);
					$(document.body).off('removed_from_cart wc_fragments_refreshed', onRefreshed);
				},
				{ once: true }
			);
		}

		syncShowcaseWithCart();

		cartDrawerInstances.set(root, () => {
			controller.abort();
			setOpen(false);
			cartDrawerInstances.delete(root);
		});
	});
};

export default cartDrawer;
