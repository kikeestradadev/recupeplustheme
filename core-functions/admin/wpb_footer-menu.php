<?php
/**
 * ACF options menu: Footer.
 *
 * @package wpboilerplate
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Registers the Footer admin options page.
 */
function wpb_register_footer_options_pages() {
	wpb_acf_register_options_menu(
		array(
			'title'    => __( 'Footer', 'wpboilerplate' ),
			'slug'     => 'wpb-footer',
			'icon'     => 'dashicons-align-full-width',
			'position' => 58,
			'pages'    => array(
				array(
					'title' => __( 'Contenido del footer', 'wpboilerplate' ),
					'slug'  => 'wpb-footer-content',
				),
			),
		)
	);
}
add_action( 'acf/init', 'wpb_register_footer_options_pages' );
