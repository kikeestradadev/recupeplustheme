<?php
/**
 * ACF options menu: Nosotros.
 *
 * @package wpboilerplate
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Registers the Nosotros admin menu.
 */
function wpb_register_nosotros_options_pages() {
	wpb_acf_register_options_menu(
		array(
			'title'    => __( 'Nosotros', 'wpboilerplate' ),
			'slug'     => 'wpb-nosotros',
			'icon'     => 'dashicons-groups',
			'position' => 7,
			'pages'    => array(
				array(
					'title' => __( 'Banner principal', 'wpboilerplate' ),
					'slug'  => 'wpb-nosotros-banner',
				),
				array(
					'title' => __( 'Nuestra historia', 'wpboilerplate' ),
					'slug'  => 'wpb-nosotros-story',
				),
				array(
					'title' => __( 'Lo que creemos', 'wpboilerplate' ),
					'slug'  => 'wpb-nosotros-believe',
				),
				array(
					'title' => __( 'Fundadoras', 'wpboilerplate' ),
					'slug'  => 'wpb-nosotros-founders',
				),
			),
		)
	);
}
add_action( 'acf/init', 'wpb_register_nosotros_options_pages' );
