import { defineConfig } from 'vitest/config';

export default defineConfig({
	test: {
		environment: 'jsdom',
		include: ['js/components/**/*.test.js'],
		restoreMocks: true,
		clearMocks: true,
	},
});
