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
		const src = img.getAttribute('src');
		if (!src) return false;
		return !img.complete || img.naturalWidth === 0;
	});

const withSliderLoader = (root, options = {}) => {
	armSliderLoader(root);
	const userOn = options.on || {};

	return {
		...options,
		on: {
			...userOn,
			init(swiper) {
				if (typeof userOn.init === 'function') userOn.init(swiper);
				if (!hasBlockingImages(root)) revealSlider(root);
			},
			imagesReady(swiper) {
				if (typeof userOn.imagesReady === 'function') userOn.imagesReady(swiper);
				revealSlider(root);
			},
		},
	};
};

export { armSliderLoader, markExistingSlider, revealSlider, withSliderLoader };
