import { describe, expect, it } from 'vitest';
import { readFileSync } from 'node:fs';
import { dirname, resolve } from 'node:path';
import { fileURLToPath } from 'node:url';

const root = resolve(dirname(fileURLToPath(import.meta.url)), '../..');
const register = readFileSync(resolve(root, 'core-functions/core/wpb_woocommerce-register.php'), 'utf8');
const page = readFileSync(resolve(root, 'page-crear-cuenta.php'), 'utf8');

describe('registration without email activation', () => {
	it('confirms on screen and does not send or require an activation email', () => {
		expect(register).toContain("return 'El registro se hizo correctamente.'");
		expect(register).toContain("add_filter( 'woocommerce_registration_auth_new_customer'");
		expect(register).toContain("add_filter( 'woocommerce_email_enabled_customer_new_account'");
		expect(register).not.toContain('wp_mail(');
		expect(register).not.toContain('wpb_account_pending');
		expect(register).not.toContain('wpb_activate');
	});

	it('does not keep the old activation notices on the registration page', () => {
		expect(page).not.toContain('activacion-invalida');
		expect(page).not.toContain('Tu cuenta ya está activa');
	});
});
