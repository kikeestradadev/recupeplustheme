---
name: wpb-create-php-function
description: >-
  Crea y ubica funciones PHP del theme wpboilerplate en la subcarpeta correcta
  de core-functions (admin, widgets, shortcodes, core). Usar al crear shortcodes,
  widgets, menús de admin, setup del theme, enqueues de CSS/JS, o cualquier
  lógica PHP nueva del theme; también al reorganizar o mover archivos PHP del theme.
---

# Crear función PHP en la carpeta correcta

## Cuándo usar esta skill

Antes de escribir o mover cualquier lógica PHP del theme wpboilerplate.

## Pasos (en orden)

### 1. Clasificar el trabajo

| Señal en el pedido | Tipo | Carpeta |
|---|---|---|
| shortcode, `add_shortcode`, `[algo]` | shortcode | `core-functions/shortcodes/` |
| admin menu, options page, `add_menu_page`, settings API | admin | `core-functions/admin/` |
| widget, sidebar, `register_sidebar` | widget | `core-functions/widgets/` |
| setup, theme support, nav menus del front, enqueue CSS/JS | core | `core-functions/core/` |
| otro (ajax, CPT, API…) | nuevo tipo | `core-functions/<tipo>/` |

Setup **no** va en una carpeta `setup/`; va en `core/`.

### 2. Nombrar el archivo

- **Shortcodes:** seguir la skill/regla de shortcodes — archivo y tag `sc_<nombre>` (snake_case), callback camelCase (`scName`, `scTopBanner`), atributos camelCase. Ver skill `wpb-create-shortcode` y rule `03-shortcode-naming`.
- Resto: `wpb_<nombre>.php` (ej. `wpb_theme-menu.php`)
- Funciones PHP no-shortcode: prefijo `wpb_`

### 3. Crear el archivo en esa ruta

Plantilla mínima:

```php
<?php
// Evita acceso directo al archivo
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Tu función aquí + add_action / add_shortcode / etc.
```

### 4. No tocar `functions.php`

El theme ya recorre `core-functions/` y carga todos los `.php`. No añadas `include`/`require`.

### 5. Verificar

- [ ] Archivo dentro de la subcarpeta correcta (no en la raíz de `core-functions/`)
- [ ] Nombre con prefijo `wpb_` o `sc_`
- [ ] Sin includes nuevos en `functions.php`
- [ ] Misma convención simple y legible que el resto del theme

## Ejemplos rápidos

| Pedido del usuario | Ruta del archivo |
|---|---|
| "Crea un shortcode de hero" | `core-functions/shortcodes/sc_hero.php` |
| "Agrega un submenú al Theme Menu" | `core-functions/admin/wpb_theme-menu.php` (o archivo admin relacionado) |
| "Registra otra sidebar" | `core-functions/widgets/wpb_<nombre>.php` |
| "Encola un script nuevo" | `core-functions/core/wpb_set-css-js.php` (o `wpb_<nombre>-assets.php` en `core/`) |
| "Activa title-tag / thumbnails" | `core-functions/core/wpb_setup.php` |

## Si la carpeta no existe

Créala (minúsculas, un propósito) y pon el archivo ahí. Actualiza mentalmente el mapa: un tipo = una carpeta bajo `core-functions/`.
