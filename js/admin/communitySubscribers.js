(() => {
	const DEBOUNCE_MS = 350;
	const config = window.wpbCommunitySubscribersAdmin || {};
	const emailInput = document.getElementById('wpb-filter-email');
	const filterForm = document.querySelector('[data-community-subscribers-filters]');
	const wrap = document.querySelector('.wrap');

	if (!emailInput || !filterForm || !wrap || !config.ajaxUrl || !config.nonce) return;

	let timer = null;
	let activeController = null;

	const readFilters = () => ({
		email: emailInput.value.trim(),
		status: filterForm.querySelector('#wpb-filter-status')?.value?.trim() || '',
		source: filterForm.querySelector('#wpb-filter-source')?.value?.trim() || '',
		date_from: filterForm.querySelector('#wpb-filter-date-from')?.value?.trim() || '',
		date_to: filterForm.querySelector('#wpb-filter-date-to')?.value?.trim() || '',
	});

	const getResultsRoot = () =>
		document.getElementById('wpb-community-subscribers-results') ||
		document.querySelector('[data-community-subscribers-results]');

	const syncUrl = (filters, paged) => {
		const url = new URL(window.location.href);
		const params = url.searchParams;

		params.set('page', 'wpb-community-subscribers');
		params.delete('action');
		params.delete('id');
		params.delete('created');
		params.delete('updated');
		params.delete('deleted');
		params.delete('error');

		['email', 'status', 'source', 'date_from', 'date_to'].forEach((key) => {
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
		body.append('action', config.action || 'wpb_community_subscribers_filter');
		body.append('nonce', config.nonce);
		body.append('paged', String(paged));
		body.append('email', filters.email);
		body.append('status', filters.status);
		body.append('source', filters.source);
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

	emailInput.addEventListener('input', () => {
		window.clearTimeout(timer);
		timer = window.setTimeout(() => {
			fetchResults(1);
		}, DEBOUNCE_MS);
	});

	emailInput.addEventListener('keydown', (event) => {
		if (event.key !== 'Enter') return;
		event.preventDefault();
		window.clearTimeout(timer);
		fetchResults(1);
	});

	wrap.addEventListener('click', (event) => {
		const link = event.target.closest('[data-community-subscribers-results] a[data-paged]');
		if (!link) return;

		event.preventDefault();
		const paged = Number(link.getAttribute('data-paged')) || 1;
		fetchResults(paged);
	});
})();
