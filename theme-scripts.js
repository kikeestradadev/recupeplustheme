(() => {
  // js/components/initWhenVisible.js
  var initWhenVisible = (el, start, rootMargin = "200px") => {
    if (!el || typeof start !== "function") {
      return;
    }
    if (!("IntersectionObserver" in window)) {
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

  // js/components/mainMenu.js
  var mainMenuInstances = /* @__PURE__ */ new WeakMap();
  var mainMenuId = 0;
  var mainMenu = (scope = document) => {
    const initialized = [];
    const roots = scope.querySelectorAll("[data-main-menu], .main-menu");
    if (roots.length > 0) {
      roots.forEach((root) => {
        if (mainMenuInstances.has(root)) return;
        const toggle = root.querySelector("[data-main-menu-toggle], .main-menu__hamburger");
        const panel = root.querySelector("[data-main-menu-panel], .main-menu__panel");
        const languageToggle = root.querySelector("[data-main-menu-language], .main-menu__language");
        const dropdownItems = Array.from(
          root.querySelectorAll(".main-menu__item--has-children")
        );
        const desktopQuery = window.matchMedia("(min-width: 1280px)");
        const controller = new AbortController();
        const { signal } = controller;
        const instanceId = ++mainMenuId;
        if (toggle && panel) {
          if (!panel.id) panel.id = `main-menu-panel-${instanceId}`;
          toggle.setAttribute("aria-controls", panel.id);
        }
        dropdownItems.forEach((item, index) => {
          const trigger = item.querySelector(
            "[data-main-menu-submenu-toggle], .main-menu__link"
          );
          const submenu = item.querySelector(
            "[data-main-menu-submenu], .main-menu__sub-menu"
          );
          if (!trigger || !submenu) return;
          if (!submenu.id) submenu.id = `main-menu-submenu-${instanceId}-${index + 1}`;
          trigger.setAttribute("aria-controls", submenu.id);
          trigger.setAttribute("aria-expanded", "false");
        });
        const setMenuOpen = (open, restoreFocus = false) => {
          root.classList.toggle("main-menu--open", open);
          if (toggle) {
            toggle.setAttribute("aria-expanded", String(open));
            toggle.setAttribute("aria-label", open ? "Cerrar men\xFA" : "Abrir men\xFA");
          }
          if (panel)
            panel.setAttribute("aria-hidden", String(!desktopQuery.matches && !open));
          if (open && panel) {
            panel.querySelector(".main-menu__link")?.focus();
          } else if (restoreFocus) {
            toggle?.focus();
          }
        };
        const setDropdownOpen = (item, open) => {
          item.classList.toggle("main-menu__item--open", open);
          const trigger = item.querySelector(
            "[data-main-menu-submenu-toggle], .main-menu__link"
          );
          if (trigger) trigger.setAttribute("aria-expanded", String(open));
        };
        const closeDropdowns = (except = null) => {
          dropdownItems.forEach((item) => {
            if (item !== except) setDropdownOpen(item, false);
          });
        };
        const onToggle = () => {
          setMenuOpen(!root.classList.contains("main-menu--open"));
        };
        const onDocumentClick = (event) => {
          if (!root.contains(event.target)) {
            setMenuOpen(false);
            closeDropdowns();
          }
        };
        const onKeydown = (event) => {
          if (event.key !== "Escape") return;
          const wasOpen = root.classList.contains("main-menu--open");
          const focusedDropdown = dropdownItems.find(
            (item) => item.contains(document.activeElement)
          );
          setMenuOpen(false, wasOpen);
          closeDropdowns();
          if (!wasOpen && focusedDropdown) {
            focusedDropdown.querySelector("[data-main-menu-submenu-toggle], .main-menu__link")?.focus();
          }
        };
        const onViewportChange = () => {
          setMenuOpen(false);
          closeDropdowns();
          if (panel) panel.setAttribute("aria-hidden", String(!desktopQuery.matches));
        };
        const syncLanguageToggle = (locale) => {
          if (!languageToggle) return;
          const nextLocale = locale === "es" ? "en" : "es";
          const nextLabel = languageToggle.getAttribute(`data-${nextLocale}-label`) || (nextLocale === "en" ? "English" : "Espa\xF1ol");
          languageToggle.setAttribute("data-locale", locale);
          languageToggle.setAttribute("aria-label", `Cambiar a ${nextLabel}`);
          languageToggle.querySelectorAll(".main-menu__language-flag, [data-flag]").forEach((flag) => {
            flag.hidden = flag.getAttribute("data-flag") !== locale;
          });
        };
        const onLanguageToggle = () => {
          if (!languageToggle) return;
          const currentLocale = languageToggle.getAttribute("data-locale") || "es";
          const nextLocale = currentLocale === "es" ? "en" : "es";
          const nextHref = languageToggle.getAttribute(`data-${nextLocale}-href`);
          syncLanguageToggle(nextLocale);
          if (nextHref && nextHref !== "#") {
            window.location.href = nextHref;
          }
        };
        toggle?.addEventListener("click", onToggle, { signal });
        languageToggle?.addEventListener("click", onLanguageToggle, { signal });
        dropdownItems.forEach((item) => {
          const trigger = item.querySelector(
            "[data-main-menu-submenu-toggle], .main-menu__link"
          );
          if (!trigger) return;
          trigger.addEventListener(
            "click",
            (event) => {
              if (desktopQuery.matches) return;
              event.preventDefault();
              const willOpen = !item.classList.contains("main-menu__item--open");
              closeDropdowns(item);
              setDropdownOpen(item, willOpen);
            },
            { signal }
          );
          item.addEventListener(
            "mouseenter",
            () => {
              if (desktopQuery.matches) setDropdownOpen(item, true);
            },
            { signal }
          );
          item.addEventListener(
            "mouseleave",
            () => {
              if (desktopQuery.matches) setDropdownOpen(item, false);
            },
            { signal }
          );
          item.addEventListener(
            "focusin",
            () => {
              if (desktopQuery.matches) setDropdownOpen(item, true);
            },
            { signal }
          );
          item.addEventListener(
            "focusout",
            (event) => {
              if (desktopQuery.matches && !item.contains(event.relatedTarget)) {
                setDropdownOpen(item, false);
              }
            },
            { signal }
          );
        });
        document.addEventListener("click", onDocumentClick, { signal });
        document.addEventListener("keydown", onKeydown, { signal });
        if (typeof desktopQuery.addEventListener === "function") {
          desktopQuery.addEventListener("change", onViewportChange, { signal });
        } else {
          desktopQuery.addListener(onViewportChange);
        }
        onViewportChange();
        const destroy = () => {
          controller.abort();
          if (typeof desktopQuery.removeEventListener !== "function") {
            desktopQuery.removeListener(onViewportChange);
          }
          setMenuOpen(false);
          closeDropdowns();
          toggle?.removeAttribute("aria-controls");
          panel?.removeAttribute("aria-hidden");
          mainMenuInstances.delete(root);
        };
        mainMenuInstances.set(root, destroy);
        initialized.push(destroy);
      });
    }
    return () => initialized.forEach((destroy) => destroy());
  };
  var mainMenu_default = mainMenu;

  // js/components/swiperRoots.js
  var swiperRoots = (scope, selector) => {
    const root = scope || document;
    if (root.nodeType === 1 && typeof root.matches === "function" && root.matches(selector)) {
      return [root];
    }
    return Array.from(root.querySelectorAll(selector));
  };

  // js/components/momentSlider.js
  var momentSlider = (scope = document) => {
    const instances = [];
    const roots = swiperRoots(scope, ".moment-slider");
    if (roots.length > 0 && typeof Swiper !== "undefined") {
      roots.forEach((root) => {
        const swiperEl = root.querySelector(".moment-slider__swiper");
        if (!swiperEl) return;
        if (swiperEl.swiper) {
          instances.push(swiperEl.swiper);
          return;
        }
        const prevEl = root.querySelector(".moment-slider__nav--prev");
        const nextEl = root.querySelector(".moment-slider__nav--next");
        const paginationEl = root.querySelector(".moment-slider__pagination");
        const instance = new Swiper(swiperEl, {
          slidesPerView: "auto",
          loop: false,
          spaceBetween: 16,
          grabCursor: true,
          freeMode: true,
          pagination: {
            el: paginationEl,
            clickable: true
          },
          navigation: {
            prevEl,
            nextEl
          },
          breakpoints: {
            640: {
              spaceBetween: 20
            },
            960: {
              spaceBetween: 24
            }
          }
        });
        instances.push(instance);
      });
    }
    return instances;
  };
  var momentSlider_default = momentSlider;

  // js/components/productSlider.js
  var productSlider = (scope = document) => {
    const instances = [];
    const roots = swiperRoots(scope, ".product-slider");
    if (roots.length > 0 && typeof Swiper !== "undefined") {
      roots.forEach((root) => {
        root.querySelectorAll(".product-slider__slider").forEach((group) => {
          const swiperEl = group.querySelector(".product-slider__swiper");
          if (!swiperEl) return;
          if (swiperEl.swiper) {
            instances.push(swiperEl.swiper);
            return;
          }
          const prevEl = group.querySelector(".product-slider__nav--prev");
          const nextEl = group.querySelector(".product-slider__nav--next");
          const paginationEl = group.querySelector(".product-slider__pagination");
          const instance = new Swiper(swiperEl, {
            slidesPerView: "auto",
            loop: false,
            spaceBetween: 12,
            grabCursor: true,
            freeMode: true,
            pagination: {
              el: paginationEl,
              clickable: true
            },
            navigation: {
              prevEl,
              nextEl
            },
            breakpoints: {
              640: {
                spaceBetween: 16
              },
              960: {
                spaceBetween: 20
              }
            }
          });
          instances.push(instance);
        });
      });
    }
    return instances;
  };
  var productSlider_default = productSlider;

  // js/components/alliesSlider.js
  var alliesSlider = (scope = document) => {
    const instances = [];
    const roots = swiperRoots(scope, ".allies-slider");
    if (roots.length > 0 && typeof Swiper !== "undefined") {
      roots.forEach((root) => {
        const swiperEl = root.querySelector(".allies-slider__swiper");
        if (!swiperEl) return;
        if (swiperEl.swiper) {
          instances.push(swiperEl.swiper);
          return;
        }
        const paginationEl = root.querySelector(".allies-slider__pagination");
        const instance = new Swiper(swiperEl, {
          slidesPerView: "auto",
          loop: true,
          spaceBetween: 12,
          grabCursor: true,
          autoplay: {
            delay: 2500,
            disableOnInteraction: false,
            pauseOnMouseEnter: true
          },
          pagination: {
            el: paginationEl,
            clickable: true
          },
          breakpoints: {
            640: {
              spaceBetween: 16
            },
            960: {
              spaceBetween: 20
            }
          }
        });
        instances.push(instance);
      });
    }
    return instances;
  };
  var alliesSlider_default = alliesSlider;

  // js/components/storeLocator.js
  var storeLocatorInstances = /* @__PURE__ */ new WeakMap();
  var storeLocator = (scope = document) => {
    const initialized = [];
    const roots = scope.querySelectorAll("[data-store-locator], .store-locator");
    if (roots.length > 0) {
      roots.forEach((root) => {
        if (storeLocatorInstances.has(root)) return;
        const items = Array.from(root.querySelectorAll(".store-locator__item[data-store]"));
        const pins = Array.from(root.querySelectorAll(".store-locator__pin[data-store]"));
        const form = root.querySelector("[data-store-locator-search], .store-locator__search");
        const input = form?.querySelector(".store-locator__input");
        const status = root.querySelector("[data-store-locator-status]");
        const controller = new AbortController();
        const { signal } = controller;
        const setActive = (storeId) => {
          items.forEach((item) => {
            const active = item.getAttribute("data-store") === storeId;
            item.classList.toggle("store-locator__item--active", active);
            item.querySelector(".store-locator__item-btn")?.setAttribute(
              "aria-pressed",
              String(active)
            );
          });
          pins.forEach((pin) => {
            pin.classList.toggle(
              "store-locator__pin--active",
              pin.getAttribute("data-store") === storeId
            );
          });
        };
        items.forEach((item) => {
          const button = item.querySelector(".store-locator__item-btn");
          if (!button) return;
          button.addEventListener(
            "click",
            () => setActive(item.getAttribute("data-store") || void 0),
            { signal }
          );
        });
        form?.addEventListener(
          "submit",
          (event) => {
            event.preventDefault();
            const query = input?.value.trim().toLocaleLowerCase("es") || "";
            const matches = items.filter(
              (item) => !query || item.textContent.toLocaleLowerCase("es").includes(query)
            );
            items.forEach((item) => {
              item.hidden = !matches.includes(item);
            });
            const firstMatch = matches[0];
            setActive(firstMatch?.getAttribute("data-store") || void 0);
            if (status) {
              status.textContent = matches.length === 1 ? "Se encontr\xF3 una tienda." : `Se encontraron ${matches.length} tiendas.`;
            }
          },
          { signal }
        );
        const initialItem = items.find((item) => item.classList.contains("store-locator__item--active")) || items[0];
        if (initialItem) setActive(initialItem.getAttribute("data-store") || void 0);
        const destroy = () => {
          controller.abort();
          storeLocatorInstances.delete(root);
        };
        storeLocatorInstances.set(root, destroy);
        initialized.push(destroy);
      });
    }
    return () => initialized.forEach((destroy) => destroy());
  };
  var storeLocator_default = storeLocator;

  // js/components/waysSlider.js
  var waysSlider = (scope = document) => {
    const instances = [];
    const roots = swiperRoots(scope, ".ways-slider");
    if (roots.length > 0 && typeof Swiper !== "undefined") {
      roots.forEach((root) => {
        const swiperEl = root.querySelector(".ways-slider__swiper");
        if (!swiperEl) return;
        if (swiperEl.swiper) {
          instances.push(swiperEl.swiper);
          return;
        }
        const prevEl = root.querySelector(".ways-slider__nav--prev");
        const nextEl = root.querySelector(".ways-slider__nav--next");
        const paginationEl = root.querySelector(".ways-slider__pagination");
        const instance = new Swiper(swiperEl, {
          slidesPerView: "auto",
          loop: false,
          spaceBetween: 16,
          grabCursor: true,
          pagination: {
            el: paginationEl,
            clickable: true
          },
          navigation: {
            prevEl,
            nextEl
          },
          breakpoints: {
            640: {
              spaceBetween: 20
            },
            960: {
              spaceBetween: 24
            }
          }
        });
        instances.push(instance);
      });
    }
    return instances;
  };
  var waysSlider_default = waysSlider;

  // js/components/believeCards.js
  var believeCards = (scope = document) => {
    const instances = [];
    const roots = scope.querySelectorAll(".believe-cards");
    if (roots.length > 0 && typeof Swiper !== "undefined") {
      roots.forEach((root) => {
        const swiperEl = root.querySelector(".believe-cards__swiper");
        if (!swiperEl) return;
        if (swiperEl.swiper) {
          instances.push(swiperEl.swiper);
          return;
        }
        const prevEl = root.querySelector(".believe-cards__nav--prev");
        const nextEl = root.querySelector(".believe-cards__nav--next");
        const paginationEl = root.querySelector(".believe-cards__pagination");
        const instance = new Swiper(swiperEl, {
          slidesPerView: "auto",
          loop: false,
          spaceBetween: 16,
          grabCursor: true,
          watchOverflow: true,
          pagination: {
            el: paginationEl,
            clickable: true
          },
          navigation: {
            prevEl,
            nextEl
          },
          breakpoints: {
            640: {
              spaceBetween: 20
            },
            960: {
              spaceBetween: 24
            }
          }
        });
        instances.push(instance);
      });
    }
    return instances;
  };
  var believeCards_default = believeCards;

  // js/components/reviewsSlider.js
  var reviewsSlider = (scope = document) => {
    const instances = [];
    const roots = swiperRoots(scope, ".reviews-slider");
    if (roots.length > 0 && typeof Swiper !== "undefined") {
      roots.forEach((root) => {
        const swiperEl = root.querySelector(".reviews-slider__swiper");
        if (!swiperEl) return;
        if (swiperEl.swiper) {
          instances.push(swiperEl.swiper);
          return;
        }
        const prevEl = root.querySelector(".reviews-slider__nav--prev");
        const nextEl = root.querySelector(".reviews-slider__nav--next");
        const paginationEl = root.querySelector(".reviews-slider__pagination");
        const instance = new Swiper(swiperEl, {
          slidesPerView: "auto",
          loop: false,
          spaceBetween: 16,
          grabCursor: true,
          pagination: {
            el: paginationEl,
            clickable: true
          },
          navigation: {
            prevEl,
            nextEl
          },
          breakpoints: {
            640: {
              spaceBetween: 20
            },
            960: {
              spaceBetween: 24
            }
          }
        });
        instances.push(instance);
      });
    }
    return instances;
  };
  var reviewsSlider_default = reviewsSlider;

  // js/components/guideSlider.js
  var guideSlider = (scope = document) => {
    const instances = [];
    const roots = swiperRoots(scope, ".guide-slider");
    if (roots.length > 0 && typeof Swiper !== "undefined") {
      roots.forEach((root) => {
        const swiperEl = root.querySelector(".guide-slider__swiper");
        if (!swiperEl) return;
        if (swiperEl.swiper) {
          instances.push(swiperEl.swiper);
          return;
        }
        const prevEl = root.querySelector(".guide-slider__nav--prev");
        const nextEl = root.querySelector(".guide-slider__nav--next");
        const paginationEl = root.querySelector(".guide-slider__pagination");
        const instance = new Swiper(swiperEl, {
          slidesPerView: "auto",
          loop: false,
          spaceBetween: 12,
          grabCursor: true,
          pagination: {
            el: paginationEl,
            clickable: true
          },
          navigation: {
            prevEl,
            nextEl
          },
          breakpoints: {
            640: {
              spaceBetween: 16
            },
            960: {
              spaceBetween: 20
            }
          }
        });
        instances.push(instance);
      });
    }
    return instances;
  };
  var guideSlider_default = guideSlider;

  // js/components/ingredientsSlider.js
  var ingredientsSlider = (scope = document) => {
    const instances = [];
    const roots = swiperRoots(scope, ".ingredients-slider");
    if (roots.length > 0 && typeof Swiper !== "undefined") {
      roots.forEach((root) => {
        const swiperEl = root.querySelector(".ingredients-slider__swiper");
        if (!swiperEl) return;
        if (swiperEl.swiper) {
          instances.push(swiperEl.swiper);
          return;
        }
        const prevEl = root.querySelector(".ingredients-slider__nav--prev");
        const nextEl = root.querySelector(".ingredients-slider__nav--next");
        const paginationEl = root.querySelector(".ingredients-slider__pagination");
        const instance = new Swiper(swiperEl, {
          slidesPerView: "auto",
          loop: false,
          spaceBetween: 16,
          grabCursor: true,
          watchOverflow: true,
          pagination: {
            el: paginationEl,
            clickable: true
          },
          navigation: {
            prevEl,
            nextEl
          },
          breakpoints: {
            640: {
              spaceBetween: 20
            },
            960: {
              spaceBetween: 24
            }
          }
        });
        instances.push(instance);
      });
    }
    return instances;
  };
  var ingredientsSlider_default = ingredientsSlider;

  // js/components/blogSlider.js
  var blogSlider = (scope = document) => {
    const instances = [];
    const roots = swiperRoots(scope, ".blog-slider");
    if (roots.length > 0 && typeof Swiper !== "undefined") {
      roots.forEach((root) => {
        const swiperEl = root.querySelector(".blog-slider__swiper");
        if (!swiperEl) return;
        if (swiperEl.swiper) {
          instances.push(swiperEl.swiper);
          return;
        }
        const prevEl = root.querySelector(".blog-slider__nav--prev");
        const nextEl = root.querySelector(".blog-slider__nav--next");
        const paginationEl = root.querySelector(".blog-slider__pagination");
        const instance = new Swiper(swiperEl, {
          slidesPerView: "auto",
          loop: false,
          spaceBetween: 16,
          grabCursor: true,
          pagination: {
            el: paginationEl,
            clickable: true
          },
          navigation: {
            prevEl,
            nextEl
          },
          breakpoints: {
            640: {
              spaceBetween: 20
            },
            960: {
              spaceBetween: 24
            }
          }
        });
        instances.push(instance);
      });
    }
    return instances;
  };
  var blogSlider_default = blogSlider;

  // js/components/blogFilter.js
  var blogFilterInstances = /* @__PURE__ */ new WeakMap();
  var blogFilter = (scope = document) => {
    const initialized = [];
    const roots = scope.querySelectorAll("[data-blog-filter], .blog-filter");
    if (roots.length > 0) {
      roots.forEach((root) => {
        if (blogFilterInstances.has(root)) return;
        const requestedPerPage = Number(root.dataset.perPage);
        const perPage = Number.isFinite(requestedPerPage) && requestedPerPage > 0 ? Math.floor(requestedPerPage) : 6;
        const chips = Array.from(root.querySelectorAll(".blog-filter__chip[data-filter]"));
        const gridCards = Array.from(
          root.querySelectorAll("[data-blog-grid] .blog-filter__card")
        );
        const mobileTrack = root.querySelector("[data-blog-mobile-track]");
        const mobileSlides = Array.from(
          root.querySelectorAll("[data-blog-mobile-track] .blog-filter__slide")
        );
        const paginationEl = root.querySelector("[data-blog-pagination]");
        const filtersSwiperEl = root.querySelector(".blog-filter__filters-swiper");
        const filtersPrev = root.querySelector(".blog-filter__filters-nav--prev");
        const filtersNext = root.querySelector(".blog-filter__filters-nav--next");
        const postsSwiperEl = root.querySelector("[data-blog-swiper]");
        const bulletsEl = root.querySelector(".blog-filter__bullets");
        const desktopQuery = window.matchMedia("(min-width: 960px)");
        const controller = new AbortController();
        const { signal } = controller;
        let activeFilter = chips.find((chip) => chip.classList.contains("blog-filter__chip--active"))?.dataset.filter || "all";
        let currentPage = 1;
        let filtersSwiper = null;
        let postsSwiper = null;
        const getFiltered = (items) => items.filter(
          (item) => activeFilter === "all" || item.dataset.category === activeFilter
        );
        const setChipState = () => {
          chips.forEach((chip) => {
            const active = chip.dataset.filter === activeFilter;
            chip.classList.toggle("blog-filter__chip--active", active);
            chip.setAttribute("aria-pressed", String(active));
          });
        };
        const renderPagination = (totalPages) => {
          if (!paginationEl) return;
          paginationEl.innerHTML = "";
          if (totalPages <= 1) return;
          const addButton = (label, page, options = {}) => {
            const button = document.createElement("button");
            button.type = "button";
            button.className = `blog-filter__page${options.active ? " blog-filter__page--active" : ""}${options.nav ? " blog-filter__page--nav" : ""}`;
            button.textContent = label;
            button.disabled = Boolean(options.disabled || options.active);
            if (options.label) button.setAttribute("aria-label", options.label);
            if (options.active) button.setAttribute("aria-current", "page");
            if (!options.active && !options.disabled) {
              button.addEventListener("click", () => {
                currentPage = page;
                updateBaseView();
              });
            }
            paginationEl.appendChild(button);
          };
          addButton("\u2039", Math.max(1, currentPage - 1), {
            nav: true,
            disabled: currentPage === 1,
            label: "P\xE1gina anterior"
          });
          for (let page = 1; page <= totalPages; page += 1) {
            addButton(String(page), page, {
              active: page === currentPage,
              label: `P\xE1gina ${page}`
            });
          }
          addButton("\u203A", Math.min(totalPages, currentPage + 1), {
            nav: true,
            disabled: currentPage === totalPages,
            label: "P\xE1gina siguiente"
          });
        };
        const updateDesktopGrid = () => {
          const filtered = getFiltered(gridCards);
          const totalPages = Math.max(1, Math.ceil(filtered.length / perPage));
          if (currentPage > totalPages) currentPage = totalPages;
          const start = (currentPage - 1) * perPage;
          const visibleCards = new Set(filtered.slice(start, start + perPage));
          gridCards.forEach((card) => {
            card.hidden = !visibleCards.has(card);
          });
          renderPagination(totalPages);
        };
        const updateMobileItems = () => {
          mobileSlides.forEach((slide) => {
            slide.hidden = !getFiltered([slide]).length;
          });
        };
        const updateBaseView = () => {
          setChipState();
          updateDesktopGrid();
          updateMobileItems();
        };
        const destroyPostsSwiper = () => {
          if (!postsSwiper) return;
          postsSwiper.destroy(true, true);
          postsSwiper = null;
        };
        const destroyFiltersSwiper = () => {
          if (!filtersSwiper) return;
          filtersSwiper.destroy(true, true);
          filtersSwiper = null;
        };
        const enhanceMobilePosts = () => {
          if (typeof Swiper === "undefined" || !postsSwiperEl || !mobileTrack) return;
          destroyPostsSwiper();
          const filtered = getFiltered(mobileSlides);
          mobileTrack.innerHTML = "";
          filtered.forEach((slide) => {
            slide.hidden = false;
            mobileTrack.appendChild(slide);
          });
          if (!filtered.length) return;
          try {
            postsSwiper = new Swiper(postsSwiperEl, {
              slidesPerView: "auto",
              loop: false,
              spaceBetween: 16,
              grabCursor: true,
              watchOverflow: true,
              pagination: {
                el: bulletsEl,
                clickable: true
              },
              breakpoints: {
                640: {
                  spaceBetween: 20
                }
              }
            });
          } catch (error) {
            postsSwiper = null;
          }
        };
        const enhanceMobileFilters = () => {
          if (typeof Swiper === "undefined" || !filtersSwiperEl || filtersSwiper) return;
          try {
            filtersSwiper = new Swiper(filtersSwiperEl, {
              slidesPerView: "auto",
              loop: false,
              spaceBetween: 10,
              freeMode: true,
              grabCursor: true,
              watchOverflow: true,
              navigation: {
                prevEl: filtersPrev,
                nextEl: filtersNext
              }
            });
          } catch (error) {
            filtersSwiper = null;
          }
        };
        const updateEnhancements = () => {
          if (desktopQuery.matches) {
            destroyPostsSwiper();
            destroyFiltersSwiper();
            return;
          }
          enhanceMobileFilters();
          enhanceMobilePosts();
        };
        chips.forEach((chip) => {
          chip.addEventListener(
            "click",
            () => {
              activeFilter = chip.dataset.filter || "all";
              currentPage = 1;
              updateBaseView();
              if (!desktopQuery.matches) enhanceMobilePosts();
            },
            { signal }
          );
        });
        const onViewportChange = () => {
          updateBaseView();
          updateEnhancements();
        };
        if (typeof desktopQuery.addEventListener === "function") {
          desktopQuery.addEventListener("change", onViewportChange, { signal });
        } else {
          desktopQuery.addListener(onViewportChange);
        }
        updateBaseView();
        updateEnhancements();
        const destroy = () => {
          controller.abort();
          if (typeof desktopQuery.removeEventListener !== "function") {
            desktopQuery.removeListener(onViewportChange);
          }
          destroyPostsSwiper();
          destroyFiltersSwiper();
          if (mobileTrack) {
            mobileTrack.innerHTML = "";
            mobileSlides.forEach((slide) => {
              slide.hidden = false;
              mobileTrack.appendChild(slide);
            });
          }
          blogFilterInstances.delete(root);
        };
        blogFilterInstances.set(root, destroy);
        initialized.push(destroy);
      });
    }
    return () => initialized.forEach((destroy) => destroy());
  };
  var blogFilter_default = blogFilter;

  // js/components/productShowcase.js
  var productShowcaseInstances = /* @__PURE__ */ new WeakMap();
  var clamp = (value, min, max) => Math.min(max, Math.max(min, value));
  var productShowcase = (scope = document) => {
    const roots = scope.querySelectorAll("[data-product-showcase], .product-showcase");
    roots.forEach((root) => {
      if (productShowcaseInstances.has(root)) return;
      const thumbs = Array.from(root.querySelectorAll("[data-product-thumb]"));
      const stage = root.querySelector("[data-product-stage]");
      const zoom = root.querySelector("[data-product-zoom]");
      const qtyInput = root.querySelector("[data-product-qty-input]");
      const qtyDec = root.querySelector("[data-product-qty-dec]");
      const qtyInc = root.querySelector("[data-product-qty-inc]");
      const cartForm = root.querySelector("[data-product-cart]");
      const tabsSwiperEl = root.querySelector("[data-product-tabs-swiper]");
      const tabsPrev = root.querySelector(".product-showcase__tabs-nav--prev");
      const tabsNext = root.querySelector(".product-showcase__tabs-nav--next");
      const tabs = Array.from(root.querySelectorAll("[data-product-tab]"));
      const panels = Array.from(root.querySelectorAll("[data-product-panel]"));
      const desktopQuery = window.matchMedia("(min-width: 960px)");
      const controller = new AbortController();
      const { signal } = controller;
      let tabsSwiper = null;
      const setActiveThumb = (thumb) => {
        if (!stage || !thumb) return;
        const nextSrc = thumb.dataset.src;
        if (!nextSrc) return;
        stage.src = nextSrc;
        stage.alt = thumb.getAttribute("aria-label")?.replace(/^Ver\s+/i, "") || stage.alt;
        thumbs.forEach((item) => {
          const isActive = item === thumb;
          item.classList.toggle("product-showcase__thumb--active", isActive);
          item.setAttribute("aria-pressed", isActive ? "true" : "false");
        });
      };
      const updateQty = (nextValue) => {
        if (!qtyInput) return;
        const min = Number(qtyInput.min) || 1;
        const max = Number(qtyInput.max) || 99;
        qtyInput.value = String(clamp(nextValue, min, max));
      };
      const activateTab = (tabId) => {
        tabs.forEach((tab) => {
          const isActive = tab.dataset.productTab === tabId;
          tab.classList.toggle("product-showcase__tab--active", isActive);
          tab.setAttribute("aria-selected", isActive ? "true" : "false");
          tab.tabIndex = isActive ? 0 : -1;
        });
        panels.forEach((panel) => {
          const isActive = panel.dataset.productPanel === tabId;
          panel.classList.toggle("product-showcase__panel--active", isActive);
          panel.hidden = !isActive;
        });
      };
      const destroyTabsSwiper = () => {
        if (!tabsSwiper) return;
        tabsSwiper.destroy(true, true);
        tabsSwiper = null;
      };
      const enhanceMobileTabs = () => {
        if (typeof window.Swiper === "undefined" || !tabsSwiperEl || tabsSwiper) return;
        try {
          tabsSwiper = new window.Swiper(tabsSwiperEl, {
            slidesPerView: "auto",
            loop: false,
            spaceBetween: 30,
            freeMode: true,
            grabCursor: true,
            watchOverflow: true,
            navigation: {
              prevEl: tabsPrev,
              nextEl: tabsNext
            }
          });
        } catch (error) {
          tabsSwiper = null;
        }
      };
      const updateTabsEnhancement = () => {
        if (desktopQuery.matches) {
          destroyTabsSwiper();
          return;
        }
        enhanceMobileTabs();
      };
      const onZoomMove = (event) => {
        if (!zoom || !stage || !desktopQuery.matches) return;
        const rect = zoom.getBoundingClientRect();
        if (!rect.width || !rect.height) return;
        const x = (event.clientX - rect.left) / rect.width * 100;
        const y = (event.clientY - rect.top) / rect.height * 100;
        stage.style.transformOrigin = `${clamp(x, 0, 100)}% ${clamp(y, 0, 100)}%`;
      };
      const onZoomLeave = () => {
        if (!stage) return;
        stage.style.transformOrigin = "center center";
      };
      thumbs.forEach((thumb) => {
        thumb.addEventListener(
          "click",
          () => {
            setActiveThumb(thumb);
          },
          { signal }
        );
      });
      if (zoom) {
        zoom.addEventListener("mousemove", onZoomMove, { signal });
        zoom.addEventListener("mouseleave", onZoomLeave, { signal });
      }
      qtyDec?.addEventListener(
        "click",
        () => {
          updateQty(Number(qtyInput?.value || 1) - 1);
        },
        { signal }
      );
      qtyInc?.addEventListener(
        "click",
        () => {
          updateQty(Number(qtyInput?.value || 1) + 1);
        },
        { signal }
      );
      qtyInput?.addEventListener(
        "change",
        () => {
          updateQty(Number(qtyInput.value) || 1);
        },
        { signal }
      );
      cartForm?.addEventListener(
        "submit",
        (event) => {
          event.preventDefault();
        },
        { signal }
      );
      tabs.forEach((tab) => {
        tab.addEventListener(
          "click",
          () => {
            activateTab(tab.dataset.productTab);
            if (tabsSwiper) {
              const slide = tab.closest(".swiper-slide");
              const slideIndex = slide ? Array.from(slide.parentElement.children).indexOf(slide) : tabs.indexOf(tab);
              tabsSwiper.slideTo(slideIndex);
            }
          },
          { signal }
        );
        tab.addEventListener(
          "keydown",
          (event) => {
            const currentIndex = tabs.indexOf(tab);
            if (currentIndex < 0) return;
            let nextIndex = currentIndex;
            if (event.key === "ArrowRight" || event.key === "ArrowDown") {
              nextIndex = (currentIndex + 1) % tabs.length;
            } else if (event.key === "ArrowLeft" || event.key === "ArrowUp") {
              nextIndex = (currentIndex - 1 + tabs.length) % tabs.length;
            } else if (event.key === "Home") {
              nextIndex = 0;
            } else if (event.key === "End") {
              nextIndex = tabs.length - 1;
            } else {
              return;
            }
            event.preventDefault();
            const nextTab = tabs[nextIndex];
            activateTab(nextTab.dataset.productTab);
            nextTab.focus();
            if (tabsSwiper) {
              const slide = nextTab.closest(".swiper-slide");
              const slideIndex = slide ? Array.from(slide.parentElement.children).indexOf(slide) : nextIndex;
              tabsSwiper.slideTo(slideIndex);
            }
          },
          { signal }
        );
      });
      updateTabsEnhancement();
      if (typeof desktopQuery.addEventListener === "function") {
        desktopQuery.addEventListener("change", updateTabsEnhancement, { signal });
      } else if (typeof desktopQuery.addListener === "function") {
        const onChange = () => updateTabsEnhancement();
        desktopQuery.addListener(onChange);
        signal.addEventListener("abort", () => desktopQuery.removeListener(onChange), { once: true });
      }
      productShowcaseInstances.set(root, {
        destroy() {
          destroyTabsSwiper();
          controller.abort();
          productShowcaseInstances.delete(root);
        }
      });
    });
  };
  var productShowcase_default = productShowcase;

  // js/components/mushroomsSplit.js
  var mushroomsSplitInstances = /* @__PURE__ */ new WeakMap();
  var mushroomsSplit = (scope = document) => {
    const roots = scope.querySelectorAll(".mushrooms-split");
    roots.forEach((root) => {
      if (mushroomsSplitInstances.has(root)) return;
      const swiperEl = root.querySelector(".mushrooms-split__swiper");
      const paginationEl = root.querySelector(".mushrooms-split__pagination");
      const gridQuery = window.matchMedia("(min-width: 640px)");
      const controller = new AbortController();
      const { signal } = controller;
      let swiper = null;
      const destroySwiper = () => {
        if (!swiper) return;
        swiper.destroy(true, true);
        swiper = null;
      };
      const initSwiper = () => {
        if (typeof Swiper === "undefined" || !swiperEl || swiper) return;
        swiper = new Swiper(swiperEl, {
          slidesPerView: 1,
          spaceBetween: 16,
          grabCursor: true,
          watchOverflow: true,
          pagination: {
            el: paginationEl,
            clickable: true
          }
        });
      };
      const updateEnhancement = () => {
        if (gridQuery.matches) {
          destroySwiper();
          return;
        }
        initSwiper();
      };
      updateEnhancement();
      if (typeof gridQuery.addEventListener === "function") {
        gridQuery.addEventListener("change", updateEnhancement, { signal });
      } else if (typeof gridQuery.addListener === "function") {
        const onChange = () => updateEnhancement();
        gridQuery.addListener(onChange);
        signal.addEventListener("abort", () => gridQuery.removeListener(onChange), {
          once: true
        });
      }
      mushroomsSplitInstances.set(root, {
        destroy() {
          destroySwiper();
          controller.abort();
          mushroomsSplitInstances.delete(root);
        }
      });
    });
  };
  var mushroomsSplit_default = mushroomsSplit;

  // js/components/mainSlider.js
  var mainSlider = (scope = document) => {
    const instances = [];
    const roots = swiperRoots(scope, "[data-main-slider], .main-slider");
    if (roots.length > 0 && typeof Swiper !== "undefined") {
      roots.forEach((root) => {
        const swiperEl = root.querySelector(".main-slider__swiper");
        if (!swiperEl) return;
        if (swiperEl.swiper) {
          instances.push(swiperEl.swiper);
          return;
        }
        const prevEl = root.querySelector(".main-slider__nav--prev");
        const nextEl = root.querySelector(".main-slider__nav--next");
        const paginationEl = root.querySelector(".main-slider__pagination");
        const autoplayToggle = root.querySelector("[data-main-slider-autoplay]");
        const prefersReducedMotion = window.matchMedia(
          "(prefers-reduced-motion: reduce)"
        ).matches;
        const instance = new Swiper(swiperEl, {
          effect: "fade",
          fadeEffect: {
            crossFade: true
          },
          speed: prefersReducedMotion ? 0 : 900,
          loop: true,
          grabCursor: true,
          keyboard: {
            enabled: true,
            onlyInViewport: true
          },
          autoplay: prefersReducedMotion ? false : {
            delay: 6e3,
            disableOnInteraction: false,
            pauseOnMouseEnter: true
          },
          pagination: {
            el: paginationEl,
            clickable: true
          },
          navigation: {
            prevEl,
            nextEl
          },
          a11y: {
            enabled: true,
            prevSlideMessage: "Diapositiva anterior",
            nextSlideMessage: "Diapositiva siguiente",
            firstSlideMessage: "Esta es la primera diapositiva",
            lastSlideMessage: "Esta es la \xFAltima diapositiva",
            paginationBulletMessage: "Ir a la diapositiva {{index}}"
          },
          on: {
            init: () => root.classList.add("main-slider--ready")
          }
        });
        if (autoplayToggle) {
          if (prefersReducedMotion || !instance.autoplay) {
            autoplayToggle.hidden = true;
          } else {
            let isPaused = false;
            const label = autoplayToggle.querySelector(
              ".main-slider__autoplay-label"
            );
            const icon = autoplayToggle.querySelector(
              ".main-slider__autoplay-icon"
            );
            const updateAutoplayControl = () => {
              autoplayToggle.setAttribute("aria-pressed", String(isPaused));
              autoplayToggle.setAttribute(
                "aria-label",
                isPaused ? "Reanudar reproducci\xF3n autom\xE1tica" : "Pausar reproducci\xF3n autom\xE1tica"
              );
              if (label) label.textContent = isPaused ? "Reanudar" : "Pausar";
              if (icon) icon.textContent = isPaused ? "\u25B6" : "\u2161";
            };
            autoplayToggle.addEventListener("click", () => {
              isPaused = !isPaused;
              if (isPaused) {
                instance.autoplay.stop();
              } else {
                instance.autoplay.start();
              }
              updateAutoplayControl();
            });
            updateAutoplayControl();
          }
        }
        instances.push(instance);
      });
    }
    return instances;
  };
  var mainSlider_default = mainSlider;

  // js/components/mainBanner.js
  var mainBanner = (scope = document) => {
    const instances = [];
    const roots = swiperRoots(scope, ".main-banner");
    if (roots.length > 0 && typeof Swiper !== "undefined") {
      roots.forEach((root) => {
        const swiperEl = root.querySelector(".main-banner__swiper");
        if (!swiperEl) return;
        if (swiperEl.swiper) {
          instances.push(swiperEl.swiper);
          return;
        }
        const prevEl = root.querySelector(".main-banner__nav--prev");
        const nextEl = root.querySelector(".main-banner__nav--next");
        const paginationEl = root.querySelector(".main-banner__pagination");
        const prefersReducedMotion = window.matchMedia(
          "(prefers-reduced-motion: reduce)"
        ).matches;
        const instance = new Swiper(swiperEl, {
          effect: "fade",
          fadeEffect: {
            crossFade: true
          },
          speed: prefersReducedMotion ? 0 : 900,
          loop: true,
          grabCursor: true,
          autoplay: prefersReducedMotion ? false : {
            delay: 6e3,
            disableOnInteraction: false,
            pauseOnMouseEnter: true
          },
          pagination: {
            el: paginationEl,
            clickable: true
          },
          navigation: {
            prevEl,
            nextEl
          }
        });
        instances.push(instance);
      });
    }
    return instances;
  };
  var mainBanner_default = mainBanner;

  // js/components/foundersCards.js
  var foundersCards = (scope = document) => {
    const instances = [];
    const roots = scope.querySelectorAll(".founders-cards");
    if (roots.length > 0 && typeof Swiper !== "undefined") {
      roots.forEach((root) => {
        const swiperEl = root.querySelector(".founders-cards__swiper");
        if (!swiperEl) return;
        if (swiperEl.swiper) {
          instances.push(swiperEl.swiper);
          return;
        }
        const prevEl = root.querySelector(".founders-cards__nav--prev");
        const nextEl = root.querySelector(".founders-cards__nav--next");
        const paginationEl = root.querySelector(".founders-cards__pagination");
        const instance = new Swiper(swiperEl, {
          slidesPerView: "auto",
          loop: false,
          spaceBetween: 16,
          grabCursor: true,
          watchOverflow: true,
          pagination: {
            el: paginationEl,
            clickable: true
          },
          navigation: {
            prevEl,
            nextEl
          },
          breakpoints: {
            640: {
              spaceBetween: 20
            },
            960: {
              spaceBetween: 24
            }
          }
        });
        instances.push(instance);
      });
    }
    return instances;
  };
  var foundersCards_default = foundersCards;

  // js/theme-scripts.js
  var lazyInit = (selector, init) => {
    document.querySelectorAll(selector).forEach((el) => {
      initWhenVisible(el, () => init(el));
    });
  };
  var initComponents = () => {
    mainMenu_default();
    mainBanner_default();
    lazyInit("[data-main-slider], .main-slider", mainSlider_default);
    lazyInit(".moment-slider", momentSlider_default);
    lazyInit(".product-slider", productSlider_default);
    lazyInit(".allies-slider", alliesSlider_default);
    lazyInit(".ways-slider", waysSlider_default);
    lazyInit(".reviews-slider", reviewsSlider_default);
    lazyInit(".guide-slider", guideSlider_default);
    lazyInit(".ingredients-slider", ingredientsSlider_default);
    lazyInit(".blog-slider", blogSlider_default);
    productShowcase_default();
    mushroomsSplit_default();
    storeLocator_default();
    believeCards_default();
    foundersCards_default();
    blogFilter_default();
    if (typeof window.Prism !== "undefined" && document.querySelector('code[class*="language-"], [class*="language-"] code')) {
      window.Prism.highlightAll();
    }
  };
  if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", initComponents, { once: true });
  } else {
    initComponents();
  }
})();
