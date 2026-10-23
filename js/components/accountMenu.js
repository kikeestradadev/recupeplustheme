const accountMenuInstances = new WeakMap();

/**
 * Endpoints the logged-in header menu must expose.
 * Orders, billing addresses, and account details stay on the My Account page.
 */
export const LOGGED_IN_ACCOUNT_ENDPOINTS = [
	'orders',
	'edit-address',
	'edit-account',
	'customer-logout',
];

/**
 * Classify a WooCommerce order without querying the database.
 * A positive customer id is the account. Guest orders keep the billing email.
 *
 * @param {{ customerId?: number, customer_id?: number, billingEmail?: string, billing_email?: string }} order
 */
export const orderCustomerLink = (order = {}) => {
	const rawId = order.customerId ?? order.customer_id ?? 0;
	const customerId = Number(rawId);

	if (Number.isFinite(customerId) && customerId > 0) {
		return { type: 'customer', customerId };
	}

	const billingEmail = String(order.billingEmail ?? order.billing_email ?? '')
		.trim()
		.toLowerCase();

	if (billingEmail) {
		return { type: 'guest', billingEmail };
	}

	return { type: 'none' };
};

/**
 * Group order ids by account or guest email. Header code must not call this on load.
 *
 * @param {Array<{ id?: number|string, customerId?: number, customer_id?: number, billingEmail?: string, billing_email?: string }>} orders
 */
export const groupOrdersByCustomer = (orders = []) => {
	const groups = {};

	orders.forEach((order) => {
		const link = orderCustomerLink(order);
		const key =
			link.type === 'customer'
				? `customer:${link.customerId}`
				: link.type === 'guest'
					? `guest:${link.billingEmail}`
					: 'none';

		if (!groups[key]) groups[key] = [];
		groups[key].push(order.id ?? null);
	});

	return groups;
};

/**
 * @param {ParentNode} root
 * @returns {string[]}
 */
export const missingAccountEndpoints = (root) => {
	if (!root) return [...LOGGED_IN_ACCOUNT_ENDPOINTS];

	const present = new Set(
		[...root.querySelectorAll('[data-account-endpoint]')].map((el) =>
			el.getAttribute('data-account-endpoint')
		)
	);

	return LOGGED_IN_ACCOUNT_ENDPOINTS.filter((endpoint) => !present.has(endpoint));
};

/**
 * The account page is the WooCommerce shortcode, not a second copy in the header.
 *
 * @param {string} content
 */
export const accountPageUsesMyAccount = (content) =>
	String(content).includes('[woocommerce_my_account]');

const closeCartDrawerShell = () => {
	const cart = document.querySelector('[data-cart-drawer]');
	if (!cart) return;
	cart.classList.remove('cart-drawer--open');
	cart.setAttribute('aria-hidden', 'true');
	document.body.classList.remove('cart-drawer-open');
	document.querySelectorAll('[data-cart-drawer-open]').forEach((trigger) => {
		trigger.setAttribute('aria-expanded', 'false');
	});
};

const accountMenu = (scope = document) => {
	const cleanups = [];
	const roots = scope.querySelectorAll('[data-account-drawer]');

	roots.forEach((root) => {
		if (accountMenuInstances.has(root)) return;

		const panel = root.querySelector('[data-account-drawer-panel]');
		const controller = new AbortController();
		const { signal } = controller;
		let lastFocus = null;

		const setOpen = (open) => {
			root.classList.toggle('account-drawer--open', open);
			root.setAttribute('aria-hidden', open ? 'false' : 'true');
			document.body.classList.toggle('account-drawer-open', open);

			document.querySelectorAll('[data-account-menu-toggle]').forEach((trigger) => {
				trigger.setAttribute('aria-expanded', open ? 'true' : 'false');
				if (!trigger.getAttribute('aria-controls') && root.id) {
					trigger.setAttribute('aria-controls', root.id);
				}
			});

			if (open) {
				closeCartDrawerShell();
				lastFocus = document.activeElement;
				const focusTarget =
					root.querySelector('[data-account-drawer-close].account-drawer__close') || panel;
				window.requestAnimationFrame(() => focusTarget?.focus?.());
			} else if (lastFocus && typeof lastFocus.focus === 'function') {
				lastFocus.focus();
				lastFocus = null;
			}
		};

		document.querySelectorAll('[data-account-menu-toggle]').forEach((trigger) => {
			trigger.setAttribute('aria-haspopup', 'dialog');
			if (root.getAttribute('aria-hidden') !== 'false') {
				trigger.setAttribute('aria-expanded', 'false');
			}
			if (root.id) trigger.setAttribute('aria-controls', root.id);
			trigger.addEventListener(
				'click',
				(event) => {
					event.preventDefault();
					setOpen(true);
				},
				{ signal }
			);
		});

		root.addEventListener(
			'click',
			(event) => {
				if (event.target.closest('[data-account-drawer-close]')) {
					event.preventDefault();
					setOpen(false);
				}
			},
			{ signal }
		);

		document.addEventListener(
			'keydown',
			(event) => {
				if (event.key === 'Escape' && root.classList.contains('account-drawer--open')) {
					setOpen(false);
				}
			},
			{ signal }
		);

		const destroy = () => {
			controller.abort();
			accountMenuInstances.delete(root);
		};

		accountMenuInstances.set(root, destroy);
		cleanups.push(destroy);
	});

	return () => cleanups.forEach((destroy) => destroy());
};

export default accountMenu;
