<?php
/**
 * Admin menu: Mantenimientos (parent for all theme admin screens).
 *
 * Registered with native add_menu_page so ACF options subpages and
 * custom list screens (Comunidad, Contactos) always attach under one parent,
 * even if ACF loads late or is unavailable.
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
 * Registers the Mantenimientos top-level menu early so children can attach.
 */
function wpb_register_mantenimientos_parent_menu() {
	if ( ! is_admin() ) {
		return;
	}

	add_menu_page(
		__( 'Mantenimientos', 'wpboilerplate' ),
		__( 'Mantenimientos', 'wpboilerplate' ),
		'edit_pages',
		wpb_mantenimientos_menu_slug(),
		'wpb_render_mantenimientos_parent_page',
		'dashicons-admin-generic',
		3
	);
}
add_action( 'admin_menu', 'wpb_register_mantenimientos_parent_menu', 5 );

/**
 * Parent landing: send editors to the first useful submenu when possible.
 */
function wpb_render_mantenimientos_parent_page() {
	if ( ! current_user_can( 'edit_pages' ) ) {
		return;
	}

	$registered = array();
	foreach ( (array) ( $GLOBALS['submenu'][ wpb_mantenimientos_menu_slug() ] ?? array() ) as $item ) {
		if ( ! empty( $item[2] ) ) {
			$registered[ $item[2] ] = true;
		}
	}

	foreach ( array( 'wpb-contacto-global' ) as $page ) {
		if ( isset( $registered[ $page ] ) ) {
			wp_safe_redirect( admin_url( 'admin.php?page=' . $page ) );
			exit;
		}
	}

	// Fall back to the first remaining submenu entry, if any.
	$first = array_key_first( $registered );
	if ( $first ) {
		wp_safe_redirect( admin_url( 'admin.php?page=' . $first ) );
		exit;
	}

	echo '<div class="wrap"><h1>' . esc_html__( 'Mantenimientos', 'wpboilerplate' ) . '</h1>';
	echo '<p>' . esc_html__( 'Usa el menú lateral para editar los contenidos y registros del sitio.', 'wpboilerplate' ) . '</p></div>';
}

/**
 * Hides the duplicate auto-generated parent submenu entry (same title as parent).
 */
function wpb_mantenimientos_hide_parent_submenu_duplicate() {
	remove_submenu_page( wpb_mantenimientos_menu_slug(), wpb_mantenimientos_menu_slug() );
}
add_action( 'admin_menu', 'wpb_mantenimientos_hide_parent_submenu_duplicate', 9999 );

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
