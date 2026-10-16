<?php
/**
 * ACF options menu: Contacto global.
 *
 * @package wpboilerplate
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Registers the Contacto global admin options page under Mantenimientos.
 */
function wpb_register_contacto_global_options_pages() {
	if ( ! is_admin() || ! function_exists( 'acf_add_options_sub_page' ) ) {
		return;
	}

	$parent_slug = function_exists( 'wpb_mantenimientos_menu_slug' )
		? wpb_mantenimientos_menu_slug()
		: 'wpb-mantenimientos';

	acf_add_options_sub_page(
		array(
			'page_title'  => __( 'Contacto global', 'wpboilerplate' ),
			'menu_title'  => __( 'Contacto global', 'wpboilerplate' ),
			'menu_slug'   => 'wpb-contacto-global',
			'parent_slug' => $parent_slug,
			'capability'  => 'edit_pages',
		)
	);
}
add_action( 'acf/init', 'wpb_register_contacto_global_options_pages' );
