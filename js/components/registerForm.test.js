import { afterEach, describe, expect, it } from 'vitest';
import registerForm, {
	passwordMatchError,
	passwordRequirementErrors,
	registrationPasswordErrors,
} from './registerForm.js';

const mountForm = (password, confirm) => {
	document.body.innerHTML = `
		<form data-register-form>
			<input data-register-password type="password" value="${password}">
			<input data-register-password-confirm type="password" value="${confirm}">
			<ul data-register-errors hidden></ul>
			<button type="submit">Registrarse</button>
		</form>
	`;
	registerForm();
	return document.querySelector('[data-register-form]');
};

describe('registerForm password rules', () => {
	afterEach(() => {
		document.body.innerHTML = '';
	});

	it('lists every failed requirement', () => {
		expect(passwordRequirementErrors('abc')).toEqual([
			'La contraseña debe tener al menos 8 caracteres.',
			'La contraseña debe incluir al menos una letra mayúscula.',
			'La contraseña debe incluir al menos un número.',
			'La contraseña debe incluir al menos un carácter especial.',
		]);
	});

	it('accepts a password that meets every rule', () => {
		expect(passwordRequirementErrors('Clave12!')).toEqual([]);
	});

	it('adds the repeat-password error without dropping the other rules', () => {
		expect(registrationPasswordErrors('abc', 'abcd')).toEqual([
			'La contraseña debe tener al menos 8 caracteres.',
			'La contraseña debe incluir al menos una letra mayúscula.',
			'La contraseña debe incluir al menos un número.',
			'La contraseña debe incluir al menos un carácter especial.',
			'Las contraseñas no coinciden.',
		]);
		expect(passwordMatchError('Clave12!', 'Clave12!')).toBe('');
		expect(registrationPasswordErrors('Clave12!', 'Clave12!')).toEqual([]);
	});

	it('blocks submit and shows each error when the passwords fail', () => {
		const form = mountForm('abc', 'zzz');
		const event = new Event('submit', { cancelable: true, bubbles: true });
		form.dispatchEvent(event);

		const messages = [...form.querySelectorAll('[data-register-errors] li')].map(
			(item) => item.textContent
		);
		expect(event.defaultPrevented).toBe(true);
		expect(form.querySelector('[data-register-errors]').hidden).toBe(false);
		expect(messages).toEqual(registrationPasswordErrors('abc', 'zzz'));
	});

	it('lets a matching valid password submit', () => {
		const form = mountForm('Clave12!', 'Clave12!');
		const event = new Event('submit', { cancelable: true, bubbles: true });
		form.dispatchEvent(event);

		expect(event.defaultPrevented).toBe(false);
		expect(form.querySelector('[data-register-errors]').hidden).toBe(true);
		expect(form.querySelectorAll('[data-register-errors] li')).toHaveLength(0);
	});
});
