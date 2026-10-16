<?php
/**
 * ACF options menu: Home + module maintenance.
 *
 * @package wpboilerplate
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Registers the Home admin menu and one submenu per Home module.
 */
function wpb_register_home_options_pages() {
	wpb_acf_register_options_menu(
		array(
			'title' => __( 'Home', 'wpboilerplate' ),
			'slug'  => 'wpb-home',
			'pages' => array(
				array(
					'title' => __( 'Banner principal', 'wpboilerplate' ),
					'slug'  => 'wpb-home-slider',
				),
				array(
					'title' => __( 'Por momento', 'wpboilerplate' ),
					'slug'  => 'wpb-home-moment-slider',
				),
				array(
					'title' => __( 'Dos formas', 'wpboilerplate' ),
					'slug'  => 'wpb-home-format-cards',
				),
				array(
					'title' => __( 'Ingredientes', 'wpboilerplate' ),
					'slug'  => 'wpb-home-ingredients-cards',
				),
				array(
					'title' => __( 'Historia de marca', 'wpboilerplate' ),
					'slug'  => 'wpb-home-brand-story',
				),
				array(
					'title' => __( 'Reseñas', 'wpboilerplate' ),
					'slug'  => 'wpb-home-reviews-slider',
				),
				array(
					'title' => __( 'Cómo empezar', 'wpboilerplate' ),
					'slug'  => 'wpb-home-start-banner',
				),
				array(
					'title' => __( 'Guías', 'wpboilerplate' ),
					'slug'  => 'wpb-home-blog-slider',
				),
				array(
					'title' => __( 'Comunidad', 'wpboilerplate' ),
					'slug'  => 'wpb-home-community-split',
				),
				array(
					'title' => __( 'Preguntas frecuentes', 'wpboilerplate' ),
					'slug'  => 'wpb-home-faqs',
				),
			),
		)
	);
}
add_action( 'acf/init', 'wpb_register_home_options_pages' );
