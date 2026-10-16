const getConfig = () => window.wpbCommunitySubscribe || window.wpbCommunitySplit || {};

const setStatus = (statusEl, block, message, type) => {
	if (!statusEl) return;

	statusEl.textContent = message || '';
	statusEl.hidden = !message;
	statusEl.classList.remove(
		`${block}__status--success`,
		`${block}__status--error`,
		`${block}__status--pending`
	);

	if (type) {
		statusEl.classList.add(`${block}__status--${type}`);
	}
};

/**
 * Binds a subscribe form to the shared community AJAX endpoint.
 *
 * @param {object} options
 * @param {Element} options.root Section root.
 * @param {string} options.block BEM block (e.g. community-split).
 * @param {string} options.source Source saved in DB.
 * @param {string} [options.statusSelector] Status element selector.
 * @param {WeakMap<Element, () => void>} options.instances
 * @param {AbortSignal} options.signal
 * @returns {boolean}
 */
export const bindCommunitySubscribeForm = ({
	root,
	block,
	source,
	statusSelector,
	instances,
	signal,
}) => {
	if (instances.has(root)) return false;

	const form = root.querySelector(`.${block}__form`);
	const input = form?.querySelector(`.${block}__input`);
	const submit = form?.querySelector(`.${block}__submit`);
	const statusEl = form?.querySelector(statusSelector || `[data-${block}-status]`);
	const config = getConfig();

	if (!form || !input || !submit) return false;

	const defaultLabel = submit.textContent;

	form.addEventListener(
		'submit',
		async (event) => {
			event.preventDefault();

			const email = input.value.trim();
			const i18n = config.i18n || {};

			if (!email || !input.checkValidity()) {
				setStatus(statusEl, block, i18n.invalid || 'Ingresa un correo válido.', 'error');
				input.focus();
				return;
			}

			if (!config.ajaxUrl || !config.nonce) {
				setStatus(
					statusEl,
					block,
					i18n.error || 'No se pudo completar el registro. Intenta de nuevo.',
					'error'
				);
				return;
			}

			submit.disabled = true;
			setStatus(statusEl, block, i18n.sending || 'Enviando…', 'pending');

			const body = new FormData();
			body.append('action', config.action || 'wpb_community_subscribe');
			body.append('nonce', config.nonce);
			body.append('email', email);
			body.append('source', source);

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
				const message =
					payload?.data?.message ||
					(payload?.success
						? i18n.success
						: payload?.data?.code === 'duplicate_email'
							? i18n.duplicate
							: i18n.error);

				if (payload?.success) {
					setStatus(statusEl, block, message || i18n.success, 'success');
					form.reset();
				} else {
					setStatus(statusEl, block, message || i18n.error, 'error');
				}
			} catch (error) {
				setStatus(
					statusEl,
					block,
					i18n.error || 'No se pudo completar el registro. Intenta de nuevo.',
					'error'
				);
			} finally {
				submit.disabled = false;
				submit.textContent = defaultLabel;
			}
		},
		{ signal }
	);

	return true;
};
