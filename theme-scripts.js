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

  // js/components/componentLoader.js
  var MEDIA_TIMEOUT_MS = 5e3;
  var VIEWPORT_MARGIN = "80px 0px";
  var refreshSliders = (section) => {
    section.querySelectorAll(".swiper").forEach((element) => {
      if (element.swiper && typeof element.swiper.update === "function") {
        element.swiper.update();
      }
    });
  };
  var isNearViewport = (element) => {
    const rect = element.getBoundingClientRect();
    if (rect.width === 0 && rect.height === 0) return true;
    const verticalLimit = (window.innerHeight || 0) + 640;
    const horizontalLimit = (window.innerWidth || 0) + 240;
    return rect.top < verticalLimit && rect.bottom > -200 && rect.left < horizontalLimit;
  };
  var unveilWithoutLibrary = (elements) => {
    elements.forEach((element) => {
      var _a;
      const picture = ((_a = element.parentElement) == null ? void 0 : _a.tagName) === "PICTURE" ? element.parentElement : null;
      picture == null ? void 0 : picture.querySelectorAll("source[data-srcset]").forEach((source) => {
        if (!source.getAttribute("srcset") && source.dataset.srcset) {
          source.srcset = source.dataset.srcset;
        }
      });
      if (!element.getAttribute("src") && element.dataset.src) {
        element.src = element.dataset.src;
      }
    });
  };
  var unveilElements = (elements) => {
    var _a, _b;
    if ((_b = (_a = window.lazySizes) == null ? void 0 : _a.loader) == null ? void 0 : _b.unveil) {
      elements.forEach((element) => window.lazySizes.loader.unveil(element));
      return;
    }
    unveilWithoutLibrary(elements);
  };
  var markReady = (section) => {
    if (section.classList.contains("component-loader--ready")) return;
    section.classList.add("component-loader--ready");
    section.setAttribute("aria-busy", "false");
    window.requestAnimationFrame(() => refreshSliders(section));
  };
  var waitForMedia = (section) => {
    const media = [...section.querySelectorAll("img[data-src], iframe[data-src]")].filter(
      (element) => element.closest(".swiper") || isNearViewport(element)
    );
    const pending = media.filter(
      (element) => element.tagName !== "IMG" || element.naturalWidth === 0
    );
    const blocking = pending.filter(isNearViewport);
    if (blocking.length === 0) {
      unveilElements(pending.filter((element) => !element.getAttribute("src")));
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
      if (element.dataset.lazySettled === "true") return;
      element.dataset.lazySettled = "true";
      finish();
    };
    blocking.forEach((element) => {
      element.addEventListener("load", () => settle(element), { once: true });
      element.addEventListener("error", () => settle(element), { once: true });
    });
    unveilElements(pending.filter((element) => !element.getAttribute("src")));
    blocking.forEach((element) => {
      if (element.tagName === "IMG" && element.naturalWidth > 0) settle(element);
    });
    window.setTimeout(() => {
      if (settled) return;
      settled = true;
      markReady(section);
    }, MEDIA_TIMEOUT_MS);
  };
  var componentLoader = (modules) => {
    const started = /* @__PURE__ */ new Set();
    const main = document.querySelector("main.lazy-sections");
    const sections = [];
    if (main) {
      [...main.children].filter((node) => node.tagName === "SECTION").slice(1).forEach((section) => sections.push(section));
    }
    document.querySelectorAll("[data-lazy-component]").forEach((node) => {
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
      if (section.dataset.lazyState === "done") return;
      section.dataset.lazyState = "done";
      window.requestAnimationFrame(() => {
        window.requestAnimationFrame(() => {
          runModules(section);
          waitForMedia(section);
        });
      });
    };
    if (sections.length === 0) return;
    sections.forEach((section) => {
      section.classList.add("component-loader");
      section.setAttribute("aria-busy", "true");
    });
    sections.forEach((section) => {
      initWhenVisible(section, () => activate(section), VIEWPORT_MARGIN);
    });
  };
  var componentLoader_default = componentLoader;

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
          var _a;
          root.classList.toggle("main-menu--open", open);
          if (toggle) {
            toggle.setAttribute("aria-expanded", String(open));
            toggle.setAttribute("aria-label", open ? "Cerrar men\xFA" : "Abrir men\xFA");
          }
          if (panel)
            panel.setAttribute("aria-hidden", String(!desktopQuery.matches && !open));
          if (open && panel) {
            (_a = panel.querySelector(".main-menu__link")) == null ? void 0 : _a.focus();
          } else if (restoreFocus) {
            toggle == null ? void 0 : toggle.focus();
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
          var _a;
          if (event.key !== "Escape") return;
          const wasOpen = root.classList.contains("main-menu--open");
          const focusedDropdown = dropdownItems.find(
            (item) => item.contains(document.activeElement)
          );
          setMenuOpen(false, wasOpen);
          closeDropdowns();
          if (!wasOpen && focusedDropdown) {
            (_a = focusedDropdown.querySelector("[data-main-menu-submenu-toggle], .main-menu__link")) == null ? void 0 : _a.focus();
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
        toggle == null ? void 0 : toggle.addEventListener("click", onToggle, { signal });
        languageToggle == null ? void 0 : languageToggle.addEventListener("click", onLanguageToggle, { signal });
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
          toggle == null ? void 0 : toggle.removeAttribute("aria-controls");
          panel == null ? void 0 : panel.removeAttribute("aria-hidden");
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

  // js/components/sliderLoader.js
  var LOADER_CLASS = "slider-loader";
  var READY_CLASS = "slider-loader--ready";
  var revealSlider = (root) => {
    if (!root || root.classList.contains(READY_CLASS)) return;
    root.classList.add(READY_CLASS);
  };
  var armSliderLoader = (root) => {
    if (!root) return;
    root.classList.add(LOADER_CLASS);
  };
  var markExistingSlider = (root) => {
    armSliderLoader(root);
    revealSlider(root);
  };
  var hasBlockingImages = (root) => [...root.querySelectorAll("img")].some((img) => {
    const src = img.getAttribute("src") || img.getAttribute("data-src");
    if (!src) return false;
    return !img.complete || img.naturalWidth === 0;
  });
  var forceEagerPendingImages = (root) => {
    [...root.querySelectorAll("img")].forEach((img) => {
      const src = img.getAttribute("src") || img.getAttribute("data-src");
      if (!src) return;
      if (img.complete && img.naturalWidth > 0) return;
      if (img.getAttribute("loading") === "lazy") {
        img.setAttribute("loading", "eager");
      }
      if (!img.getAttribute("src") && img.dataset.src) {
        img.src = img.dataset.src;
      }
    });
  };
  var withSliderLoader = (root, options = {}) => {
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
      const pending = [...root.querySelectorAll("img")].filter((img) => {
        const src = img.getAttribute("src") || img.getAttribute("data-src");
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
        img.addEventListener("load", done, { once: true });
        img.addEventListener("error", done, { once: true });
        if (img.complete) done();
      });
    };
    return {
      ...options,
      on: {
        ...userOn,
        init(swiper) {
          if (typeof userOn.init === "function") userOn.init(swiper);
          if (!hasBlockingImages(root)) {
            reveal();
            return;
          }
          watchImages();
        },
        imagesReady(swiper) {
          if (typeof userOn.imagesReady === "function") userOn.imagesReady(swiper);
          reveal();
        }
      }
    };
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
          markExistingSlider(root);
          instances.push(swiperEl.swiper);
          return;
        }
        const prevEl = root.querySelector(".moment-slider__nav--prev");
        const nextEl = root.querySelector(".moment-slider__nav--next");
        const paginationEl = root.querySelector(".moment-slider__pagination");
        const instance = new Swiper(
          swiperEl,
          withSliderLoader(root, {
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
          })
        );
        instances.push(instance);
      });
    }
    return instances;
  };
  var momentSlider_default = momentSlider;

  // js/components/productSlider.js
  var productSlideMatches = (presentacion, filter) => presentacion === filter;
  var hiddenClassFor = (slide) => slide.classList.contains("product-showcase__related-card") ? "product-showcase__related-card--hidden" : "product-slider__slide--hidden";
  var applyProductFilter = (root, value) => {
    root.querySelectorAll("[data-product-filter]").forEach((button) => {
      const active = button.getAttribute("data-product-filter") === value;
      button.classList.toggle("product-slider__filter--active", active);
      button.setAttribute("aria-pressed", active ? "true" : "false");
    });
    root.querySelectorAll("[data-presentacion], [data-product-group]").forEach((slide) => {
      const show = productSlideMatches(slide.getAttribute("data-product-group") || slide.getAttribute("data-presentacion"), value);
      slide.classList.toggle(hiddenClassFor(slide), !show);
    });
    const groups = root.matches(".product-showcase__related") ? [root] : [...root.querySelectorAll(".product-slider__group")];
    groups.forEach((group) => {
      const slides = group.querySelectorAll("[data-presentacion], [data-product-group]");
      if (!slides.length) return;
      const visible = [...slides].some((slide) => !slide.classList.contains(hiddenClassFor(slide)));
      const empty = group.querySelector("[data-product-filter-empty]");
      const slider = group.querySelector(".product-slider__slider, .product-showcase__related-slider");
      if (empty) empty.hidden = visible;
      if (slider) slider.hidden = !visible;
    });
    root.querySelectorAll(".product-slider__swiper, .product-showcase__related-swiper").forEach((el) => {
      if (!el.swiper) return;
      el.swiper.update();
      el.swiper.slideTo(0, 0);
    });
  };
  var bindProductFilter = (root) => {
    if (root.dataset.productFilterBound === "true") return;
    root.dataset.productFilterBound = "true";
    root.querySelectorAll("[data-product-filter]").forEach((button) => {
      button.addEventListener("click", () => {
        applyProductFilter(root, button.getAttribute("data-product-filter"));
      });
    });
  };
  var bindFilterArrows = (root) => {
    if (root.dataset.filterArrowsBound === "true") return;
    const scroller = root.querySelector(".product-slider__filter-bar .product-slider__filters");
    const prev = root.querySelector("[data-slider-filter-prev]");
    const next = root.querySelector("[data-slider-filter-next]");
    if (!scroller || !prev || !next) return;
    root.dataset.filterArrowsBound = "true";
    const updateFilterArrows = () => {
      const maxScroll = scroller.scrollWidth - scroller.clientWidth;
      prev.disabled = maxScroll <= 1 || scroller.scrollLeft <= 1;
      next.disabled = maxScroll <= 1 || scroller.scrollLeft >= maxScroll - 1;
    };
    const scrollFilters = (direction) => {
      const amount = Math.max(120, Math.round(scroller.clientWidth * 0.75));
      scroller.scrollBy({ left: direction * amount, behavior: "smooth" });
    };
    prev.addEventListener("click", () => scrollFilters(-1));
    next.addEventListener("click", () => scrollFilters(1));
    scroller.addEventListener("scroll", updateFilterArrows, { passive: true });
    if (typeof ResizeObserver !== "undefined") {
      const observer = new ResizeObserver(updateFilterArrows);
      observer.observe(scroller);
    }
    updateFilterArrows();
  };
  var productSlider = (scope = document) => {
    const instances = [];
    const roots = [
      ...swiperRoots(scope, ".product-slider"),
      ...swiperRoots(scope, ".product-showcase__related")
    ];
    roots.forEach((root) => {
      bindProductFilter(root);
      bindFilterArrows(root);
    });
    if (roots.length > 0 && typeof Swiper !== "undefined") {
      roots.forEach((root) => {
        root.querySelectorAll(".product-slider__slider").forEach((group) => {
          const swiperEl = group.querySelector(".product-slider__swiper");
          if (!swiperEl) return;
          if (swiperEl.swiper) {
            markExistingSlider(group);
            instances.push(swiperEl.swiper);
            return;
          }
          const prevEl = group.querySelector(".product-slider__nav--prev");
          const nextEl = group.querySelector(".product-slider__nav--next");
          const paginationEl = group.querySelector(".product-slider__pagination");
          const instance = new Swiper(
            swiperEl,
            withSliderLoader(group, {
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
            })
          );
          instances.push(instance);
        });
        const activeFilter = root.querySelector('[data-product-filter][aria-pressed="true"]');
        applyProductFilter(root, activeFilter ? activeFilter.getAttribute("data-product-filter") : "tarro");
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
          markExistingSlider(root);
          instances.push(swiperEl.swiper);
          return;
        }
        const paginationEl = root.querySelector(".allies-slider__pagination");
        const slideCount = swiperEl.querySelectorAll(".swiper-slide").length;
        const canLoop = slideCount > 4;
        const instance = new Swiper(
          swiperEl,
          withSliderLoader(root, {
            slidesPerView: "auto",
            loop: canLoop,
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
          })
        );
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
        const panels = Array.from(root.querySelectorAll(".store-locator__map-panel[data-store]"));
        const controller = new AbortController();
        const { signal } = controller;
        const setActive = (storeId) => {
          items.forEach((item) => {
            const active = item.getAttribute("data-store") === storeId;
            const button = item.querySelector(".store-locator__item-btn");
            item.classList.toggle("store-locator__item--active", active);
            if (button) {
              button.setAttribute("aria-selected", String(active));
              button.setAttribute("tabindex", active ? "0" : "-1");
            }
          });
          panels.forEach((panel) => {
            const active = panel.getAttribute("data-store") === storeId;
            panel.classList.toggle("store-locator__map-panel--active", active);
            panel.setAttribute("aria-hidden", String(!active));
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
          markExistingSlider(root);
          instances.push(swiperEl.swiper);
          return;
        }
        const prevEl = root.querySelector(".ways-slider__nav--prev");
        const nextEl = root.querySelector(".ways-slider__nav--next");
        const paginationEl = root.querySelector(".ways-slider__pagination");
        const instance = new Swiper(
          swiperEl,
          withSliderLoader(root, {
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
          })
        );
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
          markExistingSlider(root);
          instances.push(swiperEl.swiper);
          return;
        }
        const prevEl = root.querySelector(".believe-cards__nav--prev");
        const nextEl = root.querySelector(".believe-cards__nav--next");
        const paginationEl = root.querySelector(".believe-cards__pagination");
        const instance = new Swiper(
          swiperEl,
          withSliderLoader(root, {
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
          })
        );
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
          markExistingSlider(root);
          instances.push(swiperEl.swiper);
          return;
        }
        const prevEl = root.querySelector(".reviews-slider__nav--prev");
        const nextEl = root.querySelector(".reviews-slider__nav--next");
        const paginationEl = root.querySelector(".reviews-slider__pagination");
        const instance = new Swiper(
          swiperEl,
          withSliderLoader(root, {
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
          })
        );
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
          markExistingSlider(root);
          instances.push(swiperEl.swiper);
          return;
        }
        const prevEl = root.querySelector(".guide-slider__nav--prev");
        const nextEl = root.querySelector(".guide-slider__nav--next");
        const paginationEl = root.querySelector(".guide-slider__pagination");
        const instance = new Swiper(
          swiperEl,
          withSliderLoader(root, {
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
          })
        );
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
          markExistingSlider(root);
          instances.push(swiperEl.swiper);
          return;
        }
        const prevEl = root.querySelector(".ingredients-slider__nav--prev");
        const nextEl = root.querySelector(".ingredients-slider__nav--next");
        const paginationEl = root.querySelector(".ingredients-slider__pagination");
        const instance = new Swiper(
          swiperEl,
          withSliderLoader(root, {
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
          })
        );
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
          markExistingSlider(root);
          instances.push(swiperEl.swiper);
          return;
        }
        const prevEl = root.querySelector(".blog-slider__nav--prev");
        const nextEl = root.querySelector(".blog-slider__nav--next");
        const paginationEl = root.querySelector(".blog-slider__pagination");
        const instance = new Swiper(
          swiperEl,
          withSliderLoader(root, {
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
          })
        );
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
        var _a;
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
        let activeFilter = ((_a = chips.find((chip) => chip.classList.contains("blog-filter__chip--active"))) == null ? void 0 : _a.dataset.filter) || "all";
        let currentPage = 1;
        let filtersSwiper = null;
        let postsSwiper = null;
        const getFiltered = (items) => items.filter((item) => {
          if (activeFilter === "all") return true;
          const cats = String(item.dataset.category || "").trim().split(/\s+/).filter(Boolean);
          return cats.includes(activeFilter);
        });
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

  // js/components/cartDrawer.js
  var cartDrawerInstances = /* @__PURE__ */ new WeakMap();
  var getCartConfig = () => window.wpbCart || {};
  var wcAjaxUrl = (endpoint) => {
    const cfg = getCartConfig();
    if (cfg.ajaxUrl && String(cfg.ajaxUrl).includes("%%endpoint%%")) {
      return String(cfg.ajaxUrl).replace("%%endpoint%%", endpoint);
    }
    return `${cfg.wcAjaxUrl || "/?wc-ajax="}${endpoint}`;
  };
  var parseProductQtys = (content) => {
    try {
      const raw = (content == null ? void 0 : content.getAttribute("data-cart-product-qtys")) || "{}";
      const parsed = JSON.parse(raw);
      return parsed && typeof parsed === "object" && !Array.isArray(parsed) ? parsed : {};
    } catch (error) {
      return {};
    }
  };
  var syncShowcaseWithCart = () => {
    var _a;
    const content = document.querySelector("[data-cart-drawer-content]");
    const total = Number(content == null ? void 0 : content.getAttribute("data-cart-total-count")) || Number((_a = document.querySelector("[data-cart-drawer-badge]")) == null ? void 0 : _a.textContent) || 0;
    const productQtys = parseProductQtys(content);
    document.querySelectorAll("[data-cart-count]").forEach((el) => {
      el.textContent = String(total);
      el.hidden = total < 1;
    });
    document.querySelectorAll("[data-product-showcase]").forEach((root) => {
      const productId = Number(root.getAttribute("data-product-id") || 0);
      const qtyInput = root.querySelector("[data-product-qty-input]");
      if (!qtyInput || !productId) return;
      const inCart = Number(productQtys[productId] || productQtys[String(productId)] || 0);
      if (inCart > 0) {
        qtyInput.value = String(inCart);
      }
    });
    if (window.wpbCart) {
      window.wpbCart.cartCount = total;
      window.wpbCart.productQtys = productQtys;
    }
  };
  var applyFragments = (fragments) => {
    if (!fragments || typeof fragments !== "object") return;
    Object.keys(fragments).forEach((selector) => {
      if (!selector || selector === "wpb_cart_product_qtys") return;
      const html = fragments[selector];
      if (typeof html !== "string") return;
      let nodes;
      try {
        nodes = document.querySelectorAll(selector);
      } catch (error) {
        return;
      }
      nodes.forEach((node) => {
        const template = document.createElement("template");
        template.innerHTML = html.trim();
        const next = template.content.firstElementChild;
        if (next) {
          node.replaceWith(next);
        }
      });
    });
    syncShowcaseWithCart();
    document.dispatchEvent(new CustomEvent("wpb_cart_updated", { detail: { fragments } }));
  };
  var formatCartPrice = (amount) => {
    const rounded = Math.round(Number(amount) || 0);
    const grouped = String(Math.abs(rounded)).replace(/\B(?=(\d{3})+(?!\d))/g, " ");
    return `\u20A1${rounded < 0 ? "-" : ""}${grouped}`;
  };
  var cartRetryDelays = [350, 900];
  var CART_LOCK_KEY = "wpbCartLock";
  var CART_HASH_KEY = "wpbCartHash";
  var TAB_ID = `wpb-${Math.random().toString(36).slice(2)}`;
  var desiredQty = /* @__PURE__ */ new Map();
  var confirmedQty = /* @__PURE__ */ new Map();
  var rowSnapshots = /* @__PURE__ */ new Map();
  var pendingAdds = [];
  var draining = false;
  var cartQueueBound = false;
  var cartMessage = (key, fallback) => {
    const i18n = getCartConfig().i18n || {};
    return i18n[key] || fallback;
  };
  var rememberCartHash = (hash) => {
    if (!hash) return;
    if (!window.wpbCart) window.wpbCart = {};
    window.wpbCart.cartHash = hash;
    try {
      localStorage.setItem(CART_HASH_KEY, String(hash));
    } catch (error) {
    }
  };
  var knownCartHash = () => String(getCartConfig().cartHash || "");
  var sleep = (ms) => new Promise((resolve) => {
    window.setTimeout(resolve, ms);
  });
  var showCartNotice = (message, tone = "info") => {
    const root = document.querySelector("[data-cart-drawer]");
    if (!root) return;
    let el = root.querySelector("[data-cart-drawer-notice]");
    if (!el) {
      el = document.createElement("p");
      el.className = "cart-drawer__notice";
      el.setAttribute("data-cart-drawer-notice", "");
      el.setAttribute("role", "status");
      el.setAttribute("aria-live", "polite");
      const panel = root.querySelector("[data-cart-drawer-panel], .cart-drawer__panel") || root;
      const header = panel.querySelector(".cart-drawer__header");
      if (header) header.insertAdjacentElement("afterend", el);
      else panel.prepend(el);
    }
    el.classList.toggle("cart-drawer__notice--error", tone === "error");
    el.hidden = !message;
    el.textContent = message || "";
  };
  var readLock = () => {
    try {
      return JSON.parse(localStorage.getItem(CART_LOCK_KEY) || "null");
    } catch (error) {
      return null;
    }
  };
  var acquireCartLock = async () => {
    const started = Date.now();
    while (Date.now() - started < 8e3) {
      const lock = readLock();
      if (!lock || lock.tab === TAB_ID || Number(lock.until) < Date.now()) {
        try {
          localStorage.setItem(
            CART_LOCK_KEY,
            JSON.stringify({ tab: TAB_ID, until: Date.now() + 8e3 })
          );
        } catch (error) {
          return;
        }
        const confirmLock = readLock();
        if (!confirmLock || confirmLock.tab === TAB_ID) return;
      }
      await sleep(40);
    }
  };
  var releaseCartLock = () => {
    const lock = readLock();
    if (lock && lock.tab === TAB_ID) {
      try {
        localStorage.removeItem(CART_LOCK_KEY);
      } catch (error) {
      }
    }
  };
  var foreignCartHash = () => {
    try {
      const stored = localStorage.getItem(CART_HASH_KEY) || "";
      const mine = knownCartHash();
      return Boolean(stored && mine && stored !== mine);
    } catch (error) {
      return false;
    }
  };
  var responseFailed = (response) => (response == null ? void 0 : response.ok) === false || typeof (response == null ? void 0 : response.status) === "number" && response.status >= 400;
  var conflictPayload = (data) => {
    if (!data || typeof data !== "object") return null;
    if (data.code === "cart_conflict") return data;
    if (data.data && data.data.code === "cart_conflict") return data.data;
    return null;
  };
  var postCart = async (endpoint, params) => {
    let response;
    try {
      response = await fetch(wcAjaxUrl(endpoint), {
        method: "POST",
        credentials: "same-origin",
        headers: {
          "Content-Type": "application/x-www-form-urlencoded; charset=UTF-8"
        },
        body: params.toString()
      });
    } catch (error) {
      const failure = new Error("network");
      failure.code = "network";
      throw failure;
    }
    let data = null;
    try {
      data = await response.json();
    } catch (error) {
      const failure = new Error("network");
      failure.code = "network";
      throw failure;
    }
    if (data && data.cart_hash) rememberCartHash(data.cart_hash);
    const conflict = conflictPayload(data);
    if (conflict) {
      const failure = new Error("conflict");
      failure.code = "conflict";
      failure.data = conflict.fragments ? conflict : data;
      throw failure;
    }
    if (responseFailed(response) || (data == null ? void 0 : data.success) === false) {
      const failure = new Error("http");
      failure.code = "http";
      failure.data = data;
      throw failure;
    }
    return data && typeof data === "object" ? data : null;
  };
  var withRetry = async (task) => {
    const delays = cartRetryDelays.length ? cartRetryDelays : [];
    const attempts = delays.length + 1;
    let lastError = null;
    for (let attempt = 0; attempt < attempts; attempt += 1) {
      try {
        return await task();
      } catch (error) {
        lastError = error;
        if ((error == null ? void 0 : error.code) === "conflict" || attempt === attempts - 1) throw error;
        showCartNotice(
          cartMessage("retry", "Conexi\xF3n inestable. Reintentando\u2026"),
          "info"
        );
        await sleep(delays[attempt] || 0);
      }
    }
    throw lastError;
  };
  var paintTotals = (root = document.querySelector("[data-cart-drawer]")) => {
    if (!root) return;
    let count = 0;
    let subtotal = 0;
    root.querySelectorAll("[data-cart-item]").forEach((row) => {
      var _a;
      const qty = Number(((_a = row.querySelector("[data-cart-item-input]")) == null ? void 0 : _a.value) || 0);
      const unit = Number(row.getAttribute("data-unit-price") || 0);
      count += qty;
      subtotal += unit * qty;
      const line = row.querySelector("[data-cart-item-total]");
      if (line) line.textContent = formatCartPrice(unit * qty);
    });
    document.querySelectorAll("[data-cart-drawer-badge], [data-cart-count]").forEach((el) => {
      el.textContent = String(count);
      el.hidden = count < 1;
    });
    const countEl = root.querySelector("[data-cart-drawer-count]");
    if (countEl) {
      countEl.textContent = String(count);
      countEl.hidden = count < 1;
    }
    const subtotalEl = root.querySelector("[data-cart-drawer-subtotal]");
    if (subtotalEl) subtotalEl.textContent = formatCartPrice(subtotal);
    const content = root.querySelector("[data-cart-drawer-content]");
    if (content) content.setAttribute("data-cart-total-count", String(count));
    if (window.wpbCart) window.wpbCart.cartCount = count;
  };
  var showEmptyState = (root) => {
    const content = root == null ? void 0 : root.querySelector("[data-cart-drawer-content]");
    if (!content || content.querySelector(".cart-drawer__empty")) return;
    content.innerHTML = `
		<div class="cart-drawer__empty">
			<p class="cart-drawer__empty-message woocommerce-mini-cart__empty-message">${cartMessage("empty", "Tu carrito est\xE1 vac\xEDo.")}</p>
			<button class="cart-drawer__continue" type="button" data-cart-drawer-close>${cartMessage("continue", "Seguir comprando")}</button>
		</div>
	`;
  };
  var seedConfirmedCart = (root = document.querySelector("[data-cart-drawer]")) => {
    confirmedQty.clear();
    rowSnapshots.clear();
    root == null ? void 0 : root.querySelectorAll("[data-cart-item]").forEach((row) => {
      var _a;
      const key = row.getAttribute("data-cart-item-key");
      if (!key) return;
      confirmedQty.set(key, Number(((_a = row.querySelector("[data-cart-item-input]")) == null ? void 0 : _a.value) || 0));
      rowSnapshots.set(key, row.outerHTML);
    });
  };
  var rowByKey = (root, key) => {
    const escaped = window.CSS && typeof window.CSS.escape === "function" ? window.CSS.escape(key) : key;
    return (root == null ? void 0 : root.querySelector(`[data-cart-item-key="${escaped}"]`)) || null;
  };
  var rollbackCartKey = (key) => {
    const root = document.querySelector("[data-cart-drawer]");
    if (!root) return;
    const qty = confirmedQty.get(key) || 0;
    let row = rowByKey(root, key);
    if (qty < 1) {
      row == null ? void 0 : row.remove();
      if (!root.querySelector("[data-cart-item]")) showEmptyState(root);
      paintTotals(root);
      return;
    }
    if (!row && rowSnapshots.get(key)) {
      const content = root.querySelector("[data-cart-drawer-content]");
      const empty = content == null ? void 0 : content.querySelector(".cart-drawer__empty");
      empty == null ? void 0 : empty.remove();
      let list = content == null ? void 0 : content.querySelector("[data-cart-drawer-list]");
      if (!list && content) {
        content.insertAdjacentHTML(
          "afterbegin",
          '<ul class="cart-drawer__list woocommerce-mini-cart cart_list product_list_widget" data-cart-drawer-list></ul>'
        );
        list = content.querySelector("[data-cart-drawer-list]");
      }
      list == null ? void 0 : list.insertAdjacentHTML("beforeend", rowSnapshots.get(key));
      row = rowByKey(root, key);
    }
    const input = row == null ? void 0 : row.querySelector("[data-cart-item-input]");
    if (input) input.value = String(qty);
    paintTotals(root);
  };
  var acceptServerCart = (data, message) => {
    var _a;
    const fragments = (data == null ? void 0 : data.fragments) || ((_a = data == null ? void 0 : data.data) == null ? void 0 : _a.fragments);
    if (fragments) applyFragments(fragments);
    seedConfirmedCart();
    desiredQty.clear();
    showCartNotice(message, "error");
  };
  var syncFromOtherTab = async () => {
    const data = await postCart("get_refreshed_fragments", new URLSearchParams());
    acceptServerCart(
      data,
      cartMessage("conflict", "El carrito cambi\xF3 en otra ventana. Mostramos la versi\xF3n m\xE1s reciente.")
    );
  };
  var updateCartItemQty = (cartItemKey, quantity) => {
    const cfg = getCartConfig();
    const body = new URLSearchParams();
    body.set("cart_item_key", cartItemKey);
    body.set("quantity", String(quantity));
    body.set("nonce", cfg.cartNonce || "");
    if (knownCartHash()) body.set("cart_hash", knownCartHash());
    return postCart("wpb_update_cart_item", body);
  };
  var postAddToCart = ({ productId, quantity }) => {
    const body = new URLSearchParams();
    body.set("product_id", String(productId));
    body.set("quantity", String(quantity));
    if (knownCartHash()) body.set("cart_hash", knownCartHash());
    return postCart("add_to_cart", body);
  };
  var noteConfirmed = (key, qty) => {
    const root = document.querySelector("[data-cart-drawer]");
    if (qty < 1) {
      confirmedQty.delete(key);
      rowSnapshots.delete(key);
      return;
    }
    confirmedQty.set(key, qty);
    const row = rowByKey(root, key);
    if (row) rowSnapshots.set(key, row.outerHTML);
  };
  var drainCartQueue = async () => {
    if (draining) return;
    draining = true;
    try {
      await acquireCartLock();
      if (foreignCartHash()) {
        await syncFromOtherTab();
        pendingAdds.splice(0).forEach((job) => job.resolve({ conflict: true }));
        return;
      }
      let last = null;
      let reconciled = false;
      let failed = false;
      while (desiredQty.size || pendingAdds.length) {
        if (desiredQty.size) {
          const key = desiredQty.keys().next().value;
          const qty = desiredQty.get(key);
          desiredQty.delete(key);
          try {
            last = await withRetry(() => updateCartItemQty(key, qty));
            noteConfirmed(key, qty);
          } catch (error) {
            if ((error == null ? void 0 : error.code) === "conflict") {
              reconciled = true;
              acceptServerCart(
                error.data,
                cartMessage(
                  "conflict",
                  "El carrito cambi\xF3 en otra ventana. Mostramos la versi\xF3n m\xE1s reciente."
                )
              );
              pendingAdds.splice(0).forEach((job2) => job2.reject(error));
              break;
            }
            failed = true;
            rollbackCartKey(key);
            showCartNotice(
              cartMessage(
                "rollback",
                "No se pudo actualizar el carrito. Se restaur\xF3 la cantidad anterior."
              ),
              "error"
            );
          }
          continue;
        }
        const job = pendingAdds.shift();
        try {
          last = await withRetry(() => postAddToCart(job));
          last = last ? { ...last, added: true } : last;
          job.resolve(last);
        } catch (error) {
          if ((error == null ? void 0 : error.code) === "conflict") {
            reconciled = true;
            acceptServerCart(
              error.data,
              cartMessage(
                "conflict",
                "El carrito cambi\xF3 en otra ventana. Mostramos la versi\xF3n m\xE1s reciente."
              )
            );
            pendingAdds.splice(0).forEach((queued) => queued.reject(error));
            break;
          } else {
            failed = true;
            showCartNotice(
              cartMessage("addError", "No se pudo agregar el producto. Intenta de nuevo."),
              "error"
            );
          }
          job.reject(error);
        }
      }
      if (!reconciled && desiredQty.size === 0 && pendingAdds.length === 0 && (last == null ? void 0 : last.fragments)) {
        applyFragments(last.fragments);
        seedConfirmedCart();
        if (last.added && window.jQuery) {
          window.jQuery(document.body).trigger("added_to_cart", [last.fragments, last.cart_hash, null]);
        }
        if (!failed) showCartNotice("");
      }
    } catch (error) {
      console.error(error);
    } finally {
      releaseCartLock();
      draining = false;
      if (desiredQty.size || pendingAdds.length) drainCartQueue();
    }
  };
  var kickCartQueue = () => {
    if (draining) return;
    queueMicrotask(() => {
      drainCartQueue();
    });
  };
  var queueQty = (key, qty) => {
    desiredQty.set(key, qty);
    kickCartQueue();
  };
  var addToCartAjax = ({ productId, quantity = 1 }) => {
    const request = { productId, quantity };
    if (!cartQueueBound) {
      return withRetry(() => postAddToCart(request)).then((data) => {
        if (data == null ? void 0 : data.fragments) {
          applyFragments(data.fragments);
          if (window.jQuery) {
            window.jQuery(document.body).trigger("added_to_cart", [
              data.fragments,
              data.cart_hash,
              null
            ]);
          }
        }
        return data;
      });
    }
    return new Promise((resolve, reject) => {
      pendingAdds.push({ productId, quantity, resolve, reject });
      kickCartQueue();
    });
  };
  var bindCartQueue = () => {
    if (cartQueueBound || typeof window === "undefined") return;
    cartQueueBound = true;
    window.addEventListener("storage", (event) => {
      if (event.key !== CART_HASH_KEY || !event.newValue || event.newValue === knownCartHash()) return;
      if (draining) return;
      syncFromOtherTab().catch((error) => {
        console.error(error);
      });
    });
  };
  var openCartDrawer = () => {
    const root = document.querySelector("[data-cart-drawer]");
    if (!root) return;
    root.classList.add("cart-drawer--open");
    root.setAttribute("aria-hidden", "false");
    document.body.classList.add("cart-drawer-open");
    document.querySelectorAll("[data-cart-drawer-open]").forEach((trigger) => {
      trigger.setAttribute("aria-expanded", "true");
    });
  };
  var cartDrawer = (scope = document) => {
    const roots = scope.querySelectorAll("[data-cart-drawer], .cart-drawer");
    if (!roots.length) return;
    roots.forEach((root) => {
      if (cartDrawerInstances.has(root)) return;
      const panel = root.querySelector("[data-cart-drawer-panel], .cart-drawer__panel");
      const controller = new AbortController();
      const { signal } = controller;
      let lastFocus = null;
      bindCartQueue();
      seedConfirmedCart(root);
      const setOpen = (open) => {
        root.classList.toggle("cart-drawer--open", open);
        root.setAttribute("aria-hidden", String(!open));
        document.body.classList.toggle("cart-drawer-open", open);
        document.querySelectorAll("[data-cart-drawer-open], .main-menu__cart").forEach((trigger) => {
          trigger.setAttribute("aria-expanded", String(open));
          if (!trigger.getAttribute("aria-controls") && root.id) {
            trigger.setAttribute("aria-controls", root.id);
          }
        });
        if (open) {
          lastFocus = document.activeElement;
          const focusTarget = root.querySelector("[data-cart-drawer-close].cart-drawer__close") || panel;
          window.requestAnimationFrame(() => {
            var _a;
            return (_a = focusTarget == null ? void 0 : focusTarget.focus) == null ? void 0 : _a.call(focusTarget);
          });
        } else if (lastFocus && typeof lastFocus.focus === "function") {
          lastFocus.focus();
          lastFocus = null;
        }
      };
      const onOpenClick = (event) => {
        event.preventDefault();
        setOpen(true);
      };
      const onCloseClick = (event) => {
        if (event.target.closest("[data-cart-drawer-close]")) {
          event.preventDefault();
          setOpen(false);
        }
      };
      const onKeyDown = (event) => {
        if (event.key === "Escape" && root.classList.contains("cart-drawer--open")) {
          setOpen(false);
        }
      };
      const onBodyClick = (event) => {
        const decrease = event.target.closest("[data-cart-item-decrease]");
        const increase = event.target.closest("[data-cart-item-increase]");
        const remove = event.target.closest("[data-cart-item-remove]");
        if (!decrease && !increase && !remove) return;
        event.preventDefault();
        const item = event.target.closest("[data-cart-item]");
        if (!item) return;
        const key = item.getAttribute("data-cart-item-key");
        if (!key) return;
        const input = item.querySelector("[data-cart-item-input]");
        let qty = Number((input == null ? void 0 : input.value) || 1);
        if (remove) {
          qty = 0;
        } else if (decrease) {
          qty = Math.max(0, qty - 1);
        } else if (increase) {
          qty = Math.min(99, qty + 1);
        }
        if (qty < 1) {
          item.remove();
          paintTotals(root);
          if (!root.querySelector("[data-cart-item]")) showEmptyState(root);
        } else if (input) {
          input.value = String(qty);
          paintTotals(root);
        }
        queueQty(key, qty);
      };
      document.querySelectorAll("[data-cart-drawer-open], .main-menu__cart").forEach((trigger) => {
        trigger.setAttribute("aria-haspopup", "dialog");
        trigger.setAttribute("aria-expanded", "false");
        if (root.id) trigger.setAttribute("aria-controls", root.id);
        trigger.addEventListener("click", onOpenClick, { signal });
      });
      root.addEventListener("click", onCloseClick, { signal });
      root.addEventListener("click", onBodyClick, { signal });
      document.addEventListener("keydown", onKeyDown, { signal });
      const onGridAddClick = async (event) => {
        const btn = event.target.closest("[data-wpb-add-to-cart]");
        if (!btn || btn.disabled) return;
        event.preventDefault();
        event.stopPropagation();
        const productId = Number(btn.getAttribute("data-product-id") || btn.getAttribute("data-product_id") || 0);
        const quantity = Math.max(1, Number(btn.getAttribute("data-quantity") || 1));
        if (!productId) return;
        btn.disabled = true;
        btn.classList.add("loading");
        try {
          const data = await addToCartAjax({ productId, quantity });
          if ((data == null ? void 0 : data.error) && (data == null ? void 0 : data.product_url)) {
            window.location = data.product_url;
            return;
          }
          openCartDrawer();
        } catch (error) {
          console.error(error);
        } finally {
          btn.disabled = false;
          btn.classList.remove("loading");
        }
      };
      document.addEventListener("click", onGridAddClick, { signal, capture: true });
      if (window.jQuery) {
        const $ = window.jQuery;
        const onAdded = () => {
          syncShowcaseWithCart();
          openCartDrawer();
        };
        const onRefreshed = () => {
          syncShowcaseWithCart();
          if (draining || desiredQty.size) {
            desiredQty.forEach((qty, key) => {
              const row = rowByKey(root, key);
              const input = row == null ? void 0 : row.querySelector("[data-cart-item-input]");
              if (input && qty > 0) input.value = String(qty);
            });
            paintTotals(root);
            return;
          }
          seedConfirmedCart(root);
        };
        $(document.body).on("added_to_cart", onAdded);
        $(document.body).on("removed_from_cart wc_fragments_refreshed", onRefreshed);
        signal.addEventListener(
          "abort",
          () => {
            $(document.body).off("added_to_cart", onAdded);
            $(document.body).off("removed_from_cart wc_fragments_refreshed", onRefreshed);
          },
          { once: true }
        );
      }
      syncShowcaseWithCart();
      cartDrawerInstances.set(root, () => {
        controller.abort();
        setOpen(false);
        cartDrawerInstances.delete(root);
      });
    });
  };
  var cartDrawer_default = cartDrawer;

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
      const addButton = root.querySelector('.product-showcase__add, [name="add-to-cart"]');
      const productId = Number(
        root.getAttribute("data-product-id") || (addButton == null ? void 0 : addButton.value) || 0
      );
      const tabsSwiperEl = root.querySelector("[data-product-tabs-swiper]");
      const tabsPrev = root.querySelector(".product-showcase__tabs-nav--prev");
      const tabsNext = root.querySelector(".product-showcase__tabs-nav--next");
      const relatedSwiperEl = root.querySelector("[data-product-related-swiper]");
      const relatedPrev = root.querySelector("[data-product-related-prev]");
      const relatedNext = root.querySelector("[data-product-related-next]");
      const relatedPagination = root.querySelector("[data-product-related-pagination]");
      const tabs = Array.from(root.querySelectorAll("[data-product-tab]"));
      const panels = Array.from(root.querySelectorAll("[data-product-panel]"));
      const desktopQuery = window.matchMedia("(min-width: 960px)");
      const controller = new AbortController();
      const { signal } = controller;
      let tabsSwiper = null;
      let relatedSwiper = null;
      const setActiveThumb = (thumb) => {
        var _a;
        if (!stage || !thumb) return;
        const nextSrc = thumb.dataset.src;
        if (!nextSrc) return;
        stage.src = nextSrc;
        stage.alt = ((_a = thumb.getAttribute("aria-label")) == null ? void 0 : _a.replace(/^Ver\s+/i, "")) || stage.alt;
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
      const destroyRelatedSwiper = () => {
        if (!relatedSwiper) return;
        relatedSwiper.destroy(true, true);
        relatedSwiper = null;
      };
      const initRelatedSwiper = () => {
        if (typeof window.Swiper === "undefined" || !relatedSwiperEl || relatedSwiper) return;
        try {
          relatedSwiper = new window.Swiper(relatedSwiperEl, {
            slidesPerView: "auto",
            loop: false,
            spaceBetween: 16,
            grabCursor: true,
            watchOverflow: true,
            pagination: {
              el: relatedPagination,
              clickable: true
            },
            navigation: {
              prevEl: relatedPrev,
              nextEl: relatedNext
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
          const relatedRoot = root.querySelector(".product-showcase__related");
          if (relatedRoot) {
            const activeFilter = relatedRoot.querySelector('[data-product-filter][aria-pressed="true"]');
            applyProductFilter(relatedRoot, activeFilter ? activeFilter.getAttribute("data-product-filter") : "tarro");
          }
        } catch (error) {
          relatedSwiper = null;
        }
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
      qtyDec == null ? void 0 : qtyDec.addEventListener(
        "click",
        () => {
          updateQty(Number((qtyInput == null ? void 0 : qtyInput.value) || 1) - 1);
        },
        { signal }
      );
      qtyInc == null ? void 0 : qtyInc.addEventListener(
        "click",
        () => {
          updateQty(Number((qtyInput == null ? void 0 : qtyInput.value) || 1) + 1);
        },
        { signal }
      );
      qtyInput == null ? void 0 : qtyInput.addEventListener(
        "change",
        () => {
          updateQty(Number(qtyInput.value) || 1);
        },
        { signal }
      );
      cartForm == null ? void 0 : cartForm.addEventListener(
        "submit",
        async (event) => {
          event.preventDefault();
          if (!productId || !addButton) return;
          const quantity = clamp(Number((qtyInput == null ? void 0 : qtyInput.value) || 1), 1, Number(qtyInput == null ? void 0 : qtyInput.max) || 99);
          addButton.disabled = true;
          addButton.classList.add("loading");
          try {
            const data = await addToCartAjax({ productId, quantity });
            if ((data == null ? void 0 : data.error) && (data == null ? void 0 : data.product_url)) {
              window.location = data.product_url;
              return;
            }
            openCartDrawer();
          } catch (error) {
            console.error(error);
          } finally {
            addButton.disabled = false;
            addButton.classList.remove("loading");
          }
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
      const filterScroller = root.querySelector(".product-showcase__related .product-slider__filters");
      const filterPrev = root.querySelector("[data-product-filter-prev]");
      const filterNext = root.querySelector("[data-product-filter-next]");
      const updateFilterArrows = () => {
        if (!filterScroller || !filterPrev || !filterNext) return;
        const maxScroll = filterScroller.scrollWidth - filterScroller.clientWidth;
        filterPrev.disabled = maxScroll <= 1 || filterScroller.scrollLeft <= 1;
        filterNext.disabled = maxScroll <= 1 || filterScroller.scrollLeft >= maxScroll - 1;
      };
      const scrollFilters = (direction) => {
        if (!filterScroller) return;
        const amount = Math.max(120, Math.round(filterScroller.clientWidth * 0.75));
        filterScroller.scrollBy({ left: direction * amount, behavior: "smooth" });
      };
      filterPrev?.addEventListener("click", () => scrollFilters(-1), { signal });
      filterNext?.addEventListener("click", () => scrollFilters(1), { signal });
      filterScroller?.addEventListener("scroll", updateFilterArrows, { signal, passive: true });
      if (typeof ResizeObserver !== "undefined" && filterScroller) {
        const filterObserver = new ResizeObserver(updateFilterArrows);
        filterObserver.observe(filterScroller);
        signal.addEventListener("abort", () => filterObserver.disconnect(), { once: true });
      }
      updateFilterArrows();
      updateTabsEnhancement();
      initRelatedSwiper();
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
          destroyRelatedSwiper();
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
        if (swiperEl.swiper) {
          markExistingSlider(root);
          swiper = swiperEl.swiper;
          return;
        }
        swiper = new Swiper(
          swiperEl,
          withSliderLoader(root, {
            slidesPerView: 1,
            spaceBetween: 16,
            grabCursor: true,
            watchOverflow: true,
            pagination: {
              el: paginationEl,
              clickable: true
            }
          })
        );
      };
      const updateEnhancement = () => {
        if (gridQuery.matches) {
          destroySwiper();
          revealSlider(root);
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
          markExistingSlider(root);
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
        const instance = new Swiper(
          swiperEl,
          withSliderLoader(root, {
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
          })
        );
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
          markExistingSlider(root);
          instances.push(swiperEl.swiper);
          return;
        }
        const prevEl = root.querySelector(".main-banner__nav--prev");
        const nextEl = root.querySelector(".main-banner__nav--next");
        const paginationEl = root.querySelector(".main-banner__pagination");
        const prefersReducedMotion = window.matchMedia(
          "(prefers-reduced-motion: reduce)"
        ).matches;
        const instance = new Swiper(
          swiperEl,
          withSliderLoader(root, {
            effect: "fade",
            fadeEffect: {
              crossFade: true
            },
            speed: prefersReducedMotion ? 0 : 800,
            loop: true,
            autoplay: prefersReducedMotion ? false : {
              delay: 6e3,
              disableOnInteraction: false
            },
            pagination: {
              el: paginationEl,
              clickable: true
            },
            navigation: {
              prevEl,
              nextEl
            }
          })
        );
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

  // js/components/communitySubscribeForm.js
  var getConfig = () => window.wpbCommunitySubscribe || window.wpbCommunitySplit || {};
  var setStatus = (statusEl, block, message, type) => {
    if (!statusEl) return;
    statusEl.textContent = message || "";
    statusEl.hidden = !message;
    statusEl.classList.remove(
      `${block}__status--success`,
      `${block}__status--error`,
      `${block}__status--pending`
    );
    if (type) {
      statusEl.classList.add(`${block}__status--${type}`);
    }
  };
  var bindCommunitySubscribeForm = ({
    root,
    block,
    source,
    statusSelector,
    instances,
    signal
  }) => {
    if (instances.has(root)) return false;
    const form = root.querySelector(`.${block}__form`);
    const input = form == null ? void 0 : form.querySelector(`.${block}__input`);
    const submit = form == null ? void 0 : form.querySelector(`.${block}__submit`);
    const statusEl = form == null ? void 0 : form.querySelector(statusSelector || `[data-${block}-status]`);
    const config = getConfig();
    if (!form || !input || !submit) return false;
    const defaultLabel = submit.textContent;
    form.addEventListener(
      "submit",
      async (event) => {
        var _a, _b;
        event.preventDefault();
        const email = input.value.trim();
        const i18n = config.i18n || {};
        if (!email || !input.checkValidity()) {
          setStatus(statusEl, block, i18n.invalid || "Ingresa un correo v\xE1lido.", "error");
          input.focus();
          return;
        }
        if (!config.ajaxUrl || !config.nonce) {
          setStatus(
            statusEl,
            block,
            i18n.error || "No se pudo completar el registro. Intenta de nuevo.",
            "error"
          );
          return;
        }
        submit.disabled = true;
        setStatus(statusEl, block, i18n.sending || "Enviando\u2026", "pending");
        const body = new FormData();
        body.append("action", config.action || "wpb_community_subscribe");
        body.append("nonce", config.nonce);
        body.append("email", email);
        body.append("source", source);
        const honeypot = form.querySelector('[name="website"]');
        if (honeypot) {
          body.append("website", honeypot.value);
        }
        try {
          const response = await fetch(config.ajaxUrl, {
            method: "POST",
            credentials: "same-origin",
            body
          });
          const payload = await response.json();
          const message = ((_a = payload == null ? void 0 : payload.data) == null ? void 0 : _a.message) || ((payload == null ? void 0 : payload.success) ? i18n.success : ((_b = payload == null ? void 0 : payload.data) == null ? void 0 : _b.code) === "duplicate_email" ? i18n.duplicate : i18n.error);
          if (payload == null ? void 0 : payload.success) {
            setStatus(statusEl, block, message || i18n.success, "success");
            form.reset();
          } else {
            setStatus(statusEl, block, message || i18n.error, "error");
          }
        } catch (error) {
          setStatus(
            statusEl,
            block,
            i18n.error || "No se pudo completar el registro. Intenta de nuevo.",
            "error"
          );
        } finally {
          submit.disabled = false;
          submit.textContent = defaultLabel;
        }
      },
      { signal }
    );
    return true;
  };

  // js/components/communitySplit.js
  var communitySplitInstances = /* @__PURE__ */ new WeakMap();
  var communitySplit = (scope = document) => {
    const initialized = [];
    const roots = scope.querySelectorAll(".community-split");
    if (roots.length === 0) {
      return initialized;
    }
    roots.forEach((root) => {
      if (communitySplitInstances.has(root)) return;
      const controller = new AbortController();
      const { signal } = controller;
      const bound = bindCommunitySubscribeForm({
        root,
        block: "community-split",
        source: "community-split",
        statusSelector: "[data-community-split-status]",
        instances: communitySplitInstances,
        signal
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
  var communitySplit_default = communitySplit;

  // js/components/communityBanner.js
  var communityBannerInstances = /* @__PURE__ */ new WeakMap();
  var communityBanner = (scope = document) => {
    const initialized = [];
    const roots = scope.querySelectorAll(".community-banner");
    if (roots.length === 0) {
      return initialized;
    }
    roots.forEach((root) => {
      if (communityBannerInstances.has(root)) return;
      const controller = new AbortController();
      const { signal } = controller;
      const bound = bindCommunitySubscribeForm({
        root,
        block: "community-banner",
        source: "community-banner",
        statusSelector: "[data-community-banner-status]",
        instances: communityBannerInstances,
        signal
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
  var communityBanner_default = communityBanner;

  // js/components/newsletterBanner.js
  var newsletterBannerInstances = /* @__PURE__ */ new WeakMap();
  var newsletterBanner = (scope = document) => {
    const initialized = [];
    const roots = scope.querySelectorAll(".newsletter-banner");
    if (roots.length === 0) {
      return initialized;
    }
    roots.forEach((root) => {
      if (newsletterBannerInstances.has(root)) return;
      const controller = new AbortController();
      const { signal } = controller;
      const bound = bindCommunitySubscribeForm({
        root,
        block: "newsletter-banner",
        source: "newsletter-banner",
        statusSelector: "[data-newsletter-banner-status]",
        instances: newsletterBannerInstances,
        signal
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
  var newsletterBanner_default = newsletterBanner;

  // js/components/contactForm.js
  var contactFormInstances = /* @__PURE__ */ new WeakMap();
  var getConfig2 = () => window.wpbContactForm || {};
  var setStatus2 = (statusEl, message, type) => {
    if (!statusEl) return;
    statusEl.textContent = message || "";
    statusEl.hidden = !message;
    statusEl.classList.remove(
      "contact-form__status--success",
      "contact-form__status--error",
      "contact-form__status--pending"
    );
    if (type) {
      statusEl.classList.add(`contact-form__status--${type}`);
    }
  };
  var contactForm = (scope = document) => {
    const initialized = [];
    const roots = scope.querySelectorAll(".contact-form");
    if (roots.length === 0) {
      return initialized;
    }
    const config = getConfig2();
    roots.forEach((root) => {
      if (contactFormInstances.has(root)) return;
      const form = root.querySelector(".contact-form__form");
      const submit = form == null ? void 0 : form.querySelector(".contact-form__submit");
      const statusEl = form == null ? void 0 : form.querySelector("[data-contact-form-status]");
      const nameInput = form == null ? void 0 : form.querySelector('[name="name"]');
      const emailInput = form == null ? void 0 : form.querySelector('[name="email"]');
      const phoneInput = form == null ? void 0 : form.querySelector('[name="phone"]');
      const messageInput = form == null ? void 0 : form.querySelector('[name="message"]');
      const controller = new AbortController();
      const { signal } = controller;
      if (!form || !submit || !nameInput || !emailInput || !messageInput) return;
      const defaultLabel = submit.textContent;
      form.addEventListener(
        "submit",
        async (event) => {
          var _a;
          event.preventDefault();
          const i18n = config.i18n || {};
          const name = nameInput.value.trim();
          const email = emailInput.value.trim();
          const phone = (phoneInput == null ? void 0 : phoneInput.value.trim()) || "";
          const message = messageInput.value.trim();
          if (!name || !email || !message || !emailInput.checkValidity()) {
            setStatus2(statusEl, i18n.invalid || "Revisa los campos e intenta de nuevo.", "error");
            return;
          }
          if (!config.ajaxUrl || !config.nonce) {
            setStatus2(statusEl, i18n.error || "No se pudo enviar el mensaje. Intenta de nuevo.", "error");
            return;
          }
          submit.disabled = true;
          setStatus2(statusEl, i18n.sending || "Enviando\u2026", "pending");
          const body = new FormData();
          body.append("action", config.action || "wpb_contact_submit");
          body.append("nonce", config.nonce);
          body.append("name", name);
          body.append("email", email);
          body.append("phone", phone);
          body.append("message", message);
          const honeypot = form.querySelector('[name="website"]');
          if (honeypot) {
            body.append("website", honeypot.value);
          }
          try {
            const response = await fetch(config.ajaxUrl, {
              method: "POST",
              credentials: "same-origin",
              body
            });
            const payload = await response.json();
            const text = ((_a = payload == null ? void 0 : payload.data) == null ? void 0 : _a.message) || ((payload == null ? void 0 : payload.success) ? i18n.success : i18n.error);
            if (payload == null ? void 0 : payload.success) {
              setStatus2(statusEl, text || i18n.success, "success");
              form.reset();
            } else {
              setStatus2(statusEl, text || i18n.error, "error");
            }
          } catch (error) {
            setStatus2(statusEl, i18n.error || "No se pudo enviar el mensaje. Intenta de nuevo.", "error");
          } finally {
            submit.disabled = false;
            submit.textContent = defaultLabel;
          }
        },
        { signal }
      );
      const destroy = () => {
        controller.abort();
        contactFormInstances.delete(root);
      };
      contactFormInstances.set(root, destroy);
      initialized.push(destroy);
    });
    return initialized;
  };
  var contactForm_default = contactForm;

  // js/theme-scripts.js
  var initEagerComponents = () => {
    mainMenu_default();
    mainSlider_default();
    mainBanner_default();
    productSlider_default();
    cartDrawer_default();
    productShowcase_default();
    blogFilter_default();
    foundersCards_default();
    communitySplit_default();
    communityBanner_default();
    newsletterBanner_default();
    contactForm_default();
    if (typeof window.Prism !== "undefined" && document.querySelector('code[class*="language-"], [class*="language-"] code')) {
      window.Prism.highlightAll();
    }
  };
  var initComponents = () => {
    document.documentElement.classList.remove("no-js");
    document.documentElement.classList.add("js");
    document.documentElement.dataset.componentsReady = "true";
    initEagerComponents();
    componentLoader_default([
      { selector: ".moment-slider", init: momentSlider_default },
      { selector: ".product-slider", init: productSlider_default },
      { selector: ".mushrooms-split", init: mushroomsSplit_default },
      { selector: ".allies-slider", init: alliesSlider_default },
      { selector: ".store-locator", init: storeLocator_default },
      { selector: ".ways-slider", init: waysSlider_default },
      { selector: ".believe-cards", init: believeCards_default },
      { selector: ".reviews-slider", init: reviewsSlider_default },
      { selector: ".guide-slider", init: guideSlider_default },
      { selector: ".ingredients-slider", init: ingredientsSlider_default },
      { selector: ".blog-slider", init: blogSlider_default }
    ]);
  };
  if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", initComponents, { once: true });
  } else {
    initComponents();
  }
})();
