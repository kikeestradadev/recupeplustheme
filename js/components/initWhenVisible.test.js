import { afterEach, describe, expect, it, vi } from 'vitest';
import { initWhenVisible } from './initWhenVisible.js';

describe('initWhenVisible', () => {
	afterEach(() => {
		vi.restoreAllMocks();
	});

	it('no-ops when element or start callback is missing', () => {
		expect(() => initWhenVisible(null, () => {})).not.toThrow();
		expect(() => initWhenVisible(document.createElement('div'), null)).not.toThrow();
	});

	it('starts immediately when IntersectionObserver is unavailable', () => {
		const start = vi.fn();
		const original = window.IntersectionObserver;
		// eslint-disable-next-line no-unused-vars
		delete window.IntersectionObserver;

		initWhenVisible(document.createElement('div'), start);
		expect(start).toHaveBeenCalledTimes(1);

		window.IntersectionObserver = original;
	});

	it('starts once when the element intersects, then disconnects', () => {
		const start = vi.fn();
		const disconnect = vi.fn();
		let observerCallback;

		window.IntersectionObserver = vi.fn(function MockIO(cb) {
			observerCallback = cb;
			this.observe = vi.fn();
			this.disconnect = disconnect;
		});

		const el = document.createElement('section');
		initWhenVisible(el, start, '200px');

		expect(window.IntersectionObserver).toHaveBeenCalled();
		expect(start).not.toHaveBeenCalled();

		observerCallback([{ isIntersecting: false }]);
		expect(start).not.toHaveBeenCalled();

		observerCallback([{ isIntersecting: true }]);
		expect(start).toHaveBeenCalledTimes(1);
		expect(disconnect).toHaveBeenCalledTimes(1);
	});
});
