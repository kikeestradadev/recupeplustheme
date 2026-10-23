/**
 * Pure rules for the service worker. The theme folder comes from the script URL.
 */

/**
 * Theme directory URL from the worker script query, or an empty string.
 *
 * @param {string} scriptUrl
 * @returns {string}
 */
export const themeBaseFrom = (scriptUrl) => {
	let url;
	try {
		url = new URL(scriptUrl);
	} catch (error) {
		return '';
	}

	const theme = url.searchParams.get('theme');
	if (!theme) {
		return '';
	}

	let themeUrl;
	try {
		themeUrl = new URL(theme, url.origin);
	} catch (error) {
		return '';
	}

	if (themeUrl.origin !== url.origin || !themeUrl.pathname.includes('/wp-content/themes/')) {
		return '';
	}

	return themeUrl.href.endsWith('/') ? themeUrl.href : `${themeUrl.href}/`;
};

/**
 * Requests the worker must leave to the network.
 *
 * @param {URL} url
 * @returns {boolean}
 */
export const shouldBypass = (url) => {
	const path = url.pathname;
	if (path.includes('/wp-admin') || path.includes('/wp-login.php') || path.includes('admin-ajax.php')) {
		return true;
	}
	return url.search.includes('wc-ajax=') || url.search.includes('wpb_pwa=');
};

/**
 * Theme files and uploads can be reused offline. Everything else stays on the network.
 *
 * @param {string} pathname
 * @param {string} themeBaseHref
 * @returns {boolean}
 */
export const isCacheableAsset = (pathname, themeBaseHref) => {
	if (pathname.includes('/wp-content/uploads/')) {
		return true;
	}
	if (!themeBaseHref) {
		return false;
	}
	return pathname.startsWith(new URL(themeBaseHref).pathname);
};
