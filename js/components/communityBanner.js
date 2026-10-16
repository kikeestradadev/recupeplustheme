import { bindCommunitySubscribeForm } from './communitySubscribeForm';

const communityBannerInstances = new WeakMap();

const communityBanner = (scope = document) => {
	/** @type {Array<() => void>} */
	const initialized = [];
	const roots = scope.querySelectorAll('.community-banner');

	if (roots.length === 0) {
		return initialized;
	}

	roots.forEach((root) => {
		if (communityBannerInstances.has(root)) return;

		const controller = new AbortController();
		const { signal } = controller;
		const bound = bindCommunitySubscribeForm({
			root,
			block: 'community-banner',
			source: 'community-banner',
			statusSelector: '[data-community-banner-status]',
			instances: communityBannerInstances,
			signal,
		});

		if (!bound) return;

		const destroy = () => {
			controller.abort();
			communityBannerInstances.delete(root);
		};

		communityBannerInstances.set(root, destroy);
		initialized.push(destroy);
	});

	return initialized;
};

export default communityBanner;
