import { afterEach, beforeEach, describe, expect, it, vi } from 'vitest';
import cartDrawer, {
	addToCartAjax,
	applyFragments,
	closeCartDrawer,
	openCartDrawer,
	parseProductQtys,
	syncShowcaseWithCart,
	wcAjaxUrl,
} from './cartDrawer.js';

describe('cartDrawer helpers', () => {
	beforeEach(() => {
		document.body.innerHTML = '';
		window.wpbCart = {
			ajaxUrl: '/?wc-ajax=%%endpoint%%',
			wcAjaxUrl: '/?wc-ajax=',
			cartNonce: 'test-nonce',
			cartCount: 0,
			productQtys: {},
		};
	});

	afterEach(() => {
		vi.unstubAllGlobals();
		delete window.wpbCart;
		document.body.innerHTML = '';
		document.body.className = '';
	});

	it('builds WC ajax URLs from localized config', () => {
		expect(wcAjaxUrl('add_to_cart')).toBe('/?wc-ajax=add_to_cart');
		expect(wcAjaxUrl('wpb_update_cart_item')).toBe('/?wc-ajax=wpb_update_cart_item');
	});

	it('falls back to wcAjaxUrl when ajaxUrl template is missing', () => {
		window.wpbCart = { wcAjaxUrl: 'https://example.test/?wc-ajax=' };
		expect(wcAjaxUrl('add_to_cart')).toBe('https://example.test/?wc-ajax=add_to_cart');
	});

	it('parses product qty maps and recovers from invalid JSON', () => {
		const content = document.createElement('div');
		content.setAttribute('data-cart-product-qtys', '{"164":2,"180":1}');
		expect(parseProductQtys(content)).toEqual({ 164: 2, 180: 1 });

		content.setAttribute('data-cart-product-qtys', '{bad');
		expect(parseProductQtys(content)).toEqual({});

		content.setAttribute('data-cart-product-qtys', '[]');
		expect(parseProductQtys(content)).toEqual({});

		expect(parseProductQtys(null)).toEqual({});
	});

	it('opens and closes the drawer with aria and body lock', () => {
		document.body.innerHTML = `
			<button type="button" data-cart-drawer-open aria-expanded="false">Cart</button>
			<aside data-cart-drawer aria-hidden="true" id="cart-drawer">
				<button type="button" data-cart-drawer-close class="cart-drawer__close">Close</button>
			</aside>
		`;

		openCartDrawer();
		const root = document.querySelector('[data-cart-drawer]');
		expect(root.classList.contains('cart-drawer--open')).toBe(true);
		expect(root.getAttribute('aria-hidden')).toBe('false');
		expect(document.body.classList.contains('cart-drawer-open')).toBe(true);
		expect(document.querySelector('[data-cart-drawer-open]').getAttribute('aria-expanded')).toBe(
			'true'
		);

		closeCartDrawer();
		expect(root.classList.contains('cart-drawer--open')).toBe(false);
		expect(root.getAttribute('aria-hidden')).toBe('true');
		expect(document.body.classList.contains('cart-drawer-open')).toBe(false);
		expect(document.querySelector('[data-cart-drawer-open]').getAttribute('aria-expanded')).toBe(
			'false'
		);
	});

	it('syncs showcase qty and cart counters from drawer content data', () => {
		document.body.innerHTML = `
			<span data-cart-drawer-badge>0</span>
			<span data-cart-count hidden>0</span>
			<div data-product-showcase data-product-id="164">
				<input data-product-qty-input type="number" value="1" />
			</div>
			<div
				data-cart-drawer-content
				data-cart-total-count="3"
				data-cart-product-qtys='{"164":3}'
			></div>
		`;

		syncShowcaseWithCart();

		const count = document.querySelector('[data-cart-count]');
		expect(count.textContent).toBe('3');
		expect(count.hidden).toBe(false);
		expect(document.querySelector('[data-product-qty-input]').value).toBe('3');
		expect(window.wpbCart.cartCount).toBe(3);
		expect(window.wpbCart.productQtys).toEqual({ 164: 3 });
	});

	it('applies fragments, ignores invalid selectors, and dispatches update event', () => {
		document.body.innerHTML = `
			<span data-cart-drawer-badge>0</span>
			<div data-cart-drawer-content data-cart-total-count="0" data-cart-product-qtys="{}"></div>
		`;

		const updated = vi.fn();
		document.addEventListener('wpb_cart_updated', updated);

		applyFragments({
			'[data-cart-drawer-badge]': '<span data-cart-drawer-badge>2</span>',
			'[data-cart-drawer-content]':
				'<div data-cart-drawer-content data-cart-total-count="2" data-cart-product-qtys="{}"></div>',
			'::invalid(': '<div>nope</div>',
			wpb_cart_product_qtys: 'ignore',
		});

		expect(document.querySelector('[data-cart-drawer-badge]').textContent).toBe('2');
		expect(document.querySelector('[data-cart-drawer-content]').getAttribute('data-cart-total-count')).toBe(
			'2'
		);
		expect(updated).toHaveBeenCalledTimes(1);
	});

	it('posts add-to-cart and applies returned fragments', async () => {
		document.body.innerHTML = `
			<span data-cart-drawer-badge>0</span>
			<div data-cart-drawer-content data-cart-total-count="0" data-cart-product-qtys="{}"></div>
		`;

		const fetchMock = vi.fn().mockResolvedValue({
			json: async () => ({
				fragments: {
					'[data-cart-drawer-badge]': '<span data-cart-drawer-badge>1</span>',
				},
				cart_hash: 'abc',
			}),
		});
		vi.stubGlobal('fetch', fetchMock);

		await addToCartAjax({ productId: 164, quantity: 2 });

		expect(fetchMock).toHaveBeenCalledTimes(1);
		const [url, options] = fetchMock.mock.calls[0];
		expect(url).toBe('/?wc-ajax=add_to_cart');
		expect(options.method).toBe('POST');
		expect(String(options.body)).toContain('product_id=164');
		expect(String(options.body)).toContain('quantity=2');
		expect(document.querySelector('[data-cart-drawer-badge]').textContent).toBe('1');
	});

	it('wires open/close via data attributes and Escape', () => {
		document.body.innerHTML = `
			<button type="button" class="main-menu__cart" data-cart-drawer-open>Open</button>
			<aside class="cart-drawer" id="cart-drawer" data-cart-drawer aria-hidden="true">
				<div class="cart-drawer__backdrop" data-cart-drawer-close></div>
				<div data-cart-drawer-panel>
					<button type="button" class="cart-drawer__close" data-cart-drawer-close>X</button>
					<div data-cart-drawer-content data-cart-total-count="0" data-cart-product-qtys="{}"></div>
				</div>
			</aside>
		`;

		cartDrawer(document);

		const root = document.querySelector('[data-cart-drawer]');
		const openBtn = document.querySelector('[data-cart-drawer-open]');
		openBtn.click();
		expect(root.classList.contains('cart-drawer--open')).toBe(true);

		document.dispatchEvent(new KeyboardEvent('keydown', { key: 'Escape' }));
		expect(root.classList.contains('cart-drawer--open')).toBe(false);

		openBtn.click();
		root.querySelector('.cart-drawer__close').click();
		expect(root.classList.contains('cart-drawer--open')).toBe(false);
	});
});
