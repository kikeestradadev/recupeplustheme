import { afterEach, beforeEach, describe, expect, it, vi } from 'vitest';
import blogFilter from './blogFilter.js';

describe('blogFilter', () => {
	beforeEach(() => {
		document.body.innerHTML = '';
		window.matchMedia = vi.fn().mockImplementation((query) => ({
			matches: true,
			media: query,
			addEventListener: vi.fn(),
			removeEventListener: vi.fn(),
			addListener: vi.fn(),
			removeListener: vi.fn(),
		}));
	});

	afterEach(() => {
		document.body.innerHTML = '';
	});

	it('filters cards by chip category and updates aria-pressed', () => {
		document.body.innerHTML = `
			<section data-blog-filter data-per-page="6">
				<button type="button" class="blog-filter__chip blog-filter__chip--active" data-filter="all" aria-pressed="true">All</button>
				<button type="button" class="blog-filter__chip" data-filter="bienestar" aria-pressed="false">Bienestar</button>
				<div data-blog-grid>
					<article class="blog-filter__card" data-category="bienestar"></article>
					<article class="blog-filter__card" data-category="energia"></article>
					<article class="blog-filter__card" data-category="bienestar energia"></article>
				</div>
				<div data-blog-pagination></div>
			</section>
		`;

		blogFilter(document);

		const cards = () =>
			Array.from(document.querySelectorAll('[data-blog-grid] .blog-filter__card')).map((card) => ({
				category: card.dataset.category,
				hidden: card.hidden || card.style.display === 'none' || card.classList.contains('is-hidden'),
				display: getComputedStyle(card).display,
			}));

		document.querySelector('[data-filter="bienestar"]').click();

		const bienestarChip = document.querySelector('[data-filter="bienestar"]');
		expect(bienestarChip.classList.contains('blog-filter__chip--active')).toBe(true);
		expect(bienestarChip.getAttribute('aria-pressed')).toBe('true');
		expect(document.querySelector('[data-filter="all"]').getAttribute('aria-pressed')).toBe('false');

		const visible = Array.from(document.querySelectorAll('[data-blog-grid] .blog-filter__card')).filter(
			(card) => !card.hidden && card.style.display !== 'none'
		);
		// Implementation may hide via hidden attribute or display — assert category filter outcome.
		const shownCategories = visible.map((card) => card.dataset.category);
		expect(shownCategories.every((cat) => String(cat).split(/\s+/).includes('bienestar'))).toBe(true);
		expect(cards().length).toBe(3);
	});
});
