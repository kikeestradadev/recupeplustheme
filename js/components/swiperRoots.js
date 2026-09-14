export const swiperRoots = (scope, selector) => {
	const root = scope || document;

	if (root.nodeType === 1 && typeof root.matches === 'function' && root.matches(selector)) {
		return [root];
	}

	return Array.from(root.querySelectorAll(selector));
};
