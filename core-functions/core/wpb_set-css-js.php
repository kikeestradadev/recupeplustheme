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

            // Load theme CSS synchronously. Async preload delayed logo/layout rules and let
            // the 2560–5000px logo bitmap paint at near-viewport size (height:auto + max-width:100%).
        }
    }
endif;
if ( !function_exists( 'wpb_enqueue_scripts' ) ) :
    function wpb_enqueue_scripts() {
        // Encolar JS personalizado del tema hijo
        $child_theme_js_path = get_stylesheet_directory() . '/theme-scripts.js';
        if (file_exists($child_theme_js_path)) {
            $script_deps = array( 'jquery', 'wpb-lazysizes' );
            if ( function_exists( 'wpb_front_needs_swiper' ) && wpb_front_needs_swiper() ) {
                $script_deps[] = 'wpb-swiper';
            }

            wp_enqueue_script(
                'child-theme-scripts',
                get_stylesheet_directory_uri() . '/theme-scripts.js',
                $script_deps,
                filemtime($child_theme_js_path),
                array(
                    'in_footer' => true,
                    'strategy'  => 'defer',
                )
            );
        }
    }
endif;
// Agregamos las acciones con prioridad 20 para asegurarnos de que se ejecutan después de que el tema padre haya encolado sus estilos y scripts
add_action('wp_enqueue_scripts', 'wpb_enqueue_styles', 20);
add_action('wp_enqueue_scripts', 'wpb_enqueue_scripts', 20);