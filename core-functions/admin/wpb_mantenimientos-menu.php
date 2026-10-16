<?php
/**
 * ACF options menu: Mantenimientos (parent for all theme admin screens).
 *
 * @package wpboilerplate
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Parent menu slug for theme maintenance screens.
 *
 * @return string
 */
function wpb_mantenimientos_menu_slug() {
	return 'wpb-mantenimientos';
}

/**
 * Registers the Mantenimientos top-level options page.
 * Runs early so other menus can attach as children.
 */
function wpb_register_mantenimientos_options_page() {
	if ( ! is_admin() || ! function_exists( 'acf_add_options_page' ) ) {
		return;
	}

	acf_add_options_page(
		array(
			'page_title' => __( 'Mantenimientos', 'wpboilerplate' ),
			'menu_title' => __( 'Mantenimientos', 'wpboilerplate' ),
			'menu_slug'  => wpb_mantenimientos_menu_slug(),
			'capability' => 'edit_pages',
			'icon_url'   => 'dashicons-admin-generic',
			'position'   => 3,
			'redirect'   => true,
		)
	);
}
add_action( 'acf/init', 'wpb_register_mantenimientos_options_page', 1 );

/**
 * Enqueues collapsible grouping assets for the Mantenimientos admin menu.
 */
function wpb_mantenimientos_enqueue_menu_assets( $hook_suffix = '' ) {
	unset( $hook_suffix );

	$theme_uri = get_stylesheet_directory_uri();
	$theme_dir = get_stylesheet_directory();
	$css_rel   = '/css/admin/mantenimientos-menu.css';
	$js_rel    = '/js/admin/mantenimientosMenu.js';
	$css_path  = $theme_dir . $css_rel;
	$js_path   = $theme_dir . $js_rel;

	if ( file_exists( $css_path ) ) {
		wp_enqueue_style(
			'wpb-mantenimientos-menu',
			$theme_uri . $css_rel,
			array( 'dashicons', 'common' ),
			(string) filemtime( $css_path )
		);
	}

	if ( file_exists( $js_path ) ) {
		wp_enqueue_script(
			'wpb-mantenimientos-menu',
			$theme_uri . $js_rel,
			array(),
			(string) filemtime( $js_path ),
			true
		);
	}
}
add_action( 'admin_enqueue_scripts', 'wpb_mantenimientos_enqueue_menu_assets', 20 );
