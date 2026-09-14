---
name: wpb-shortcode-js-component
description: >-
  Crea el módulo JS de un shortcode del theme wpboilerplate en js/components con
  naming camelCase (mainSliderTest.js) e import/llamada en theme-scripts.js.
  Usar al crear shortcodes con JS o al faltarle el módulo al componente.
---

# JS de componente shortcode

## Cuándo

Al crear un shortcode que use JS, o si el shortcode ya existe y no tiene módulo.

## Conversión de nombre

| Shortcode | Archivo | Export |
|---|---|---|
| `sc_main_slider_test` | `mainSliderTest.js` | `mainSliderTest` |
| `sc_top_banner` | `topBanner.js` | `topBanner` |

Quitar `sc_` → snake_case a camelCase.

## Pasos

1. Crear `js/components/<camelCase>.js` siguiendo el estilo de `module1.js`:

```js
export const mainSliderTest = () => {
  console.log('mainSliderTest');
};
```

2. En `js/theme-scripts.js`, importar y ejecutar (como `module1` / `module2`):

```js
import { mainSliderTest } from './components/mainSliderTest';

(() => {
  $ = jQuery.noConflict();
  mainSliderTest();
})();
```

3. Prepros recompila hacia `theme-scripts.js` en la raíz del theme.

## Checklist

- [ ] Archivo en `js/components/<camelCase>.js`
- [ ] `export const <camelCase> = () => { ... }`
- [ ] `import { <camelCase> } from './components/<camelCase>'` en `theme-scripts.js`
- [ ] Llamada `<camelCase>()` dentro del IIFE
