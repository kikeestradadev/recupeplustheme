import { describe, expect, it } from 'vitest';
import { readFileSync } from 'node:fs';
import { dirname, resolve } from 'node:path';
import { fileURLToPath } from 'node:url';

const root = resolve(dirname(fileURLToPath(import.meta.url)), '../..');
const read = (path) => readFileSync(resolve(root, path), 'utf8');

const banners = [
	{
		name: 'suplementos',
		template: 'template-parts/static-modules/suplementos/supplements-banner.php',
		style: 'scss/modules/_supplements-banner.scss',
		importName: 'supplements-banner',
	},
	{
		name: 'tienda',
		template: 'template-parts/static-modules/tienda/tienda-banner.php',
		style: 'scss/modules/_tienda-banner.scss',
		importName: 'tienda-banner',
	},
	{
		name: 'guías',
		template: 'template-parts/static-modules/guias/guides-banner.php',
		style: 'scss/modules/_guides-banner.scss',
		importName: 'guides-banner',
	},
];

describe('banner pages', () => {
	it('uses the same desktop, tablet, and mobile sources on the three pages', () => {
		const modules = read('scss/modules/modules.scss');

		banners.forEach((banner) => {
			const template = read(banner.template);
			const style = read(banner.style);

			expect(template).toContain('(width >= 1280px)');
			expect(template).toContain('(width >= 1024px)');
			expect(template).toContain('<picture');
			expect(style).toContain('width: 100%');
			expect(style).toContain('height: auto');
			expect(modules).toContain(`@import "${banner.importName}"`);
		});
	});

	it('prints a promo title only when that ACF field has text', () => {
		const titleGuard = "'' !== $" + 'title';
		expect(read(banners[0].template)).toContain(titleGuard);
		expect(read(banners[1].template)).toContain(titleGuard);
		expect(read(banners[2].template)).not.toContain('banner_title');
	});
});
