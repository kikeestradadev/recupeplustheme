import { afterEach, beforeEach, describe, expect, it, vi } from 'vitest';
import productShowcase, { clamp } from './productShowcase.js';

describe('productShowcase', () => {
	beforeEach(() => {
		document.body.innerHTML = '';
		window.wpbCart = {
			ajaxUrl: '/?wc-ajax=%%endpoint%%',
			cartNonce: 'n',
		};
		window.matchMedia = vi.fn().mockImplementation((query) => ({
			matches: String(query).includes('960') ? true : false,
			media: query,
			addEventListener: vi.fn(),
			removeEventListener: vi.fn(),
			addListener: vi.fn(),
			removeListener: vi.fn(),
		}));
	});

	afterEach(() => {
		vi.unstubAllGlobals();
		delete window.wpbCart;
		document.body.innerHTML = '';
	});

	it('clamps qty values into the allowed range', () => {
		expect(clamp(5, 1, 99)).toBe(5);
		expect(clamp(0, 1, 99)).toBe(1);
		expect(clamp(200, 1, 99)).toBe(99);
		expect(clamp(-3, 0, 10)).toBe(0);
	});

	it('switches gallery thumbs and stage image', () => {
		document.body.innerHTML = `
			<section data-product-showcase data-product-id="164">
				<button type="button" data-product-thumb data-src="/a.webp" aria-label="Ver A" class="product-showcase__thumb product-showcase__thumb--active" aria-pressed="true"></button>
				<button type="button" data-product-thumb data-src="/b.webp" aria-label="Ver B" class="product-showcase__thumb" aria-pressed="false"></button>
				<div data-product-zoom>
					<img data-product-stage src="/a.webp" alt="A" />
				</div>
			</section>
		`;

		productShowcase(document);
		const thumbs = document.querySelectorAll('[data-product-thumb]');
		thumbs[1].click();

		const stage = document.querySelector('[data-product-stage]');
		expect(stage.getAttribute('src')).toBe('/b.webp');
		expect(thumbs[1].classList.contains('product-showcase__thumb--active')).toBe(true);
		expect(thumbs[0].getAttribute('aria-pressed')).toBe('false');
		expect(thumbs[1].getAttribute('aria-pressed')).toBe('true');
	});

	it('increments and decrements qty with min/max bounds', () => {
		document.body.innerHTML = `
			<section data-product-showcase data-product-id="164">
				<button type="button" data-product-qty-dec>−</button>
				<input data-product-qty-input type="number" min="1" max="3" value="1" />
				<button type="button" data-product-qty-inc>+</button>
			</section>
		`;

		productShowcase(document);
		const input = document.querySelector('[data-product-qty-input]');
		document.querySelector('[data-product-qty-dec]').click();
		expect(input.value).toBe('1');
		document.querySelector('[data-product-qty-inc]').click();
		document.querySelector('[data-product-qty-inc]').click();
		document.querySelector('[data-product-qty-inc]').click();
		expect(input.value).toBe('3');
	});

	it('activates info tabs and panels', () => {
		document.body.innerHTML = `
			<section data-product-showcase data-product-id="164">
				<button type="button" data-product-tab="desc" class="product-showcase__tab product-showcase__tab--active" aria-selected="true"></button>
				<button type="button" data-product-tab="info" class="product-showcase__tab" aria-selected="false"></button>
				<div data-product-panel="desc" class="product-showcase__panel product-showcase__panel--active"></div>
				<div data-product-panel="info" class="product-showcase__panel" hidden></div>
			</section>
		`;

		productShowcase(document);
		document.querySelector('[data-product-tab="info"]').click();

		expect(
			document.querySelector('[data-product-tab="info"]').classList.contains('product-showcase__tab--active')
		).toBe(true);
		expect(document.querySelector('[data-product-panel="info"]').hidden).toBe(false);
		expect(document.querySelector('[data-product-panel="desc"]').hidden).toBe(true);
	});

	it('submits add-to-cart via AJAX with current qty', async () => {
		document.body.innerHTML = `
			<section data-product-showcase data-product-id="164">
				<form data-product-cart>
					<input data-product-qty-input type="number" min="1" max="99" value="2" />
					<button type="submit" class="product-showcase__add" name="add-to-cart" value="164">Add</button>
				</form>
				<aside data-cart-drawer aria-hidden="true"></aside>
			</section>
		`;

		const fetchMock = vi.fn().mockResolvedValue({
			json: async () => ({ fragments: {}, cart_hash: 'h' }),
		});
		vi.stubGlobal('fetch', fetchMock);

		productShowcase(document);
		const form = document.querySelector('[data-product-cart]');
		form.dispatchEvent(new Event('submit', { bubbles: true, cancelable: true }));

		await vi.waitFor(() => expect(fetchMock).toHaveBeenCalled());
		expect(String(fetchMock.mock.calls[0][1].body)).toContain('product_id=164');
		expect(String(fetchMock.mock.calls[0][1].body)).toContain('quantity=2');
		await vi.waitFor(() => {
			expect(
				document.querySelector('[data-cart-drawer]').classList.contains('cart-drawer--open')
			).toBe(true);
		});
	});
});
