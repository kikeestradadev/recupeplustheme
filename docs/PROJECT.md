# Recupé Plus Theme — Project Documentation

> **Read this file before changing anything.**  
> Active theme path: `/Applications/MAMP/htdocs/recupeplustheme/wp-content/themes/recupeplustheme`  
> WordPress install: `/Applications/MAMP/htdocs/recupeplustheme`

---

## 1. Overview

Custom WordPress theme (**wpboilerplate** / Recupé Plus) that ports the static marketing landing page into editable page modules (ACF Options), guides (posts), legal pages, and a single-post layout.

| Name | Path / URL | Role |
|---|---|---|
| **This theme** | `…/wp-content/themes/recupeplustheme` | Active WP theme (this repo) |
| **This WP site** | `…/htdocs/recupeplustheme` | Local MAMP install |
| **Landing page (LP)** | `/Users/enriqueestradanunez/Documents/repos/empresas/recupeplus/recupepluslp` | Static Pug/SCSS source; design reference + assets on GH Pages |
| **El sitio original** | `/Applications/MAMP/htdocs/recupeplus` | Older Woodmart/Elementor shop — **not** this theme |

Git remotes:

- Theme: `https://github.com/kikeestradadev/recupeplustheme.git` (`origin`, branch `main`)
- LP: `https://github.com/kikeestradadev/recupepluslp.git` (`origin`, branch `main`)
- LP live preview: `https://kikeestradadev.github.io/recupepluslp/` (also in LP `package.json` → `homepage`)

---

## 2. Directory structure (theme)

```
recupeplustheme/
├── functions.php              # Recursively includes all PHP under core-functions/
├── header.php / footer.php
├── front-page.php / page.php / single.php
├── page-*.php                 # Named page templates (Guías, Home, Suplementos, legales…)
├── core-functions/
│   ├── admin/                 # ACF menus + field groups + custom admin screens
│   ├── ajax/                  # Contact / community subscribe endpoints
│   ├── core/                  # Setup, enqueues (CSS/JS, Swiper, lazysizes), LP assets helper, forms
│   ├── widgets/               # Sidebars (e.g. top-widget)
│   └── shortcodes/            # sc_*.php shortcodes
├── template-parts/
│   ├── content/               # content-single.php, content-page.php
│   ├── static-modules/        # One folder per site section (home, guias, suplementos…)
│   └── menu.php
├── scss/
│   ├── theme-styles.scss      # Prepros entry → theme-styles.css
│   ├── core/                  # layout, fonts, breakpoints, hacks
│   └── modules/               # All UI/module/shortcode SCSS (imported via modules.scss)
├── js/
│   ├── theme-scripts.js       # Prepros entry → root theme-scripts.js
│   ├── components/            # ES modules per UI block
│   └── admin/                 # Admin accordion / CRUD screens
├── css/admin/
├── docs/PROJECT.md            # This file
├── AGENTS.md                  # AI/developer entry instructions
├── .cursor/rules/             # Cursor always-applied rules
└── prepros.config
```

**Convention:** New PHP goes in `core-functions/{admin|core|shortcodes|widgets|ajax}/` — not theme root, not `core-functions/*.php` (see `.cursor/rules/01-core-functions-structure.mdc`).

---

## 3. Key templates

| Template | Purpose |
|---|---|
| `front-page.php` / `page-home.php` | Home modules via `home/home-modules.php` |
| `page-guias.php` | Guías: banner, **blog-filter** (dynamic posts), community, help, guide slider |
| `page-suplementos.php` | Supplements modules (product slider currently **static**) |
| `page-donde-comprar.php` | Buy page modules |
| `page-nosotros.php` | About modules |
| `page-contacto.php` | Contact form |
| `page-preguntas-frecuentes.php` | FAQ page (ACF `pf_*` on the page) |
| `page-politica-de-privacidad.php` / `page-terminos-y-condiciones.php` / `page-politica-de-devoluciones.php` | Legal stubs/modules |
| `single.php` | Single post → `template-parts/content/content-single.php` |
| `page.php` | Generic pages → `content-page.php` |

### Single post layout

- File: `template-parts/content/content-single.php`
- Featured image: LP-style **`post-banner`** (`section.post-banner` > `figure` > `img.post-banner__image`), built with `get_the_post_thumbnail_url()` (avoids WP inline image styles).
- Body: `main-container` > `container` inside `article.post-content`.
- Styles: `scss/modules/_post-banner.scss`, `_post-content.scss`.

