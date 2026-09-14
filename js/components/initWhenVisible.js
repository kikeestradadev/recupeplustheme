export const initWhenVisible = (el, start, rootMargin = '200px') => {
	if (!el || typeof start !== 'function') {
		return;
	}

	if (!('IntersectionObserver' in window)) {
		start();
		return;
	}

	const io = new IntersectionObserver(
		(entries) => {
			entries.forEach((entry) => {
				if (!entry.isIntersecting) {
					return;
				}

				start();
				io.disconnect();
			});
		},
		{ rootMargin }
	);

	io.observe(el);
};
