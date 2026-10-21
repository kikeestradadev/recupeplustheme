import { afterEach, beforeEach, describe, expect, it, vi } from 'vitest';
import cartDrawer, {
	addToCartAjax,
	applyFragments,
	cartRetryDelays,
	closeCartDrawer,
	formatCartPrice,
	openCartDrawer,
	parseProductQtys,
	resetCartQueueForTests,
	syncShowcaseWithCart,
	wcAjaxUrl,
} from './cartDrawer.js';

describe('cartDrawer helpers', () => {
	beforeEach(() => {
		resetCartQueueForTests();
		cartRetryDelays.splice(0, cartRetryDelays.length, 350, 900);
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

	it('formats CRC prices with a space as thousands separator', () => {
		expect(formatCartPrice(8900)).toBe('₡8 900');
		expect(formatCartPrice(44500)).toBe('₡44 500');
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

	it('applies every rapid increase immediately and sends only the latest qty', async () => {
		document.body.innerHTML = `
			<span data-cart-drawer-badge>2</span>
			<aside id="cart-drawer" data-cart-drawer aria-hidden="true">
				<div data-cart-drawer-content data-cart-total-count="2" data-cart-product-qtys='{"164":2}'>
					<ul>
						<li data-cart-item data-cart-item-key="line-1" data-unit-price="8900">
							<button type="button" data-cart-item-increase>+</button>
							<input data-cart-item-input value="2" />
							<span data-cart-item-total>₡17 800</span>
						</li>
					</ul>
					<span data-cart-drawer-subtotal>₡17 800</span>
					<span data-cart-drawer-count>2</span>
				</div>
			</aside>
		`;

		const fetchMock = vi.fn(
			() =>
				new Promise(() => {
					/* stay in flight so later clicks cannot be blocked */
				})
		);
		vi.stubGlobal('fetch', fetchMock);

		cartDrawer(document);
		const increase = document.querySelector('[data-cart-item-increase]');
		increase.click();
		increase.click();
		increase.click();

		expect(document.querySelector('[data-cart-item-input]').value).toBe('5');
		expect(document.querySelector('[data-cart-drawer-badge]').textContent).toBe('5');
		expect(document.querySelector('[data-cart-drawer-subtotal]').textContent).toBe('₡44 500');
		expect(document.querySelector('[data-cart-item-total]').textContent).toBe('₡44 500');

		await vi.waitFor(() => expect(fetchMock).toHaveBeenCalledTimes(1));
		expect(String(fetchMock.mock.calls[0][1].body)).toContain('quantity=5');
	});

	it('sends qty updates for different products one after another', async () => {
		document.body.innerHTML = `
			<span data-cart-drawer-badge>2</span>
			<aside id="cart-drawer" data-cart-drawer aria-hidden="true">
				<div data-cart-drawer-content data-cart-total-count="2" data-cart-product-qtys="{}">
					<ul>
						<li data-cart-item data-cart-item-key="line-a" data-unit-price="8900">
							<a class="cart-drawer__name">Vitamin</a>
							<button type="button" data-cart-item-increase>+</button>
							<input data-cart-item-input value="1" />
							<span data-cart-item-total>₡8 900</span>
						</li>
						<li data-cart-item data-cart-item-key="line-b" data-unit-price="15000">
							<a class="cart-drawer__name">Are More</a>
							<button type="button" data-cart-item-increase>+</button>
							<input data-cart-item-input value="1" />
							<span data-cart-item-total>₡15 000</span>
						</li>
					</ul>
					<span data-cart-drawer-subtotal>₡23 900</span>
				</div>
			</aside>
		`;

		const resolvers = [];
		const fetchMock = vi.fn(
			() =>
				new Promise((resolve) => {
					resolvers.push(resolve);
				})
		);
		vi.stubGlobal('fetch', fetchMock);
		cartDrawer(document);

		const [first, second] = document.querySelectorAll('[data-cart-item-increase]');
		first.click();
		first.click();
		first.click();
		second.click();
		second.click();
		second.click();

		expect(document.querySelectorAll('[data-cart-item-input]')[0].value).toBe('4');
		expect(document.querySelectorAll('[data-cart-item-input]')[1].value).toBe('4');
		expect(document.querySelector('[data-cart-drawer-badge]').textContent).toBe('8');

		await vi.waitFor(() => expect(fetchMock).toHaveBeenCalledTimes(1));
		expect(String(fetchMock.mock.calls[0][1].body)).toContain('cart_item_key=line-a');
		expect(String(fetchMock.mock.calls[0][1].body)).toContain('quantity=4');

		resolvers[0]({
			json: async () => ({
				fragments: {
					'[data-cart-drawer-badge]': '<span data-cart-drawer-badge>1</span>',
				},
			}),
		});
		await vi.waitFor(() => expect(fetchMock).toHaveBeenCalledTimes(2));
		expect(document.querySelector('[data-cart-drawer-badge]').textContent).toBe('8');
		expect(String(fetchMock.mock.calls[1][1].body)).toContain('cart_item_key=line-b');
		expect(String(fetchMock.mock.calls[1][1].body)).toContain('quantity=4');

		resolvers[1]({
			json: async () => ({
				fragments: {
					'[data-cart-drawer-badge]': '<span data-cart-drawer-badge>8</span>',
				},
			}),
		});
		await vi.waitFor(() => {
			expect(document.querySelector('[data-cart-drawer-badge]').textContent).toBe('8');
		});
	});

	it('retries a dropped request, tells the user, and keeps the new quantity', async () => {
		cartRetryDelays.splice(0, cartRetryDelays.length, 0, 0);
		document.body.innerHTML = `
			<span data-cart-drawer-badge>2</span>
			<aside id="cart-drawer" data-cart-drawer aria-hidden="true">
				<div data-cart-drawer-panel>
					<p data-cart-drawer-notice hidden></p>
					<div data-cart-drawer-content data-cart-total-count="2" data-cart-product-qtys="{}">
						<ul>
							<li data-cart-item data-cart-item-key="line-1" data-unit-price="8900">
								<button type="button" data-cart-item-increase>+</button>
								<input data-cart-item-input value="2" />
								<span data-cart-item-total>₡17 800</span>
							</li>
						</ul>
						<span data-cart-drawer-subtotal>₡17 800</span>
					</div>
				</div>
			</aside>
		`;

		let calls = 0;
		let releaseRetry;
		vi.stubGlobal(
			'fetch',
			vi.fn(() => {
				calls += 1;
				if (calls === 1) return Promise.reject(new TypeError('offline'));
				return new Promise((resolve) => {
					releaseRetry = resolve;
				});
			})
		);

		cartDrawer(document);
		document.querySelector('[data-cart-item-increase]').click();

		await vi.waitFor(() => {
			expect(document.querySelector('[data-cart-drawer-notice]').textContent).toContain('Reintentando');
		});
		expect(calls).toBe(2);
		expect(document.querySelector('[data-cart-item-input]').value).toBe('3');

		releaseRetry({
			json: async () => ({
				fragments: {
					'[data-cart-drawer-badge]': '<span data-cart-drawer-badge>3</span>',
				},
				cart_hash: 'ok',
			}),
		});
		await vi.waitFor(() => {
			expect(document.querySelector('[data-cart-drawer-notice]').hidden).toBe(true);
		});
		expect(document.querySelector('[data-cart-item-input]').value).toBe('3');
		expect(document.querySelector('[data-cart-drawer-badge]').textContent).toBe('3');
	});

	it('restores quantity, badge and subtotal when every retry fails', async () => {
		cartRetryDelays.splice(0, cartRetryDelays.length, 0, 0);
		document.body.innerHTML = `
			<span data-cart-drawer-badge>2</span>
			<aside id="cart-drawer" data-cart-drawer aria-hidden="true">
				<div data-cart-drawer-panel>
					<p data-cart-drawer-notice hidden></p>
					<div data-cart-drawer-content data-cart-total-count="2" data-cart-product-qtys="{}">
						<ul>
							<li data-cart-item data-cart-item-key="line-1" data-unit-price="8900">
								<button type="button" data-cart-item-increase>+</button>
								<input data-cart-item-input value="2" />
								<span data-cart-item-total>₡17 800</span>
							</li>
						</ul>
						<span data-cart-drawer-subtotal>₡17 800</span>
					</div>
				</div>
			</aside>
		`;

		const fetchMock = vi.fn(() => Promise.reject(new TypeError('offline')));
		vi.stubGlobal('fetch', fetchMock);
		cartDrawer(document);
		const increase = document.querySelector('[data-cart-item-increase]');
		increase.click();
		increase.click();
		increase.click();

		await vi.waitFor(() => {
			expect(document.querySelector('[data-cart-drawer-notice]').textContent).toContain(
				'Se restauró la cantidad anterior'
			);
		});
		expect(fetchMock.mock.calls.length).toBe(3);
		expect(document.querySelector('[data-cart-item-input]').value).toBe('2');
		expect(document.querySelector('[data-cart-drawer-badge]').textContent).toBe('2');
		expect(document.querySelector('[data-cart-drawer-subtotal]').textContent).toBe('₡17 800');
		expect(document.querySelector('[data-cart-item-total]').textContent).toBe('₡17 800');
	});

	it('does not retry a cart conflict and shows the server cart', async () => {
		document.body.innerHTML = `
			<span data-cart-drawer-badge>2</span>
			<aside id="cart-drawer" data-cart-drawer aria-hidden="true">
				<div data-cart-drawer-panel>
					<p data-cart-drawer-notice hidden></p>
					<div data-cart-drawer-content data-cart-total-count="2" data-cart-product-qtys="{}">
						<ul data-cart-drawer-list>
							<li data-cart-item data-cart-item-key="line-1" data-unit-price="8900">
								<button type="button" data-cart-item-increase>+</button>
								<input data-cart-item-input value="2" />
								<span data-cart-item-total>₡17 800</span>
							</li>
						</ul>
						<span data-cart-drawer-subtotal>₡17 800</span>
					</div>
				</div>
			</aside>
		`;

		window.wpbCart.cartHash = 'tab-a';
		const fetchMock = vi.fn().mockResolvedValue({
			ok: false,
			status: 409,
			json: async () => ({
				code: 'cart_conflict',
				cart_hash: 'tab-b',
				fragments: {
					'[data-cart-drawer-badge]': '<span data-cart-drawer-badge>2</span>',
					'[data-cart-drawer-content]': `
						<div data-cart-drawer-content data-cart-total-count="2" data-cart-product-qtys="{}">
							<ul data-cart-drawer-list>
								<li data-cart-item data-cart-item-key="line-1" data-unit-price="8900">
									<button type="button" data-cart-item-increase>+</button>
									<input data-cart-item-input value="2" />
									<span data-cart-item-total>₡17 800</span>
								</li>
							</ul>
							<span data-cart-drawer-subtotal>₡17 800</span>
						</div>
					`,
				},
			}),
		});
		vi.stubGlobal('fetch', fetchMock);
		cartDrawer(document);
		document.querySelector('[data-cart-item-increase]').click();

		await vi.waitFor(() => {
			expect(document.querySelector('[data-cart-drawer-notice]').textContent).toContain('otra ventana');
		});
		expect(fetchMock).toHaveBeenCalledTimes(1);
		expect(document.querySelector('[data-cart-item-input]').value).toBe('2');
		expect(document.querySelector('[data-cart-drawer-badge]').textContent).toBe('2');
		expect(document.querySelector('[data-cart-drawer-subtotal]').textContent).toBe('₡17 800');
	});

	it('waits to add a product until the in-flight quantity update finishes', async () => {
		document.body.innerHTML = `
			<span data-cart-drawer-badge>1</span>
			<button type="button" data-wpb-add-to-cart data-product-id="180">Add</button>
			<aside id="cart-drawer" data-cart-drawer aria-hidden="true">
				<div data-cart-drawer-panel>
					<div data-cart-drawer-content data-cart-total-count="1" data-cart-product-qtys="{}">
						<ul>
							<li data-cart-item data-cart-item-key="line-1" data-unit-price="8900">
								<button type="button" data-cart-item-increase>+</button>
								<input data-cart-item-input value="1" />
								<span data-cart-item-total>₡8 900</span>
							</li>
						</ul>
						<span data-cart-drawer-subtotal>₡8 900</span>
					</div>
				</div>
			</aside>
		`;

		let releaseUpdate;
		const fetchMock = vi.fn((url) => {
			if (String(url).includes('wpb_update_cart_item')) {
				return new Promise((resolve) => {
					releaseUpdate = resolve;
				});
			}
			return Promise.resolve({
				json: async () => ({
					fragments: {
						'[data-cart-drawer-badge]': '<span data-cart-drawer-badge>3</span>',
					},
					cart_hash: 'added',
				}),
			});
		});
		vi.stubGlobal('fetch', fetchMock);
		cartDrawer(document);
		document.querySelector('[data-cart-item-increase]').click();
		await vi.waitFor(() => expect(fetchMock).toHaveBeenCalledTimes(1));

		const added = addToCartAjax({ productId: 180, quantity: 1 });
		await Promise.resolve();
		await Promise.resolve();
		expect(fetchMock).toHaveBeenCalledTimes(1);

		releaseUpdate({
			json: async () => ({
				fragments: {
					'[data-cart-drawer-badge]': '<span data-cart-drawer-badge>2</span>',
				},
				cart_hash: 'qty',
			}),
		});
		await added;
		expect(fetchMock).toHaveBeenCalledTimes(2);
		expect(String(fetchMock.mock.calls[1][0])).toContain('add_to_cart');
		expect(document.querySelector('[data-cart-drawer-badge]').textContent).toBe('3');
	});
});
