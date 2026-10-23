<?php
/**
 * ACF options menu: Tienda.
 *
 * @package wpboilerplate
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Registers the Tienda admin menu under Mantenimientos.
 */
function wpb_register_tienda_options_pages() {
	wpb_acf_register_options_menu(
		array(
			'title'    => __( 'Tienda', 'wpboilerplate' ),
			'slug'     => 'wpb-tienda',
			'icon'     => 'dashicons-store',
			'position' => 4,
			'pages'    => array(
				array(
					'title' => __( 'Banner principal', 'wpboilerplate' ),
					'slug'  => 'wpb-tienda-banner',
				),
			),
		)
	);
}
add_action( 'acf/init', 'wpb_register_tienda_options_pages' );
