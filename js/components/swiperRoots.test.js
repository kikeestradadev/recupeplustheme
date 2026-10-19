import { describe, expect, it } from 'vitest';
import { swiperRoots } from './swiperRoots.js';

describe('swiperRoots', () => {
	it('returns the scope itself when it matches the selector', () => {
		const section = document.createElement('section');
		section.className = 'product-slider';
		expect(swiperRoots(section, '.product-slider')).toEqual([section]);
	});

	it('collects matching descendants when scope does not match', () => {
		const wrap = document.createElement('div');
		wrap.innerHTML = `
			<section class="product-slider"></section>
			<section class="other"></section>
			<section class="product-slider"></section>
		`;
		const roots = swiperRoots(wrap, '.product-slider');
		expect(roots).toHaveLength(2);
		expect(roots.every((el) => el.classList.contains('product-slider'))).toBe(true);
	});

	it('defaults scope to document', () => {
		document.body.innerHTML = '<div class="blog-slider"></div>';
		expect(swiperRoots(undefined, '.blog-slider')).toHaveLength(1);
		document.body.innerHTML = '';
	});
});
