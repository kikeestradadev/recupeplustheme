import { describe, expect, it, vi } from 'vitest';
import { markExistingSlider, withSliderLoader } from './sliderLoader.js';

describe('sliderLoader', () => {
	it('marks an existing slider as armed and ready', () => {
		const root = document.createElement('div');
		markExistingSlider(root);
		expect(root.classList.contains('slider-loader')).toBe(true);
		expect(root.classList.contains('slider-loader--ready')).toBe(true);
	});

	it('reveals immediately when there are no pending images', () => {
		const root = document.createElement('div');
		const options = withSliderLoader(root, { slidesPerView: 1 });
		expect(root.classList.contains('slider-loader')).toBe(true);
		expect(typeof options.on.init).toBe('function');
		options.on.init();
		expect(root.classList.contains('slider-loader--ready')).toBe(true);
	});

	it('forces eager loading and reveals after images settle', () => {
		const root = document.createElement('div');
		const img = document.createElement('img');
		img.setAttribute('loading', 'lazy');
		img.setAttribute('data-src', '/cover.webp');
		Object.defineProperty(img, 'complete', { configurable: true, get: () => false });
		Object.defineProperty(img, 'naturalWidth', { configurable: true, get: () => 0 });
		root.appendChild(img);

		const userInit = vi.fn();
		const options = withSliderLoader(root, {
			on: {
				init: userInit,
			},
		});

		options.on.init();
		expect(userInit).toHaveBeenCalled();
		expect(img.getAttribute('loading')).toBe('eager');
		expect(img.getAttribute('src')).toBe('/cover.webp');
		expect(root.classList.contains('slider-loader--ready')).toBe(false);

		img.dispatchEvent(new Event('load'));
		expect(root.classList.contains('slider-loader--ready')).toBe(true);
	});
});
