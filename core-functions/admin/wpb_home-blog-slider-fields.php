<?php
/**
 * ACF fields for Home blog / guides slider.
 *
 * @package wpboilerplate
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Registers fields for Home → Guías.
 */
function wpb_register_home_blog_slider_fields() {
	wpb_acf_home_field_group(
		'group_wpb_home_blog_slider',
		__( 'Guías', 'wpboilerplate' ),
		'wpb-home-blog-slider',
		array(
			wpb_acf_text_field( 'field_wpb_home_blog_title', 'home_blog_title', __( 'Título de la sección', 'wpboilerplate' ) ),
			wpb_acf_repeater_field(
				'field_wpb_home_blog_slider',
				'home_blog_slider',
				__( 'Guías', 'wpboilerplate' ),
				array(
					wpb_acf_image_field( 'field_wpb_home_blog_image', 'image', __( 'Imagen', 'wpboilerplate' ) ),
					wpb_acf_image_alt_field( 'field_wpb_home_blog_image_alt' ),
					wpb_acf_text_field( 'field_wpb_home_blog_name', 'name', __( 'Título', 'wpboilerplate' ) ),
					wpb_acf_link_field( 'field_wpb_home_blog_link', 'link', __( 'Enlace', 'wpboilerplate' ) ),
				),
				'field_wpb_home_blog_name',
				__( 'Agregar guía', 'wpboilerplate' )
			),
		)
	);
}
add_action( 'acf/init', 'wpb_register_home_blog_slider_fields' );
wpb_acf_filter_empty_repeater(
	'home_blog_slider',
	array( 'image', 'name', 'link', 'field_wpb_home_blog_image', 'field_wpb_home_blog_name', 'field_wpb_home_blog_link' )
);