### Guides grid (dynamic)

- File: `template-parts/static-modules/guias/blog-filter.php`
- `WP_Query`: all published `post`, `posts_per_page => -1`, date DESC.
- Cards use existing `blog-filter__*` markup: title, permalink, featured image, category label(s).
- `data-category` is space-separated chip slugs (`bienestar-diario` → `bienestar` for the chip).
- Client filter/pagination: `js/components/blogFilter.js` (supports multiple categories per card).

### Home blog slider (Block Slider)

- File: `template-parts/static-modules/home/blog-slider.php`
- Slides: `WP_Query` of the **5 most recent** published `post`s (same source as Guías grid; no separate guías CPT).
- Section title still from ACF `home_blog_title` (fallback: “Guías para sentirte mejor”).
- “Ver más” below the slider → permalink of the `guias` page (`/guias/`), same tab (no `target="_blank"`).
- Styles: `scss/modules/_blog-slider.scss` (`blog-slider__more` / `blog-slider__more-link`). JS unchanged (`blogSlider.js`).

---

## 4. ACF field groups (high level)

Admin parent menu: **Mantenimientos** (`wpb-mantenimientos`) — flat submenu + accordion JS (`js/admin/mantenimientosMenu.js`).

| Options slug / location | Notable fields | Used by |
|---|---|---|
| `wpb-home-faqs` | `home_faqs_title`, `home_faqs_intro`, repeater **`home_faqs`** (`question` / `answer`) | `home/faqs.php` |
| Page template Preguntas frecuentes | `pf_title`, `pf_intro`, repeater **`pf_faqs`** | Page ID **136**, `preguntas-frecuentes` module |
| `wpb-footer-content` | Footer columns, copyright, etc. | `shared/alt-footer.php` |
| `wpb-contacto-global` | Global WhatsApp URL | WhatsApp button, banners, contact CTAs via `wpb_acf_global_whatsapp_*` |
| Home / Nosotros / Guías / Suplementos / Dónde comprar | Per-section `wpb-*` option pages | Matching `static-modules/*` |

**Registros (custom tables, under Mantenimientos accordion → Registros):**

| Admin page | Slug / URL | Storage |
|---|---|---|
| Comunidad (email subscribers) | `admin.php?page=wpb-community-subscribers` | Table `{prefix}wpb_community_subscribers` via `wpb_community_subscriber_insert()` |
| Contactos (contact form) | `admin.php?page=wpb-contact-messages` | Contact messages table |

Subscribe AJAX: `admin-ajax.php` → `wpb_community_subscribe` (`core-functions/ajax/wpb_community-subscribe.php`). Sources: `community-split`, `community-banner`, `newsletter-banner`.

Helpers (prefer these over raw `get_field`): `wpb_acf_option_text`, `wpb_acf_option_image`, `wpb_acf_option_rows`, `wpb_acf_option_link` in `core-functions/admin/wpb_acf-helpers.php`.

---

## 5. Content (local DB — reference)

### Categories (do not delete casually)

| Name | Term ID | Slug | Filter chip |
|---|---|---|---|
| rutina | 23 | `rutina` | `rutina` |
| descanso | 24 | `descanso` | `descanso` |
| ingredientes | 25 | `ingredientes` | `ingredientes` |
| hongos | 26 | `hongos` | `hongos` |
| bienestar diario | 27 | `bienestar-diario` | `bienestar` |

### Published guide posts

Featured image for all: **`1post.png`**, attachment ID **148**.  
Titles cleaned from raw `.docx` filenames (IDs, categories, featured image, and body content unchanged).

| ID | Title | Categories |
|---|---|---|
| 140 | Hongos Cordyceps | descanso (24), ingredientes (25) |
| 141 | ¿Qué es la Salud Holística? | rutina (23) |
| 142 | Ashwagandha para el Estrés | ingredientes (25) |
| 143 | Dormir Es el Truco de Bienestar Más Subestimado | bienestar diario (27) |
| 144 | Hongos Cola de Pavo | ingredientes (25) |
| 145 | Hongos Melena de León | rutina (23) |
| 146 | La Conexión Mente-Cuerpo | rutina (23) |
| 147 | Reishi | rutina (23) |

Source docs (import origin): LP  
`public/assets/drive_22_set/Guias/Optimized Content/Recupe Plus/2. Spanish Content/*.docx`

