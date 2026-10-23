import { describe, expect, it } from 'vitest';
import { readFileSync } from 'node:fs';
import { dirname, resolve } from 'node:path';
import { fileURLToPath } from 'node:url';

const root = resolve(dirname(fileURLToPath(import.meta.url)), '../..');
const fields = readFileSync(
	resolve(root, 'core-functions/admin/wpb_suplementos-fields.php'),
	'utf8'
);
const template = readFileSync(
	resolve(root, 'template-parts/static-modules/suplementos/ingredients-split.php'),
	'utf8'
);

const iconKeys = ['citrus', 'magnesium', 'ginger', 'chamomile'];

describe('ingredients icon dropdown', () => {
	it('offers the same four icons in the ACF select and draws each one', () => {
		iconKeys.forEach((key) => {
			expect(fields).toContain(`'${key}'`);
			expect(template).toContain(`'${key}' === $card['icon']`);
			expect(template).toContain('<svg');
		});
		expect(template).toContain('sanitize_key');
		expect(template).toContain('in_array( $icon, $icon_keys, true )');
	});
});