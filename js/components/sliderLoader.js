const LOADER_CLASS = 'slider-loader';
const READY_CLASS = 'slider-loader--ready';

const revealSlider = (root) => {
	if (!root || root.classList.contains(READY_CLASS)) return;
	root.classList.add(READY_CLASS);
};

const armSliderLoader = (root) => {
	if (!root) return;
	root.classList.add(LOADER_CLASS);
};

const markExistingSlider = (root) => {
	armSliderLoader(root);
	revealSlider(root);
};

const hasBlockingImages = (root) =>
	[...root.querySelectorAll('img')].some((img) => {
		const src = img.getAttribute('src') || img.getAttribute('data-src');
		if (!src) return false;
		return !img.complete || img.naturalWidth === 0;
	});

/**
 * Native loading="lazy" + opacity:0 (slider-loader) can deadlock:
 * the browser never fetches off-screen / invisible lazy images,
 * so load/error never fire and --ready is never added.
 * Force eager fetch for pending images inside the armed slider.
 */
const forceEagerPendingImages = (root) => {
	[...root.querySelectorAll('img')].forEach((img) => {
		const src = img.getAttribute('src') || img.getAttribute('data-src');
		if (!src) return;
		if (img.complete && img.naturalWidth > 0) return;

		if (img.getAttribute('loading') === 'lazy') {
			img.setAttribute('loading', 'eager');
		}

		if (!img.getAttribute('src') && img.dataset.src) {
			img.src = img.dataset.src;
		}
	});
};

const withSliderLoader = (root, options = {}) => {
	armSliderLoader(root);
	const userOn = options.on || {};
	let revealed = false;

	const reveal = () => {
		if (revealed) return;
		revealed = true;
		revealSlider(root);
	};

	const watchImages = () => {
		forceEagerPendingImages(root);

		const pending = [...root.querySelectorAll('img')].filter((img) => {
			const src = img.getAttribute('src') || img.getAttribute('data-src');
			return Boolean(src) && (!img.complete || img.naturalWidth === 0);
		});

		if (pending.length === 0) {
			reveal();
			return;
		}

		let remaining = pending.length;
		pending.forEach((img) => {
			const done = () => {
				remaining -= 1;
				if (remaining <= 0) reveal();
			};
			img.addEventListener('load', done, { once: true });
			img.addEventListener('error', done, { once: true });
			// Cached images can already be complete after forcing eager.
			if (img.complete) done();
		});
	};

	return {
		...options,
		on: {
			...userOn,
			init(swiper) {
				if (typeof userOn.init === 'function') userOn.init(swiper);
				if (!hasBlockingImages(root)) {
					reveal();
					return;
				}
				watchImages();
			},
			imagesReady(swiper) {
				if (typeof userOn.imagesReady === 'function') userOn.imagesReady(swiper);
				reveal();
			},
		},
	};
};

export { armSliderLoader, markExistingSlider, revealSlider, withSliderLoader };
