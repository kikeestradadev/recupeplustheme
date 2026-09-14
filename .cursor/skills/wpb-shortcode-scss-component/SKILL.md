---
name: wpb-shortcode-scss-component
description: >-
  Crea el partial SCSS de un shortcode del theme wpboilerplate en scss/components
  con naming _nombre-kebab.scss e import en _components.scss. Usar al crear o
  actualizar shortcodes, o cuando falte el CSS/SASS de un componente shortcode.
---

# SCSS de componente shortcode

## Cuándo

Al crear un shortcode nuevo, o si un shortcode existe sin su partial SCSS.

## Conversión de nombre

| Shortcode PHP | Archivo SCSS | Import en `_components.scss` |
|---|---|---|
| `sc_name` | `_name.scss` | `@import "name";` |
| `sc_top_banner` | `_top-banner.scss` | `@import "top-banner";` |
| `sc_main_slider_test` | `_main-slider-test.scss` | `@import "main-slider-test";` |

1. Quitar `sc_`.
2. `_` → `-` (kebab-case).
3. Archivo: `scss/components/_<kebab>.scss`.

## Pasos

1. Crear el partial en `scss/components/_<kebab>.scss`.
2. Escribir estilos del componente ahí (clase raíz recomendada: `.<kebab>`).
3. Abrir `scss/components/_components.scss` y agregar:

```scss
@import "<kebab>";
```

4. No editar `theme-styles.scss` por componente (ya importa `components/components`).
5. Dejar que Prepros recompile a `theme-styles.css`.

## Plantilla mínima del partial

```scss
// Componente: sc_main_slider_test
.main-slider-test {
	// estilos del shortcode
}
```

## Checklist

- [ ] Partial `scss/components/_<kebab>.scss` existe
- [ ] Import en `scss/components/_components.scss`
- [ ] Naming kebab-case (no snake_case en el partial)
- [ ] Estilos no sueltos en layout ni en la raíz de theme-styles
