import { bindCommunitySubscribeForm } from './communitySubscribeForm';

const newsletterBannerInstances = new WeakMap();

const newsletterBanner = (scope = document) => {
	/** @type {Array<() => void>} */
	const initialized = [];
	const roots = scope.querySelectorAll('.newsletter-banner');

	if (roots.length === 0) {
		return initialized;
	}

	roots.forEach((root) => {
		if (newsletterBannerInstances.has(root)) return;

		const controller = new AbortController();
		const { signal } = controller;
		const bound = bindCommunitySubscribeForm({
			root,
			block: 'newsletter-banner',
			source: 'newsletter-banner',
			statusSelector: '[data-newsletter-banner-status]',
			instances: newsletterBannerInstances,
			signal,
		});

		if (!bound) return;

		const destroy = () => {
			controller.abort();
			newsletterBannerInstances.delete(root);
		};

		newsletterBannerInstances.set(root, destroy);
		initialized.push(destroy);
	});

	return initialized;
};

export default newsletterBanner;
