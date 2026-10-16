<?php
/**
 * ACF options menu: Dónde comprar.
 *
 * @package wpboilerplate
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Registers the Dónde comprar admin menu.
 */
function wpb_register_donde_comprar_options_pages() {
	wpb_acf_register_options_menu(
		array(
			'title'    => __( 'Dónde comprar', 'wpboilerplate' ),
			'slug'     => 'wpb-donde-comprar',
			'icon'     => 'dashicons-location',
			'position' => 5,
			'pages'    => array(
				array(
					'title' => __( 'Banner principal', 'wpboilerplate' ),
					'slug'  => 'wpb-buy-banner',
				),
				array(
					'title' => __( 'Formas de comprar', 'wpboilerplate' ),
					'slug'  => 'wpb-buy-ways',
				),
				array(
					'title' => __( 'Aliados', 'wpboilerplate' ),
					'slug'  => 'wpb-buy-allies',
				),
				array(
					'title' => __( 'Encontranos cerca de vos', 'wpboilerplate' ),
					'slug'  => 'wpb-buy-locator',
				),
			),
		)
	);
}
add_action( 'acf/init', 'wpb_register_donde_comprar_options_pages' );
