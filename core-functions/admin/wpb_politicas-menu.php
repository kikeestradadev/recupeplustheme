<?php
/**
 * ACF options menu: Políticas.
 *
 * @package wpboilerplate
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Registers the Políticas admin menu under Mantenimientos.
 */
function wpb_register_politicas_options_pages() {
	wpb_acf_register_options_menu(
		array(
			'title'    => __( 'Políticas', 'wpboilerplate' ),
			'slug'     => 'wpb-politicas',
			'icon'     => 'dashicons-media-text',
			'position' => 8,
			'pages'    => array(
				array(
					'title' => __( 'Contenido', 'wpboilerplate' ),
					'slug'  => 'wpb-politicas-contenido',
				),
			),
		)
	);
}
add_action( 'acf/init', 'wpb_register_politicas_options_pages' );