Home FAQs (`home_faqs` options) and page 136 `pf_faqs`: **10** Q&As each, copied from LP `#faqs` / `faqs-data.pug`.

---

## 6. Landing page (context repo)

- Stack: Pug → HTML, SCSS → `public/styles.css`, assets under `public/assets/`.
- Post reference: `public/post.html` + `src/pug/components/post-banner.pug` / `post-content.pug`.
- FAQ source: `src/pug/data/faqs-data.pug`.
- Homepage URL in `package.json`: GH Pages link above.
- Theme static modules resolve LP CDN images via `wpb_lp_asset()` (see §8), not hardcoded full URLs.

---

## 7. Build process

| Asset | Entry | Output | Tool |
|---|---|---|---|
| CSS | `scss/theme-styles.scss` | `theme-styles.css` | Prepros (or `npx sass`) |
| JS | `js/theme-scripts.js` | `theme-scripts.js` | Prepros (or `npx esbuild … --bundle`) |

PHP templates do **not** need a build step. After SCSS/JS edits, recompile before testing in the browser.

Swiper: only enqueue when `wpb_front_needs_swiper` is true — see `.cursor/rules/06-swiper-performance.mdc`.

---

## 8. LP asset URLs

Static modules that still pull images from the landing-page CDN use:

- Constant: `WPB_LP_ASSETS_URL` (default `https://kikeestradadev.github.io/recupepluslp/assets`)
- Helper: `wpb_lp_asset( 'path/under/assets' )` in `core-functions/core/wpb_lp-assets.php`
- Filter: `wpb_lp_assets_url` to override the base

Prefer Media Library / ACF images when a module is fully dynamic.

## 9. Cleanup changelog (theme hygiene)

Already applied (do not re-do casually):

1. **Legacy PHP:** moved `wpb_setup`, `wpb_set-css-js`, parent-theme enqueue helper into `core-functions/core/`; widget into `core-functions/widgets/wpb_top-widget.php`; removed empty `test.php`.
2. **Security:** deleted root `wp-cache.php` (Tiny File Manager v2.6 leftover).
3. **SCSS convention:** docs/rules/skills use `scss/modules/` + `modules.scss` (not `scss/components/`).
4. **JS:** wired `initWhenVisible` into `componentLoader`; removed unused stubs `module1.js`, `module2.js`, `internalModule.js`, `image.png`; kept helpers used via imports (`sliderLoader`, `swiperRoots`, `communitySubscribeForm`).
5. **Assets:** `WPB_LP_ASSETS_URL` + `wpb_lp_asset()` replace hardcoded GH Pages URLs in static modules / menu.
6. **Posts 140–147:** titles cleaned to readable Spanish (see §5); IDs, categories, featured image 148, and content kept.

## 10. Known TODOs / debt

1. **Supplements / Gomitas–Cápsulas product slider** (`suplementos/product-slider.php`) is **static HTML** (LP assets via `wpb_lp_asset()` + `product.html` links). Make dynamic after **WooCommerce** is installed and products exist.
2. Migrate remaining LP CDN images into the Media Library / ACF over time.
3. Stub shortcodes `sc_name` / `sc_name2` are examples only.
4. Legal page modules may still be stub/static copy — confirm with content owners before launch.

---

## 11. Development practices (expected)

- Escape output: `esc_html`, `esc_url`, `esc_attr`; use `wp_kses_post` for rich text.
- Prefer `wpb_acf_option_*` helpers for Options fields.
- One visual module ≈ one PHP partial under `static-modules/{section}/` + SCSS in `scss/modules/_*.scss` + JS in `js/components/*.js` when interactive.
- Do not invent product data; wait for WooCommerce for commerce blocks.
- Do not call the old Woodmart install “Digital” — call it **el sitio original** (rule `07-sitio-original-naming.mdc`).

---

## 12. Quick local ops

```bash
# Theme CSS
npx sass scss/theme-styles.scss theme-styles.css --style=compressed --no-source-map

# Theme JS
npx esbuild js/theme-scripts.js --bundle --outfile=theme-scripts.js --format=iife --platform=browser

# WP via MAMP PHP (example)
/Applications/MAMP/bin/php/php8.4.1/bin/php -r '/* bootstrap wp-load.php */'
# MySQL socket: /Applications/MAMP/tmp/mysql/mysql.sock
```

Page IDs of note: FAQ page **136**; posts **140–147**; featured attachment **148**.
