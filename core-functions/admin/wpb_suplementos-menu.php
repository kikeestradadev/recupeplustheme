<?php
/**
 * ACF options menu: Suplementos.
 *
 * @package wpboilerplate
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Registers the Suplementos admin menu.
 */
function wpb_register_suplementos_options_pages() {
	wpb_acf_register_options_menu(
		array(
			'title'    => __( 'Suplementos', 'wpboilerplate' ),
			'slug'     => 'wpb-suplementos',
			'icon'     => 'dashicons-plus-alt',
			'position' => 4,
			'pages'    => array(
				array(
					'title' => __( 'Banner principal', 'wpboilerplate' ),
					'slug'  => 'wpb-suplementos-banner',
				),
				array(
					'title' => __( 'Ingredientes', 'wpboilerplate' ),
					'slug'  => 'wpb-suplementos-ingredients',
				),
				array(
					'title' => __( 'Beneficios', 'wpboilerplate' ),
					'slug'  => 'wpb-suplementos-features',
				),
			),
		)
	);
}
add_action( 'acf/init', 'wpb_register_suplementos_options_pages' );
