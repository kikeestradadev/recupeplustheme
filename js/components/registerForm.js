export const PASSWORD_MIN_LENGTH = 8;

const RULES = [
	{
		test: (password) => password.length >= PASSWORD_MIN_LENGTH,
		message: 'La contraseña debe tener al menos 8 caracteres.',
	},
	{
		test: (password) => /[A-Z]/.test(password),
		message: 'La contraseña debe incluir al menos una letra mayúscula.',
	},
	{
		test: (password) => /[0-9]/.test(password),
		message: 'La contraseña debe incluir al menos un número.',
	},
	{
		test: (password) => /[^A-Za-z0-9]/.test(password),
		message: 'La contraseña debe incluir al menos un carácter especial.',
	},
];

/**
 * One message per failed password rule.
 *
 * @param {string} password
 * @returns {string[]}
 */
export const passwordRequirementErrors = (password) => {
	const value = String(password ?? '');
	return RULES.filter((rule) => !rule.test(value)).map((rule) => rule.message);
};

/**
 * @param {string} password
 * @param {string} confirm
 * @returns {string}
 */
export const passwordMatchError = (password, confirm) =>
	String(password ?? '') === String(confirm ?? '') ? '' : 'Las contraseñas no coinciden.';

/**
 * Strength rules plus the repeat-password check.
 *
 * @param {string} password
 * @param {string} confirm
 * @returns {string[]}
 */
export const registrationPasswordErrors = (password, confirm) => {
	const errors = passwordRequirementErrors(password);
	const match = passwordMatchError(password, confirm);
	if (match) errors.push(match);
	return errors;
};

const paintErrors = (list, errors) => {
	list.replaceChildren();
	errors.forEach((message) => {
		const item = document.createElement('li');
		item.textContent = message;
		list.appendChild(item);
	});
	list.hidden = errors.length === 0;
};

const registerForm = (scope = document) => {
	const cleanups = [];

	scope.querySelectorAll('[data-register-form]').forEach((form) => {
		if (form.dataset.registerBound === 'true') return;
		form.dataset.registerBound = 'true';

		const onSubmit = (event) => {
			const password = form.querySelector('[data-register-password]')?.value ?? '';
			const confirm = form.querySelector('[data-register-password-confirm]')?.value ?? '';
			const list = form.querySelector('[data-register-errors]');
			const errors = registrationPasswordErrors(password, confirm);

			if (!list) return;

			paintErrors(list, errors);
			if (errors.length) event.preventDefault();
		};

		form.addEventListener('submit', onSubmit);
		cleanups.push(() => {
			form.removeEventListener('submit', onSubmit);
			delete form.dataset.registerBound;
		});
	});

	return () => cleanups.forEach((destroy) => destroy());
};

export default registerForm;
