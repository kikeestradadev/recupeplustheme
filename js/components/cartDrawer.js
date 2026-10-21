const cartDrawerInstances = new WeakMap();

/** @returns {Record<string, unknown>} */
export const getCartConfig = () => window.wpbCart || {};

/**
 * Build a WooCommerce AJAX endpoint URL from localized cart config.
 *
 * @param {string} endpoint WC ajax endpoint name.
 * @returns {string}
 */
export const wcAjaxUrl = (endpoint) => {
	const cfg = getCartConfig();
	if (cfg.ajaxUrl && String(cfg.ajaxUrl).includes('%%endpoint%%')) {
		return String(cfg.ajaxUrl).replace('%%endpoint%%', endpoint);
	}
	return `${cfg.wcAjaxUrl || '/?wc-ajax='}${endpoint}`;
};

/**
 * Parse product qty map from the cart drawer content node.
 *
 * @param {Element | null | undefined} content
 * @returns {Record<string, number>}
 */
export const parseProductQtys = (content) => {
	try {
		const raw = content?.getAttribute('data-cart-product-qtys') || '{}';
		const parsed = JSON.parse(raw);
		return parsed && typeof parsed === 'object' && !Array.isArray(parsed) ? parsed : {};
	} catch (error) {
		return {};
	}
};

/**
 * Keep showcase qty inputs and cart counters in sync with drawer fragments.
 */
