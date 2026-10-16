import { bindCommunitySubscribeForm } from './communitySubscribeForm';

const communitySplitInstances = new WeakMap();

const communitySplit = (scope = document) => {
	/** @type {Array<() => void>} */
	const initialized = [];
	const roots = scope.querySelectorAll('.community-split');

	if (roots.length === 0) {
		return initialized;
	}

	roots.forEach((root) => {
		if (communitySplitInstances.has(root)) return;

		const controller = new AbortController();
		const { signal } = controller;
		const bound = bindCommunitySubscribeForm({
			root,
			block: 'community-split',
			source: 'community-split',
			statusSelector: '[data-community-split-status]',
			instances: communitySplitInstances,
			signal,
		});

		if (!bound) return;

		const destroy = () => {
			controller.abort();
			communitySplitInstances.delete(root);
		};

		communitySplitInstances.set(root, destroy);
		initialized.push(destroy);
	});

	return initialized;
};

export default communitySplit;
