const contactFormInstances = new WeakMap();

const getConfig = () => window.wpbContactForm || {};

const setStatus = (statusEl, message, type) => {
	if (!statusEl) return;

	statusEl.textContent = message || '';
	statusEl.hidden = !message;
	statusEl.classList.remove(
		'contact-form__status--success',
		'contact-form__status--error',
		'contact-form__status--pending'
	);

	if (type) {
		statusEl.classList.add(`contact-form__status--${type}`);
	}
};

const contactForm = (scope = document) => {
	/** @type {Array<() => void>} */
	const initialized = [];
	const roots = scope.querySelectorAll('.contact-form');

	if (roots.length === 0) {
		return initialized;
	}

	const config = getConfig();

	roots.forEach((root) => {
		if (contactFormInstances.has(root)) return;

		const form = root.querySelector('.contact-form__form');
		const submit = form?.querySelector('.contact-form__submit');
		const statusEl = form?.querySelector('[data-contact-form-status]');
		const nameInput = form?.querySelector('[name="name"]');
		const emailInput = form?.querySelector('[name="email"]');
		const phoneInput = form?.querySelector('[name="phone"]');
		const messageInput = form?.querySelector('[name="message"]');
		const controller = new AbortController();
		const { signal } = controller;

		if (!form || !submit || !nameInput || !emailInput || !messageInput) return;

		const defaultLabel = submit.textContent;

		form.addEventListener(
			'submit',
			async (event) => {
				event.preventDefault();

				const i18n = config.i18n || {};
				const name = nameInput.value.trim();
				const email = emailInput.value.trim();
				const phone = phoneInput?.value.trim() || '';
				const message = messageInput.value.trim();

				if (!name || !email || !message || !emailInput.checkValidity()) {
					setStatus(statusEl, i18n.invalid || 'Revisa los campos e intenta de nuevo.', 'error');
					return;
				}

				if (!config.ajaxUrl || !config.nonce) {
					setStatus(statusEl, i18n.error || 'No se pudo enviar el mensaje. Intenta de nuevo.', 'error');
					return;
				}

				submit.disabled = true;
				setStatus(statusEl, i18n.sending || 'Enviando…', 'pending');

				const body = new FormData();
				body.append('action', config.action || 'wpb_contact_submit');
				body.append('nonce', config.nonce);
				body.append('name', name);
				body.append('email', email);
				body.append('phone', phone);
				body.append('message', message);

				const honeypot = form.querySelector('[name="website"]');
				if (honeypot) {
					body.append('website', honeypot.value);
				}

				try {
					const response = await fetch(config.ajaxUrl, {
						method: 'POST',
						credentials: 'same-origin',
						body,
					});
					const payload = await response.json();
					const text = payload?.data?.message || (payload?.success ? i18n.success : i18n.error);

					if (payload?.success) {
						setStatus(statusEl, text || i18n.success, 'success');
						form.reset();
					} else {
						setStatus(statusEl, text || i18n.error, 'error');
					}
				} catch (error) {
					setStatus(statusEl, i18n.error || 'No se pudo enviar el mensaje. Intenta de nuevo.', 'error');
				} finally {
					submit.disabled = false;
					submit.textContent = defaultLabel;
				}
			},
			{ signal }
		);

		const destroy = () => {
			controller.abort();
			contactFormInstances.delete(root);
		};

		contactFormInstances.set(root, destroy);
		initialized.push(destroy);
	});

	return initialized;
};

export default contactForm;
