---
name: wpb-swiper-slider
description: >-
  Inicializa sliders Swiper del theme de forma liviana: CDN solo si la página
  lo necesita, hero inmediato, resto con IntersectionObserver, sin re-init.
  Usar al crear o editar JS de sliders, carousels, o módulos tipo mainBanner /
  momentSlider / alliesSlider.
---

# Swiper slider (liviano)

## Contexto del theme

- Swiper **no** va hardcodeado en `header.php` / `footer.php`.
- Se encola en `core-functions/core/wpb_swiper-assets.php` solo si `wpb_front_needs_swiper()` es true (Home / front por defecto).
- Global: `Swiper`. No importar el paquete npm.
- El módulo vive en `js/components/<camelCase>.js` y se llama desde `js/theme-scripts.js`.

## Página nueva con slider

Si no es Home, hay que pedir el CDN:

```php
add_filter( 'wpb_front_needs_swiper', '__return_true' );
```

O ampliar `wpb_front_needs_swiper()`. No pegar otro `<script>` / `<link>` de Swiper.

## Init

| Slider | Cuándo inicializar |
|---|---|
| Hero / above the fold (`main-banner`) | Inmediato |
| Todo lo demás | `initWhenVisible(el, () => slider(el))` en `theme-scripts.js` |

`initWhenVisible` está en `js/components/initWhenVisible.js` (margen 200px).
Si el `scope` es el root del módulo, usar `swiperRoots(scope, '.bloque')` — `querySelectorAll` no se encuentra a sí mismo.

## Plantilla del módulo

```js
import { swiperRoots } from './swiperRoots';

const momentSlider = (scope = document) => {
	const instances = [];
	const roots = swiperRoots(scope, '.moment-slider');

	if (roots.length > 0 && typeof Swiper !== 'undefined') {
		roots.forEach((root) => {
			const swiperEl = root.querySelector('.moment-slider__swiper');
			if (!swiperEl) return;

			if (swiperEl.swiper) {
				instances.push(swiperEl.swiper);
				return;
			}

			const instance = new Swiper(swiperEl, {
				slidesPerView: 'auto',
				loop: false,
				spaceBetween: 16,
				grabCursor: true,
			});

			instances.push(instance);
		});
	}

	return instances;
};

export default momentSlider;
```

En `theme-scripts.js` (no-hero):

```js
lazyInit('.moment-slider', momentSlider);
```

Hero:

```js
mainBanner();
```

## Config que no estorba

- `loop: false` salvo diseño (loop clona slides).
- Autoplay solo en el hero; si existe, apagarlo con `prefers-reduced-motion`.
- No `observer` / `observeParents` si no hace falta.
- No meter la config en `theme-scripts.js`.

## Imágenes (el usuario las mantiene livianas)

- WebP.
- Primer slide del hero: `loading="eager"`.
- Resto: `loading="lazy"`.

## Checklist

- [ ] No CDN suelto en header/footer
- [ ] Página no-Home: filtro `wpb_front_needs_swiper`
- [ ] `typeof Swiper !== 'undefined'` + roots + no re-init
- [ ] `swiperRoots` si el init recibe el root
- [ ] Hero inmediato; resto `lazyInit` / `initWhenVisible`
- [ ] `loop: false` por defecto

## Prohibido

- Cargar Swiper en todas las páginas “por si acaso”
- Inicializar todos los sliders al cargar Home
- `import Swiper from '...'`
- Duplicar instancias en el mismo `.swiper`
