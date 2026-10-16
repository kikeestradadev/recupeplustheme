/**
 * Mantenimientos admin menu: accordion groups by "Section — Page" prefix.
 * Uses a flat sibling structure (no nested ul) so WP admin CSS does not hide items.
 */
(function () {
	'use strict';

	var SEPARATORS = [' — ', ' – ', ' - '];

	/**
	 * @param {string} text
	 * @returns {{section:string,label:string,hasPrefix:boolean}}
	 */
	function parseTitle(text) {
		var clean = (text || '').replace(/\s+/g, ' ').trim();
		var i;

		for (i = 0; i < SEPARATORS.length; i++) {
			var sep = SEPARATORS[i];
			var idx = clean.indexOf(sep);
			if (idx !== -1) {
				return {
					section: clean.slice(0, idx).trim(),
					label: clean.slice(idx + sep.length).trim() || clean,
					hasPrefix: true,
				};
			}
		}

		return { section: clean, label: clean, hasPrefix: false };
	}

	/**
	 * @param {HTMLAnchorElement} link
	 * @param {string} label
	 */
	function setLinkLabel(link, label) {
		if (!link) {
			return;
		}

		var nameSpan = link.querySelector('.wp-menu-name');
		if (nameSpan) {
			nameSpan.textContent = label;
			return;
		}

		// Prefer replacing first non-empty text node; fallback to textContent.
		var node;
		for (var i = 0; i < link.childNodes.length; i++) {
			node = link.childNodes[i];
			if (node.nodeType === 3 && node.textContent.replace(/\s+/g, ' ').trim()) {
				node.textContent = ' ' + label + ' ';
				return;
			}
		}

		link.textContent = label;
	}

	/**
	 * @returns {HTMLLIElement|null}
	 */
	function findParentMenuItem() {
		var byId = document.getElementById('toplevel_page_wpb-mantenimientos');
		if (byId) {
			return byId;
		}

		var links = document.querySelectorAll('#adminmenu a[href*="page=wpb-"]');
		var i;
		for (i = 0; i < links.length; i++) {
			var href = links[i].getAttribute('href') || '';
			if (
				href.indexOf('page=wpb-mantenimientos') !== -1 ||
				href.indexOf('page=wpb-home-') !== -1 ||
				href.indexOf('page=wpb-suplementos') !== -1 ||
				href.indexOf('page=wpb-footer') !== -1
			) {
				var top = links[i].closest('li.menu-top');
				if (top) {
					return top;
				}
			}
		}

		return null;
	}

	/**
	 * @param {HTMLUListElement} submenu
	 */
	function groupSubmenu(submenu) {
		if (!submenu || submenu.getAttribute('data-wpb-mantenimientos-grouped') === '1') {
			return;
		}

		var head = submenu.querySelector('li.wp-submenu-head');
		var items = Array.prototype.slice.call(submenu.children).filter(function (li) {
			return li.tagName === 'LI' && !li.classList.contains('wp-submenu-head');
		});

		if (!items.length) {
			return;
		}

		/** @type {Object.<string, {items:HTMLLIElement[],hasPrefix:boolean}>} */
		var groups = {};
		/** @type {string[]} */
		var order = [];

		items.forEach(function (li) {
			var link = li.querySelector('a');
			var raw = link ? link.textContent : li.textContent;
			var parsed = parseTitle(raw);

			if (!groups[parsed.section]) {
				groups[parsed.section] = { items: [], hasPrefix: parsed.hasPrefix };
				order.push(parsed.section);
			}

			if (parsed.hasPrefix) {
				groups[parsed.section].hasPrefix = true;
				setLinkLabel(link, parsed.label);
			}

			li.classList.add('wpb-mantenimientos-item');
			li.setAttribute('data-wpb-section', parsed.section);
			groups[parsed.section].items.push(li);
		});

		// Rebuild submenu: head + section headers + flat children.
		while (submenu.firstChild) {
			submenu.removeChild(submenu.firstChild);
		}
		if (head) {
			submenu.appendChild(head);
		}

		order.forEach(function (section) {
			var group = groups[section];
			var children = group.items;
			var hasCurrent = children.some(function (li) {
				return (
					li.classList.contains('current') ||
					!!li.querySelector('a.current') ||
					!!li.querySelector('.current')
				);
			});

			// Standalone (no prefix): keep as a normal link row.
			if (!group.hasPrefix && children.length === 1) {
				submenu.appendChild(children[0]);
				return;
			}

			var header = document.createElement('li');
			header.className = 'wpb-mantenimientos-group' + (hasCurrent ? ' is-open' : '');
			header.setAttribute('data-wpb-section', section);

			var toggle = document.createElement('button');
			toggle.type = 'button';
			toggle.className = 'wpb-mantenimientos-group__toggle';
			toggle.setAttribute('aria-expanded', hasCurrent ? 'true' : 'false');
			toggle.innerHTML =
				'<span class="dashicons dashicons-arrow-right-alt2" aria-hidden="true"></span>' +
				'<span class="wpb-mantenimientos-group__label"></span>';
			toggle.querySelector('.wpb-mantenimientos-group__label').textContent = section;

			header.appendChild(toggle);
			submenu.appendChild(header);

			children.forEach(function (li) {
				if (!hasCurrent) {
					li.classList.add('wpb-mantenimientos-item--collapsed');
				}
				submenu.appendChild(li);
			});

			toggle.addEventListener('click', function (event) {
				event.preventDefault();
				event.stopPropagation();

				var open = !header.classList.contains('is-open');
				header.classList.toggle('is-open', open);
				toggle.setAttribute('aria-expanded', open ? 'true' : 'false');

				children.forEach(function (li) {
					li.classList.toggle('wpb-mantenimientos-item--collapsed', !open);
				});
			});
		});

		submenu.setAttribute('data-wpb-mantenimientos-grouped', '1');
	}

	function init() {
		var parent = findParentMenuItem();
		if (!parent) {
			return;
		}

		parent.classList.add('wpb-mantenimientos-menu');
		var submenu = parent.querySelector('ul.wp-submenu');
		if (submenu) {
			groupSubmenu(submenu);
		}
	}

	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', init);
	} else {
		init();
	}

	// WP sometimes rewrites admin menu after load; retry once.
	window.setTimeout(init, 300);
})();
