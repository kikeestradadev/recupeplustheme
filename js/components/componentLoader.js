const MEDIA_TIMEOUT_MS = 5000;
const VIEWPORT_MARGIN = '80px 0px';

const refreshSliders = (section) => {
	section.querySelectorAll('.swiper').forEach((element) => {
		if (element.swiper && typeof element.swiper.update === 'function') {
			element.swiper.update();
		}
	});
};

const isNearViewport = (element) => {
	const rect = element.getBoundingClientRect();
	if (rect.width === 0 && rect.height === 0) return true;

	const verticalLimit = (window.innerHeight || 0) + 640;
	const horizontalLimit = (window.innerWidth || 0) + 240;
	return rect.top < verticalLimit && rect.bottom > -200 && rect.left < horizontalLimit;
};

const unveilWithoutLibrary = (elements) => {
	elements.forEach((element) => {
		const picture = element.parentElement?.tagName === 'PICTURE' ? element.parentElement : null;
		picture?.querySelectorAll('source[data-srcset]').forEach((source) => {
			if (!source.getAttribute('srcset') && source.dataset.srcset) {
				source.srcset = source.dataset.srcset;
			}
		});

		if (!element.getAttribute('src') && element.dataset.src) {
			element.src = element.dataset.src;
		}
	});
};

const unveilElements = (elements) => {
	if (window.lazySizes?.loader?.unveil) {
		elements.forEach((element) => window.lazySizes.loader.unveil(element));
		return;
	}

	unveilWithoutLibrary(elements);
};

const markReady = (section) => {
	if (section.classList.contains('component-loader--ready')) return;

	section.classList.add('component-loader--ready');
	section.setAttribute('aria-busy', 'false');
	window.requestAnimationFrame(() => refreshSliders(section));
};

const waitForMedia = (section) => {
	const media = [...section.querySelectorAll('img[data-src], iframe[data-src]')].filter(
		(element) => element.closest('.swiper') || isNearViewport(element)
	);
	const pending = media.filter(
		(element) => element.tagName !== 'IMG' || element.naturalWidth === 0
	);
	const blocking = pending.filter(isNearViewport);

	if (blocking.length === 0) {
		unveilElements(pending.filter((element) => !element.getAttribute('src')));
		markReady(section);
		return;
	}

	let remaining = blocking.length;
	let settled = false;

	const finish = () => {
		if (settled) return;
		remaining -= 1;
		if (remaining > 0) return;
		settled = true;
		markReady(section);
	};

	const settle = (element) => {
		if (element.dataset.lazySettled === 'true') return;
		element.dataset.lazySettled = 'true';
		finish();
	};

	blocking.forEach((element) => {
		element.addEventListener('load', () => settle(element), { once: true });
		element.addEventListener('error', () => settle(element), { once: true });
	});

	unveilElements(pending.filter((element) => !element.getAttribute('src')));

	blocking.forEach((element) => {
		if (element.tagName === 'IMG' && element.naturalWidth > 0) settle(element);
	});

	window.setTimeout(() => {
		if (settled) return;
		settled = true;
		markReady(section);
	}, MEDIA_TIMEOUT_MS);
};

const componentLoader = (modules) => {
	const started = new Set();
	const main = document.querySelector('main.lazy-sections');
	const sections = [];

	if (main) {
		[...main.children]
			.filter((node) => node.tagName === 'SECTION')
			.slice(1)
			.forEach((section) => sections.push(section));
	}

	document.querySelectorAll('[data-lazy-component]').forEach((node) => {
		const nestedInDeferred = sections.some(
			(section) => section !== node && section.contains(node)
		);
		if (nestedInDeferred || sections.includes(node)) return;
		sections.push(node);
	});

	const runModules = (section) => {
		modules.forEach(({ selector, init }) => {
			if (started.has(selector)) return;
			if (!section.matches(selector) && !section.querySelector(selector)) return;
			started.add(selector);
			init(document);
		});
	};

	const activate = (section) => {
		if (section.dataset.lazyState === 'done') return;
		section.dataset.lazyState = 'done';

		window.requestAnimationFrame(() => {
			window.requestAnimationFrame(() => {
				runModules(section);
				waitForMedia(section);
			});
		});
	};

	if (sections.length === 0) return;

	sections.forEach((section) => {
		section.classList.add('component-loader');
		section.setAttribute('aria-busy', 'true');
	});

	if (!('IntersectionObserver' in window)) {
		sections.forEach(activate);
		return;
	}

	const observer = new IntersectionObserver(
		(entries) => {
			entries.forEach((entry) => {
				if (!entry.isIntersecting) return;
				observer.unobserve(entry.target);
				activate(entry.target);
			});
		},
		{
			root: null,
			rootMargin: VIEWPORT_MARGIN,
			threshold: 0.01,
		}
	);

	sections.forEach((section) => observer.observe(section));
};

export default componentLoader;
