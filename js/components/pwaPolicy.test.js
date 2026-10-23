import { describe, expect, it } from 'vitest';
import { readFileSync } from 'node:fs';
import { dirname, resolve } from 'node:path';
import { fileURLToPath } from 'node:url';
import { isCacheableAsset, shouldBypass, themeBaseFrom } from '../../pwa/sw-policy.js';

const root = resolve(dirname(fileURLToPath(import.meta.url)), '../..');
const read = (path) => readFileSync(resolve(root, path), 'utf8');

describe('service worker theme path', () => {
	it('accepts whatever theme folder the script URL points at', () => {
		const script =
			'https://shop.example/tienda/?wpb_pwa=sw&theme=' +
			encodeURIComponent('https://shop.example/tienda/wp-content/themes/otro-theme/');

		expect(themeBaseFrom(script)).toBe('https://shop.example/tienda/wp-content/themes/otro-theme/');
	});

	it('rejects a theme URL on another origin or outside the themes directory', () => {
		const foreign =
			'https://shop.example/?wpb_pwa=sw&theme=' + encodeURIComponent('https://evil.example/wp-content/themes/x/');
		const outside =
			'https://shop.example/?wpb_pwa=sw&theme=' + encodeURIComponent('https://shop.example/wp-content/uploads/');

		expect(themeBaseFrom(foreign)).toBe('');
		expect(themeBaseFrom(outside)).toBe('');
		expect(themeBaseFrom('https://shop.example/?wpb_pwa=sw')).toBe('');
	});

	it('leaves admin, ajax, and the worker response itself on the network', () => {
		expect(shouldBypass(new URL('https://shop.example/wp-admin/index.php'))).toBe(true);
		expect(shouldBypass(new URL('https://shop.example/wp-login.php'))).toBe(true);
		expect(shouldBypass(new URL('https://shop.example/wp-admin/admin-ajax.php'))).toBe(true);
		expect(shouldBypass(new URL('https://shop.example/?wc-ajax=add_to_cart'))).toBe(true);
		expect(shouldBypass(new URL('https://shop.example/?wpb_pwa=sw'))).toBe(true);
		expect(shouldBypass(new URL('https://shop.example/tienda/'))).toBe(false);
	});

	it('caches the active theme and uploads, not a hardcoded theme name', () => {
		const base = 'https://shop.example/wp-content/themes/otro-theme/';

		expect(isCacheableAsset('/wp-content/themes/otro-theme/theme-styles.css', base)).toBe(true);
		expect(isCacheableAsset('/wp-content/themes/recupeplustheme/theme-styles.css', base)).toBe(false);
		expect(isCacheableAsset('/wp-content/uploads/2026/10/favicon.png', base)).toBe(true);
		expect(isCacheableAsset('/wp-includes/js/jquery.js', base)).toBe(false);
	});

	it('keeps the worker inside the theme and does not name a theme folder', () => {
		const php = read('core-functions/core/wpb_pwa.php');
		const policy = read('pwa/sw-policy.js');
		const runtime = read('pwa/sw-runtime.js');

		expect(php).not.toContain("ABSPATH . 'sw.js'");
		expect(php).not.toContain('copy(');
		expect(php).toContain('sw-policy.js');
		expect(php).toContain('sw-runtime.js');
		expect(policy + runtime).not.toContain('recupeplustheme');
	});
});
