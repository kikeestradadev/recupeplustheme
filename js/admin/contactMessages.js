(() => {
	const DEBOUNCE_MS = 350;
	const config = window.wpbContactMessagesAdmin || {};
	const filterForm = document.querySelector('[data-contact-messages-filters]');
	const wrap = document.querySelector('.wrap');
	const liveInputs = [
		document.getElementById('wpb-filter-name'),
		document.getElementById('wpb-filter-email'),
		document.getElementById('wpb-filter-phone'),
	].filter(Boolean);

	if (!filterForm || !wrap || !config.ajaxUrl || !config.nonce || liveInputs.length === 0) return;

	let timer = null;
	let activeController = null;

	const readFilters = () => ({
		name: filterForm.querySelector('#wpb-filter-name')?.value?.trim() || '',
		email: filterForm.querySelector('#wpb-filter-email')?.value?.trim() || '',
		phone: filterForm.querySelector('#wpb-filter-phone')?.value?.trim() || '',
		date_from: filterForm.querySelector('#wpb-filter-date-from')?.value?.trim() || '',
		date_to: filterForm.querySelector('#wpb-filter-date-to')?.value?.trim() || '',
	});

	const getResultsRoot = () =>
		document.getElementById('wpb-contact-messages-results') ||
		document.querySelector('[data-contact-messages-results]');

	const syncUrl = (filters, paged) => {
		const url = new URL(window.location.href);
		const params = url.searchParams;

		params.set('page', 'wpb-contact-messages');
		params.delete('action');
		params.delete('id');
		params.delete('created');
		params.delete('updated');
		params.delete('deleted');
		params.delete('error');

		['name', 'email', 'phone', 'date_from', 'date_to'].forEach((key) => {
			const value = filters[key];
			if (value) params.set(key, value);
			else params.delete(key);
		});

		if (paged > 1) params.set('paged', String(paged));
		else params.delete('paged');

		window.history.replaceState({}, '', url.toString());
	};

	const fetchResults = async (paged = 1) => {
		const filters = readFilters();
		const body = new FormData();
		body.append('action', config.action || 'wpb_contact_messages_filter');
		body.append('nonce', config.nonce);
		body.append('paged', String(paged));
		body.append('name', filters.name);
		body.append('email', filters.email);
		body.append('phone', filters.phone);
		body.append('date_from', filters.date_from);
		body.append('date_to', filters.date_to);

		if (activeController) activeController.abort();
		activeController = new AbortController();

		const resultsRoot = getResultsRoot();
		if (resultsRoot) {
			resultsRoot.setAttribute('aria-busy', 'true');
			resultsRoot.style.opacity = '0.65';
		}

		try {
			const response = await fetch(config.ajaxUrl, {
				method: 'POST',
				credentials: 'same-origin',
				body,
				signal: activeController.signal,
			});
			const payload = await response.json();

			if (!payload?.success || typeof payload?.data?.html !== 'string') {
				throw new Error('bad_response');
			}

			const current = getResultsRoot();
			if (!current) return;

			const temp = document.createElement('div');
			temp.innerHTML = payload.data.html.trim();
			const next = temp.firstElementChild;
			if (!next) return;

			current.replaceWith(next);
			syncUrl(filters, Number(payload.data.paged) || paged);
		} catch (error) {
			if (error?.name === 'AbortError') return;
			const current = getResultsRoot();
			if (current) {
				current.setAttribute('aria-busy', 'false');
				current.style.opacity = '';
			}
		} finally {
			activeController = null;
		}
	};

	const scheduleFetch = () => {
		window.clearTimeout(timer);
		timer = window.setTimeout(() => fetchResults(1), DEBOUNCE_MS);
	};

	liveInputs.forEach((input) => {
		input.addEventListener('input', scheduleFetch);
		input.addEventListener('keydown', (event) => {
			if (event.key !== 'Enter') return;
			event.preventDefault();
			window.clearTimeout(timer);
			fetchResults(1);
		});
	});

	filterForm.addEventListener('submit', (event) => {
		event.preventDefault();
		window.clearTimeout(timer);
		fetchResults(1);
	});

	wrap.addEventListener('click', (event) => {
		const link = event.target.closest('[data-contact-messages-results] a[data-paged]');
		if (!link) return;

		event.preventDefault();
		const paged = Number(link.getAttribute('data-paged')) || 1;
		fetchResults(paged);
	});
})();
