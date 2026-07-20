<?php
// Salir si se intenta acceder directamente al archivo
if (!defined('ABSPATH')) {
    exit;
}
if ( !function_exists( 'wpb_enqueue_styles' ) ) :
    function wpb_enqueue_styles() {
        // Desencolar estilos del tema padre
        wp_dequeue_style('twenty-twenty-one-style');
        wp_deregister_style('twenty-twenty-one-style');
        // Encolar CSS personalizado del tema hijo
        $child_theme_css_path = get_stylesheet_directory() . '/theme-styles.css';
        if (file_exists($child_theme_css_path)) {
            wp_enqueue_style(
                'child-theme-styles', // Handle
                get_stylesheet_directory_uri() . '/theme-styles.css', // Ruta al archivo CSS del tema hijo
                [], // Un array de estilos de los que depende
                filemtime($child_theme_css_path) // Número de versión para el control de cache
            );

            // Agregar filtro para modificar el tag de enlace de CSS con preload
            add_filter('style_loader_tag', 'wpb_add_preload_to_styles', 10, 4);
            function wpb_add_preload_to_styles($html, $handle, $href, $media) {
                if ($handle === 'child-theme-styles') {
                    $html = '<link rel="preload" href="' . $href . '" as="style" onload="this.onload=null;this.rel=\'stylesheet\'"><noscript><link rel="stylesheet" href="' . $href . '"></noscript>';
                }
                return $html;
            }
        }
    }
endif;
if ( !function_exists( 'wpb_enqueue_scripts' ) ) :
    function wpb_enqueue_scripts() {
        // Encolar JS personalizado del tema hijo
        $child_theme_js_path = get_stylesheet_directory() . '/theme-scripts.js';
        if (file_exists($child_theme_js_path)) {
            wp_enqueue_script(
                'child-theme-scripts', // Handle
                get_stylesheet_directory_uri() . '/theme-scripts.js', // Ruta al archivo JS del tema hijo
                ['jquery'], // Un array de scripts de los que depende
                filemtime($child_theme_js_path), // Número de versión para el control de cache
                true // Cargar en el footer
            );
        }
    }
endif;
// Agregamos las acciones con prioridad 20 para asegurarnos de que se ejecutan después de que el tema padre haya encolado sus estilos y scripts
add_action('wp_enqueue_scripts', 'wpb_enqueue_styles', 20);
add_action('wp_enqueue_scripts', 'wpb_enqueue_scripts', 20);