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
	if ( ! is_admin() || ! function_exists( 'acf_add_options_page' ) ) {
		return;
	}

	acf_add_options_page(
		array(
			'page_title' => __( 'Home', 'wpboilerplate' ),
			'menu_title' => __( 'Home', 'wpboilerplate' ),
			'menu_slug'  => 'wpb-home',
			'capability' => 'edit_pages',
			'icon_url'   => 'dashicons-admin-home',
			'position'   => 3,
			'redirect'   => true,
		)
	);

	$pages = array(
		array(
			'title' => __( 'Slider uno', 'wpboilerplate' ),
			'slug'  => 'wpb-home-slider',
		),
		array(
			'title' => __( 'Slider Moment Slider', 'wpboilerplate' ),
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
			'title' => __( 'No sabés por dónde empezar', 'wpboilerplate' ),
			'slug'  => 'wpb-home-start-banner',
		),
		array(
			'title' => __( 'Guías para sentirte mejor', 'wpboilerplate' ),
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
	);

	foreach ( $pages as $page ) {
		acf_add_options_sub_page(
			array(
				'page_title'  => $page['title'],
				'menu_title'  => $page['title'],
				'menu_slug'   => $page['slug'],
				'parent_slug' => 'wpb-home',
				'capability'  => 'edit_pages',
			)
		);
	}
}
add_action( 'acf/init', 'wpb_register_home_options_pages' );
