import { afterEach, describe, expect, it, vi } from 'vitest';
import accountMenu, {
	accountPageUsesMyAccount,
	groupOrdersByCustomer,
	missingAccountEndpoints,
	orderCustomerLink,
} from './accountMenu.js';

const loggedOutMarkup = () => `
	<button type="button" data-account-menu-toggle aria-expanded="false">Cuenta</button>
	<aside id="account-drawer" data-account-drawer data-account-state="logged-out" aria-hidden="true">
		<div class="account-drawer__backdrop" data-account-drawer-close></div>
		<div data-account-drawer-panel>
			<button type="button" class="account-drawer__close" data-account-drawer-close>Cerrar</button>
			<form class="woocommerce-form woocommerce-form-login login" method="post"></form>
			<p>Aún no tienes una cuenta</p>
			<a data-account-register href="/crear-cuenta/">Crear una cuenta</a>
		</div>
	</aside>
`;

const loggedInMarkup = () => `
	<button type="button" data-account-menu-toggle aria-expanded="false">Cuenta</button>
	<aside id="account-drawer" data-account-drawer data-account-state="logged-in" aria-hidden="true">
		<div class="account-drawer__backdrop" data-account-drawer-close></div>
		<div data-account-drawer-panel>
			<button type="button" class="account-drawer__close" data-account-drawer-close>Cerrar</button>
			<a data-account-link data-account-endpoint="orders" href="/my-account/orders/">Pedidos</a>
			<a data-account-link data-account-endpoint="edit-address" href="/my-account/edit-address/">Direcciones</a>
			<a data-account-link data-account-endpoint="edit-account" href="/my-account/edit-account/">Detalles de la cuenta</a>
			<a data-account-link data-account-endpoint="customer-logout" href="/my-account/customer-logout/">Salir</a>
		</div>
	</aside>
`;

describe('accountMenu', () => {
	afterEach(() => {
		document.body.innerHTML = '';
		document.body.className = '';
		vi.restoreAllMocks();
	});

	it('shows login and a link to the registration page when logged out', () => {
		document.body.innerHTML = loggedOutMarkup();
		accountMenu();

		const root = document.querySelector('[data-account-drawer]');

		expect(root.getAttribute('data-account-state')).toBe('logged-out');
		expect(root.querySelector('.woocommerce-form-login')).not.toBeNull();
		expect(root.querySelector('.woocommerce-form-register')).toBeNull();
		expect(root.querySelector('[data-account-register]').getAttribute('href')).toBe('/crear-cuenta/');
		expect(root.getAttribute('aria-hidden')).toBe('true');
		expect(document.querySelector('[data-account-menu-toggle]').getAttribute('aria-expanded')).toBe(
			'false'
		);
	});

	it('shows account links and no register form when logged in', () => {
		document.body.innerHTML = loggedInMarkup();
		accountMenu();

		const root = document.querySelector('[data-account-drawer]');
		expect(root.getAttribute('data-account-state')).toBe('logged-in');
		expect(root.querySelector('.woocommerce-form-register')).toBeNull();
		expect(root.querySelector('.woocommerce-form-login')).toBeNull();
		expect(missingAccountEndpoints(root)).toEqual([]);
	});

	it('opens the drawer from the header button the same way as the cart', () => {
		document.body.innerHTML = loggedOutMarkup();
		accountMenu();

		const toggle = document.querySelector('[data-account-menu-toggle]');
		const root = document.querySelector('[data-account-drawer]');

		toggle.click();
		expect(root.classList.contains('account-drawer--open')).toBe(true);
		expect(root.getAttribute('aria-hidden')).toBe('false');
		expect(toggle.getAttribute('aria-expanded')).toBe('true');
		expect(document.body.classList.contains('account-drawer-open')).toBe(true);
	});

	it('closes from the backdrop, the close button, and Escape', () => {
		document.body.innerHTML = loggedInMarkup();
		accountMenu();

		const toggle = document.querySelector('[data-account-menu-toggle]');
		const root = document.querySelector('[data-account-drawer]');

		toggle.click();
		root.querySelector('.account-drawer__backdrop').click();
		expect(root.classList.contains('account-drawer--open')).toBe(false);
		expect(root.getAttribute('aria-hidden')).toBe('true');
		expect(document.body.classList.contains('cart-drawer-open')).toBe(false);

		toggle.click();
		root.querySelector('.account-drawer__close').click();
		expect(root.getAttribute('aria-hidden')).toBe('true');

		toggle.focus();
		toggle.click();
		document.dispatchEvent(new KeyboardEvent('keydown', { key: 'Escape' }));
		expect(root.classList.contains('account-drawer--open')).toBe(false);
		expect(document.activeElement).toBe(toggle);
	});

	it('does not attach document listeners when the header has no account menu', () => {
		const addEventListener = vi.spyOn(document, 'addEventListener');
		accountMenu();
		expect(addEventListener).not.toHaveBeenCalled();
	});

	it('links registered orders by customer id and ignores a guest email on that order', () => {
		expect(orderCustomerLink({ customerId: 12, billingEmail: 'Ana@Recupe.test' })).toEqual({
			type: 'customer',
			customerId: 12,
		});
		expect(orderCustomerLink({ customer_id: '7' })).toEqual({
			type: 'customer',
			customerId: 7,
		});
	});

	it('links guest orders by billing email and leaves empty orders unlinked', () => {
		expect(orderCustomerLink({ customerId: 0, billingEmail: '  Hola@Recupe.test ' })).toEqual({
			type: 'guest',
			billingEmail: 'hola@recupe.test',
		});
		expect(orderCustomerLink({ customer_id: null, billing_email: '' })).toEqual({
			type: 'none',
		});
	});

	it('groups purchases by customer id or guest email', () => {
		expect(
			groupOrdersByCustomer([
				{ id: 10, customerId: 4, billingEmail: 'a@recupe.test' },
				{ id: 11, customerId: 4, billingEmail: 'a@recupe.test' },
				{ id: 12, customerId: 0, billingEmail: 'b@recupe.test' },
				{ id: 13, customerId: 0, billingEmail: 'B@recupe.test' },
				{ id: 14, customerId: 0, billingEmail: 'c@recupe.test' },
			])
		).toEqual({
			'customer:4': [10, 11],
			'guest:b@recupe.test': [12, 13],
			'guest:c@recupe.test': [14],
		});
	});

	it('treats the My Account page as the WooCommerce shortcode', () => {
		expect(
			accountPageUsesMyAccount('<!-- wp:shortcode -->[woocommerce_my_account]<!-- /wp:shortcode -->')
		).toBe(true);
		expect(accountPageUsesMyAccount('[woocommerce_cart]')).toBe(false);
		expect(missingAccountEndpoints(null)).toEqual([
			'orders',
			'edit-address',
			'edit-account',
			'customer-logout',
		]);
	});
});