export const syncShowcaseWithCart = () => {
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

/**
 * Apply WooCommerce cart fragments to matching DOM nodes.
 *
 * @param {Record<string, string> | null | undefined} fragments
 */
export const applyFragments = (fragments) => {
	if (!fragments || typeof fragments !== 'object') return;

	Object.keys(fragments).forEach((selector) => {
		if (!selector || selector === 'wpb_cart_product_qtys') return;
		const html = fragments[selector];
		if (typeof html !== 'string') return;

		let nodes;
		try {
			nodes = document.querySelectorAll(selector);
		} catch (error) {
			return;
		}

		nodes.forEach((node) => {
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

/**
 * CRC display price, same grouping as PHP `wpb_cart_format_price`.
 *
 * @param {number} amount
 * @returns {string}
 */
export const formatCartPrice = (amount) => {
	const rounded = Math.round(Number(amount) || 0);
	const grouped = String(Math.abs(rounded)).replace(/\B(?=(\d{3})+(?!\d))/g, ' ');
	return `₡${rounded < 0 ? '-' : ''}${grouped}`;
};

/** Delays before repeating a failed cart write. Tests may replace these values. */
export const cartRetryDelays = [350, 900];

const CART_LOCK_KEY = 'wpbCartLock';
const CART_HASH_KEY = 'wpbCartHash';
const TAB_ID = `wpb-${Math.random().toString(36).slice(2)}`;

/** @type {Map<string, number>} */
const desiredQty = new Map();
/** @type {Map<string, number>} */
const confirmedQty = new Map();
/** @type {Map<string, string>} */
const rowSnapshots = new Map();
/** @type {Array<{ productId: number, quantity: number, resolve: Function, reject: Function }>} */
const pendingAdds = [];
let draining = false;
let cartQueueBound = false;

const cartMessage = (key, fallback) => {
	const i18n = getCartConfig().i18n || {};
	return i18n[key] || fallback;
};

const rememberCartHash = (hash) => {
	if (!hash) return;
	if (!window.wpbCart) window.wpbCart = {};
	window.wpbCart.cartHash = hash;
	try {
		localStorage.setItem(CART_HASH_KEY, String(hash));
	} catch (error) {
		/* private mode */
	}
};

const knownCartHash = () => String(getCartConfig().cartHash || '');

const sleep = (ms) => new Promise((resolve) => {
	window.setTimeout(resolve, ms);
});

/**
 * @param {string} message
 * @param {'info' | 'error'} [tone]
 */
export const showCartNotice = (message, tone = 'info') => {
	const root = document.querySelector('[data-cart-drawer]');
	if (!root) return;
	let el = root.querySelector('[data-cart-drawer-notice]');
	if (!el) {
		el = document.createElement('p');
		el.className = 'cart-drawer__notice';
		el.setAttribute('data-cart-drawer-notice', '');
		el.setAttribute('role', 'status');
		el.setAttribute('aria-live', 'polite');
		const panel = root.querySelector('[data-cart-drawer-panel], .cart-drawer__panel') || root;
		const header = panel.querySelector('.cart-drawer__header');
		if (header) header.insertAdjacentElement('afterend', el);
		else panel.prepend(el);
	}
	el.classList.toggle('cart-drawer__notice--error', tone === 'error');
	el.hidden = !message;
	el.textContent = message || '';
};

const readLock = () => {
	try {
		return JSON.parse(localStorage.getItem(CART_LOCK_KEY) || 'null');
	} catch (error) {
		return null;
	}
};

const acquireCartLock = async () => {
	const started = Date.now();
	while (Date.now() - started < 8000) {
		const lock = readLock();
		if (!lock || lock.tab === TAB_ID || Number(lock.until) < Date.now()) {
			try {
				localStorage.setItem(
					CART_LOCK_KEY,
					JSON.stringify({ tab: TAB_ID, until: Date.now() + 8000 })
				);
			} catch (error) {
				return;
			}
			const confirmLock = readLock();
			if (!confirmLock || confirmLock.tab === TAB_ID) return;
		}
		await sleep(40);
	}
};

const releaseCartLock = () => {
	const lock = readLock();
	if (lock && lock.tab === TAB_ID) {
		try {
			localStorage.removeItem(CART_LOCK_KEY);
		} catch (error) {
			/* ignore */
		}
	}
};

const foreignCartHash = () => {
	try {
		const stored = localStorage.getItem(CART_HASH_KEY) || '';
		const mine = knownCartHash();
		return Boolean(stored && mine && stored !== mine);
	} catch (error) {
		return false;
	}
};

const responseFailed = (response) =>
	response?.ok === false || (typeof response?.status === 'number' && response.status >= 400);

const conflictPayload = (data) => {
	if (!data || typeof data !== 'object') return null;
	if (data.code === 'cart_conflict') return data;
	if (data.data && data.data.code === 'cart_conflict') return data.data;
	return null;
};

const postCart = async (endpoint, params) => {
	let response;
	try {
		response = await fetch(wcAjaxUrl(endpoint), {
			method: 'POST',
			credentials: 'same-origin',
			headers: {
				'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8'
			},
			body: params.toString()
		});
	} catch (error) {
		const failure = new Error('network');
		failure.code = 'network';
		throw failure;
	}

	let data = null;
	try {
		data = await response.json();
	} catch (error) {
		const failure = new Error('network');
		failure.code = 'network';
		throw failure;
	}

	if (data && data.cart_hash) rememberCartHash(data.cart_hash);
	const conflict = conflictPayload(data);
	if (conflict) {
		const failure = new Error('conflict');
		failure.code = 'conflict';
		failure.data = conflict.fragments ? conflict : data;
		throw failure;
	}
	if (responseFailed(response) || data?.success === false) {
		const failure = new Error('http');
		failure.code = 'http';
		failure.data = data;
		throw failure;
	}
	return data && typeof data === 'object' ? data : null;
};

const withRetry = async (task) => {
	const delays = cartRetryDelays.length ? cartRetryDelays : [];
	const attempts = delays.length + 1;
	let lastError = null;
	for (let attempt = 0; attempt < attempts; attempt += 1) {
		try {
			return await task();
		} catch (error) {
			lastError = error;
			if (error?.code === 'conflict' || attempt === attempts - 1) throw error;
			showCartNotice(
				cartMessage('retry', 'Conexión inestable. Reintentando…'),
				'info'
			);
			await sleep(delays[attempt] || 0);
		}
	}
	throw lastError;
};

const paintTotals = (root = document.querySelector('[data-cart-drawer]')) => {
	if (!root) return;
	let count = 0;
	let subtotal = 0;

	root.querySelectorAll('[data-cart-item]').forEach((row) => {
		const qty = Number(row.querySelector('[data-cart-item-input]')?.value || 0);
		const unit = Number(row.getAttribute('data-unit-price') || 0);
		count += qty;
		subtotal += unit * qty;
		const line = row.querySelector('[data-cart-item-total]');
		if (line) line.textContent = formatCartPrice(unit * qty);
	});

	document.querySelectorAll('[data-cart-drawer-badge], [data-cart-count]').forEach((el) => {
		el.textContent = String(count);
		el.hidden = count < 1;
	});

	const countEl = root.querySelector('[data-cart-drawer-count]');
	if (countEl) {
		countEl.textContent = String(count);
		countEl.hidden = count < 1;
	}

	const subtotalEl = root.querySelector('[data-cart-drawer-subtotal]');
	if (subtotalEl) subtotalEl.textContent = formatCartPrice(subtotal);

	const content = root.querySelector('[data-cart-drawer-content]');
	if (content) content.setAttribute('data-cart-total-count', String(count));
	if (window.wpbCart) window.wpbCart.cartCount = count;
};

const showEmptyState = (root) => {
	const content = root?.querySelector('[data-cart-drawer-content]');
	if (!content || content.querySelector('.cart-drawer__empty')) return;
	content.innerHTML = `
		<div class="cart-drawer__empty">
			<p class="cart-drawer__empty-message woocommerce-mini-cart__empty-message">${cartMessage('empty', 'Tu carrito está vacío.')}</p>
			<button class="cart-drawer__continue" type="button" data-cart-drawer-close>${cartMessage('continue', 'Seguir comprando')}</button>
		</div>
	`;
};

export const seedConfirmedCart = (root = document.querySelector('[data-cart-drawer]')) => {
	confirmedQty.clear();
	rowSnapshots.clear();
	root?.querySelectorAll('[data-cart-item]').forEach((row) => {
		const key = row.getAttribute('data-cart-item-key');
		if (!key) return;
		confirmedQty.set(key, Number(row.querySelector('[data-cart-item-input]')?.value || 0));
		rowSnapshots.set(key, row.outerHTML);
	});
};

const rowByKey = (root, key) => {
	const escaped = window.CSS && typeof window.CSS.escape === 'function' ? window.CSS.escape(key) : key;
	return root?.querySelector(`[data-cart-item-key="${escaped}"]`) || null;
};

/**
 * Put one line back to the last quantity the server confirmed.
 *
 * @param {string} key
 */
export const rollbackCartKey = (key) => {
	const root = document.querySelector('[data-cart-drawer]');
	if (!root) return;
	const qty = confirmedQty.get(key) || 0;
	let row = rowByKey(root, key);

	if (qty < 1) {
		row?.remove();
		if (!root.querySelector('[data-cart-item]')) showEmptyState(root);
		paintTotals(root);
		return;
	}

	if (!row && rowSnapshots.get(key)) {
		const content = root.querySelector('[data-cart-drawer-content]');
		const empty = content?.querySelector('.cart-drawer__empty');
		empty?.remove();
		let list = content?.querySelector('[data-cart-drawer-list]');
		if (!list && content) {
			content.insertAdjacentHTML(
				'afterbegin',
				'<ul class="cart-drawer__list woocommerce-mini-cart cart_list product_list_widget" data-cart-drawer-list></ul>'
			);
			list = content.querySelector('[data-cart-drawer-list]');
		}
		list?.insertAdjacentHTML('beforeend', rowSnapshots.get(key));
		row = rowByKey(root, key);
	}

	const input = row?.querySelector('[data-cart-item-input]');
	if (input) input.value = String(qty);
	paintTotals(root);
};

const acceptServerCart = (data, message) => {
	const fragments = data?.fragments || data?.data?.fragments;
	if (fragments) applyFragments(fragments);
	seedConfirmedCart();
	desiredQty.clear();
	showCartNotice(message, 'error');
};

const syncFromOtherTab = async () => {
	const data = await postCart('get_refreshed_fragments', new URLSearchParams());
	acceptServerCart(
		data,
		cartMessage('conflict', 'El carrito cambió en otra ventana. Mostramos la versión más reciente.')
	);
};

/**
 * POST a cart line quantity. Fragments are returned and not applied here.
 *
 * @param {string} cartItemKey
 * @param {number} quantity
 * @returns {Promise<Record<string, unknown> | null>}
 */
const updateCartItemQty = (cartItemKey, quantity) => {
	const cfg = getCartConfig();
	const body = new URLSearchParams();
	body.set('cart_item_key', cartItemKey);
	body.set('quantity', String(quantity));
	body.set('nonce', cfg.cartNonce || '');
	if (knownCartHash()) body.set('cart_hash', knownCartHash());
	return postCart('wpb_update_cart_item', body);
};

const postAddToCart = ({ productId, quantity }) => {
	const body = new URLSearchParams();
	body.set('product_id', String(productId));
	body.set('quantity', String(quantity));
	if (knownCartHash()) body.set('cart_hash', knownCartHash());
	return postCart('add_to_cart', body);
};

const noteConfirmed = (key, qty) => {
	const root = document.querySelector('[data-cart-drawer]');
	if (qty < 1) {
		confirmedQty.delete(key);
		rowSnapshots.delete(key);
		return;
	}
	confirmedQty.set(key, qty);
	const row = rowByKey(root, key);
	if (row) rowSnapshots.set(key, row.outerHTML);
};

const drainCartQueue = async () => {
	if (draining) return;
	draining = true;
	try {
		await acquireCartLock();
		if (foreignCartHash()) {
			await syncFromOtherTab();
			pendingAdds.splice(0).forEach((job) => job.resolve({ conflict: true }));
			return;
		}

		let last = null;
		let reconciled = false;
		let failed = false;
		while (desiredQty.size || pendingAdds.length) {
			if (desiredQty.size) {
				const key = desiredQty.keys().next().value;
				const qty = desiredQty.get(key);
				desiredQty.delete(key);
				try {
					last = await withRetry(() => updateCartItemQty(key, qty));
					noteConfirmed(key, qty);
				} catch (error) {
					if (error?.code === 'conflict') {
						reconciled = true;
						acceptServerCart(
							error.data,
							cartMessage(
								'conflict',
								'El carrito cambió en otra ventana. Mostramos la versión más reciente.'
							)
						);
						pendingAdds.splice(0).forEach((job) => job.reject(error));
						break;
					}
					failed = true;
					rollbackCartKey(key);
					showCartNotice(
						cartMessage(
							'rollback',
							'No se pudo actualizar el carrito. Se restauró la cantidad anterior.'
						),
						'error'
					);
				}
				continue;
			}

			const job = pendingAdds.shift();
			try {
				last = await withRetry(() => postAddToCart(job));
				last = last ? { ...last, added: true } : last;
				job.resolve(last);
			} catch (error) {
				if (error?.code === 'conflict') {
					reconciled = true;
					acceptServerCart(
						error.data,
						cartMessage(
							'conflict',
							'El carrito cambió en otra ventana. Mostramos la versión más reciente.'
						)
					);
					pendingAdds.splice(0).forEach((queued) => queued.reject(error));
					break;
				} else {
					failed = true;
					showCartNotice(
						cartMessage('addError', 'No se pudo agregar el producto. Intenta de nuevo.'),
						'error'
					);
				}
				job.reject(error);
			}
		}

		if (!reconciled && desiredQty.size === 0 && pendingAdds.length === 0 && last?.fragments) {
			applyFragments(last.fragments);
			seedConfirmedCart();
			if (last.added && window.jQuery) {
				window.jQuery(document.body).trigger('added_to_cart', [last.fragments, last.cart_hash, null]);
			}
			if (!failed) showCartNotice('');
		}
	} catch (error) {
		console.error(error);
	} finally {
		releaseCartLock();
		draining = false;
		if (desiredQty.size || pendingAdds.length) drainCartQueue();
	}
};

const kickCartQueue = () => {
	if (draining) return;
	queueMicrotask(() => {
		drainCartQueue();
	});
};

const queueQty = (key, qty) => {
	desiredQty.set(key, qty);
	kickCartQueue();
};

export const addToCartAjax = ({ productId, quantity = 1 }) => {
	const request = { productId, quantity };
	if (!cartQueueBound) {
		return withRetry(() => postAddToCart(request)).then((data) => {
			if (data?.fragments) {
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
		});
	}

	return new Promise((resolve, reject) => {
		pendingAdds.push({ productId, quantity, resolve, reject });
		kickCartQueue();
	});
};

/** Clears in-memory queue state so a hung request cannot block the next one. */
export const resetCartQueueForTests = () => {
	desiredQty.clear();
	confirmedQty.clear();
	rowSnapshots.clear();
	pendingAdds.splice(0, pendingAdds.length);
	draining = false;
	try {
		localStorage.removeItem(CART_LOCK_KEY);
		localStorage.removeItem(CART_HASH_KEY);
	} catch (error) {
		/* ignore */
	}
};

const bindCartQueue = () => {
	if (cartQueueBound || typeof window === 'undefined') return;
	cartQueueBound = true;
	window.addEventListener('storage', (event) => {
		if (event.key !== CART_HASH_KEY || !event.newValue || event.newValue === knownCartHash()) return;
		if (draining) return;
		syncFromOtherTab().catch((error) => {
			console.error(error);
		});
	});
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
		bindCartQueue();
		seedConfirmedCart(root);

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

		const onBodyClick = (event) => {
			const decrease = event.target.closest('[data-cart-item-decrease]');
			const increase = event.target.closest('[data-cart-item-increase]');
			const remove = event.target.closest('[data-cart-item-remove]');
			if (!decrease && !increase && !remove) return;

			event.preventDefault();
			const item = event.target.closest('[data-cart-item]');
			if (!item) return;

			const key = item.getAttribute('data-cart-item-key');
			if (!key) return;

			const input = item.querySelector('[data-cart-item-input]');
			let qty = Number(input?.value || 1);

			if (remove) {
				qty = 0;
			} else if (decrease) {
				qty = Math.max(0, qty - 1);
			} else if (increase) {
				qty = Math.min(99, qty + 1);
			}

			if (qty < 1) {
				item.remove();
				paintTotals(root);
				if (!root.querySelector('[data-cart-item]')) showEmptyState(root);
			} else if (input) {
				input.value = String(qty);
				paintTotals(root);
			}

			queueQty(key, qty);
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
				if (draining || desiredQty.size) {
					desiredQty.forEach((qty, key) => {
						const row = rowByKey(root, key);
						const input = row?.querySelector('[data-cart-item-input]');
						if (input && qty > 0) input.value = String(qty);
					});
					paintTotals(root);
					return;
				}
				seedConfirmedCart(root);
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
