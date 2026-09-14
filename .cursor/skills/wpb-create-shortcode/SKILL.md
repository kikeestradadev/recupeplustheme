---
name: wpb-create-shortcode
description: >-
  Crea shortcodes del theme wpboilerplate con convención sc_nombre, callback
  camelCase, atributos camelCase, partial SCSS y módulo JS en components.
  Usar cuando el usuario pida crear, renombrar o corregir un shortcode.
---

# Crear shortcode (wpboilerplate)

## Ubicación

| Capa | Ruta |
|---|---|
| PHP | `core-functions/shortcodes/sc_<nombre>.php` |
| SCSS | `scss/components/_<kebab>.scss` + import en `_components.scss` |
| JS | `js/components/<camelCase>.js` + import/call en `js/theme-scripts.js` |
| Slider Swiper | Skill `wpb-swiper-slider` (CDN global + validar DOM) |

No tocar `functions.php`.

## Naming PHP

| Pieza | Formato | Ejemplo |
|---|---|---|
| Archivo / tag | `sc_<nombre>` | `sc_main_slider_test` |
| Función | camelCase | `scMainSliderTest` |
| Atributos | camelCase | `titleText` |

## Naming SCSS

| Shortcode | Partial | Import |
|---|---|---|
| `sc_main_slider_test` | `_main-slider-test.scss` | `@import "main-slider-test";` |

Quitar `sc_` → `_` a `-`.

## Naming JS

| Shortcode | Archivo | Export |
|---|---|---|
| `sc_main_slider_test` | `mainSliderTest.js` | `mainSliderTest` |

Quitar `sc_` → camelCase. Estilo `module1.js`.

Si el shortcode es un **slider Swiper**, seguir la skill `wpb-swiper-slider`:
validar roots / `Swiper` / no re-init; no importar el paquete (usar CDN).

## Pasos (en orden)

1. PHP: `core-functions/shortcodes/sc_<nombre>.php` + `add_shortcode`.
2. SCSS: `scss/components/_<kebab>.scss` + `@import` en `_components.scss`.
3. JS: `js/components/<camelCase>.js` con `export const <camelCase>`.
4. JS entry: import + llamada en `js/theme-scripts.js` (como module1/module2).
5. Prepros recompila CSS y JS.

## Plantilla JS (como module1)

```js
export const mainSliderTest = () => {
  // lógica
};
```

En `js/theme-scripts.js`:

```js
import { mainSliderTest } from './components/mainSliderTest';
// dentro del IIFE:
mainSliderTest();
```

## Verificar

- [ ] PHP `sc_*` + función camelCase
- [ ] Partial SCSS + import
- [ ] Módulo JS camelCase + import/call en theme-scripts.js
