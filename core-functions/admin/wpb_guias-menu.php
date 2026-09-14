<?php
/**
 * ACF options menu: Guías.
 *
 * @package wpboilerplate
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Registers the Guías admin menu.
 */
function wpb_register_guias_options_pages() {
	wpb_acf_register_options_menu(
		array(
			'title'    => __( 'Guías', 'wpboilerplate' ),
			'slug'     => 'wpb-guias',
			'icon'     => 'dashicons-book-alt',
			'position' => 6,
			'pages'    => array(
				array(
					'title' => __( 'Panel principal', 'wpboilerplate' ),
					'slug'  => 'wpb-guias-banner',
				),
				array(
					'title' => __( 'Comunidad', 'wpboilerplate' ),
					'slug'  => 'wpb-guias-community',
				),
				array(
					'title' => __( 'No sabés por dónde empezar', 'wpboilerplate' ),
					'slug'  => 'wpb-guias-help',
				),
			),
		)
	);
}
add_action( 'acf/init', 'wpb_register_guias_options_pages' );
