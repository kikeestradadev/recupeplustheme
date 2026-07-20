const mainMenuInstances = new WeakMap();
let mainMenuId = 0;

const mainMenu = (scope = document) => {
	/** @type {Array<() => void>} */
	const initialized = [];
	const roots = scope.querySelectorAll('[data-main-menu], .main-menu');

	if (roots.length > 0) {
		roots.forEach((root) => {
			if (mainMenuInstances.has(root)) return;

			const toggle = root.querySelector('[data-main-menu-toggle], .main-menu__hamburger');
			const panel = root.querySelector('[data-main-menu-panel], .main-menu__panel');
			const languageToggle = root.querySelector('[data-main-menu-language], .main-menu__language');
			const dropdownItems = Array.from(
				root.querySelectorAll('.main-menu__item--has-children')
			);
			const desktopQuery = window.matchMedia('(min-width: 1280px)');
			const controller = new AbortController();
			const { signal } = controller;
			const instanceId = ++mainMenuId;

			if (toggle && panel) {
				if (!panel.id) panel.id = `main-menu-panel-${instanceId}`;
				toggle.setAttribute('aria-controls', panel.id);
			}

			dropdownItems.forEach((item, index) => {
				const trigger = item.querySelector(
					'[data-main-menu-submenu-toggle], .main-menu__link'
				);
				const submenu = item.querySelector(
					'[data-main-menu-submenu], .main-menu__sub-menu'
				);
				if (!trigger || !submenu) return;

				if (!submenu.id) submenu.id = `main-menu-submenu-${instanceId}-${index + 1}`;
				trigger.setAttribute('aria-controls', submenu.id);
				trigger.setAttribute('aria-expanded', 'false');
			});

			const setMenuOpen = (open, restoreFocus = false) => {
				root.classList.toggle('main-menu--open', open);
				if (toggle) {
					toggle.setAttribute('aria-expanded', String(open));
					toggle.setAttribute('aria-label', open ? 'Cerrar menú' : 'Abrir menú');
				}
				if (panel)
					panel.setAttribute('aria-hidden', String(!desktopQuery.matches && !open));

				if (open && panel) {
					panel.querySelector('.main-menu__link')?.focus();
				} else if (restoreFocus) {
					toggle?.focus();
				}
			};

			const setDropdownOpen = (item, open) => {
				item.classList.toggle('main-menu__item--open', open);
				const trigger = item.querySelector(
					'[data-main-menu-submenu-toggle], .main-menu__link'
				);
				if (trigger) trigger.setAttribute('aria-expanded', String(open));
			};

			const closeDropdowns = (except = null) => {
				dropdownItems.forEach((item) => {
					if (item !== except) setDropdownOpen(item, false);
				});
			};

			const onToggle = () => {
				setMenuOpen(!root.classList.contains('main-menu--open'));
			};

			const onDocumentClick = (event) => {
				if (!root.contains(event.target)) {
					setMenuOpen(false);
					closeDropdowns();
				}
			};

			const onKeydown = (event) => {
				if (event.key !== 'Escape') return;
				const wasOpen = root.classList.contains('main-menu--open');
				const focusedDropdown = dropdownItems.find((item) =>
					item.contains(document.activeElement)
				);
				setMenuOpen(false, wasOpen);
				closeDropdowns();
				if (!wasOpen && focusedDropdown) {
					focusedDropdown
						.querySelector('[data-main-menu-submenu-toggle], .main-menu__link')
						?.focus();
				}
			};

			const onViewportChange = () => {
				setMenuOpen(false);
				closeDropdowns();
				if (panel) panel.setAttribute('aria-hidden', String(!desktopQuery.matches));
			};

			const syncLanguageToggle = (locale) => {
				if (!languageToggle) return;

				const nextLocale = locale === 'es' ? 'en' : 'es';
				const nextLabel =
					languageToggle.getAttribute(`data-${nextLocale}-label`) ||
					(nextLocale === 'en' ? 'English' : 'Español');

				languageToggle.setAttribute('data-locale', locale);
				languageToggle.setAttribute('aria-label', `Cambiar a ${nextLabel}`);

				languageToggle
					.querySelectorAll('.main-menu__language-flag, [data-flag]')
					.forEach((flag) => {
						flag.hidden = flag.getAttribute('data-flag') !== locale;
					});
			};

			const onLanguageToggle = () => {
				if (!languageToggle) return;

				const currentLocale = languageToggle.getAttribute('data-locale') || 'es';
				const nextLocale = currentLocale === 'es' ? 'en' : 'es';
				const nextHref = languageToggle.getAttribute(`data-${nextLocale}-href`);

				syncLanguageToggle(nextLocale);

				if (nextHref && nextHref !== '#') {
					window.location.href = nextHref;
				}
			};

			toggle?.addEventListener('click', onToggle, { signal });
			languageToggle?.addEventListener('click', onLanguageToggle, { signal });

			dropdownItems.forEach((item) => {
				const trigger = item.querySelector(
					'[data-main-menu-submenu-toggle], .main-menu__link'
				);
				if (!trigger) return;

				trigger.addEventListener(
					'click',
					(event) => {
						if (desktopQuery.matches) return;
						event.preventDefault();
						const willOpen = !item.classList.contains('main-menu__item--open');
						closeDropdowns(item);
						setDropdownOpen(item, willOpen);
					},
					{ signal }
				);
				item.addEventListener(
					'mouseenter',
					() => {
						if (desktopQuery.matches) setDropdownOpen(item, true);
					},
					{ signal }
				);
				item.addEventListener(
					'mouseleave',
					() => {
						if (desktopQuery.matches) setDropdownOpen(item, false);
					},
					{ signal }
				);
				item.addEventListener(
					'focusin',
					() => {
						if (desktopQuery.matches) setDropdownOpen(item, true);
					},
					{ signal }
				);
				item.addEventListener(
					'focusout',
					(event) => {
						if (desktopQuery.matches && !item.contains(event.relatedTarget)) {
							setDropdownOpen(item, false);
						}
					},
					{ signal }
				);
			});

			document.addEventListener('click', onDocumentClick, { signal });
			document.addEventListener('keydown', onKeydown, { signal });
			if (typeof desktopQuery.addEventListener === 'function') {
				desktopQuery.addEventListener('change', onViewportChange, { signal });
			} else {
				desktopQuery.addListener(onViewportChange);
			}

			onViewportChange();

			const destroy = () => {
				controller.abort();
				if (typeof desktopQuery.removeEventListener !== 'function') {
					desktopQuery.removeListener(onViewportChange);
				}
				setMenuOpen(false);
				closeDropdowns();
				toggle?.removeAttribute('aria-controls');
				panel?.removeAttribute('aria-hidden');
				mainMenuInstances.delete(root);
			};

			mainMenuInstances.set(root, destroy);
			initialized.push(destroy);
		});
	}

	return () => initialized.forEach((destroy) => destroy());
};

export default mainMenu;
