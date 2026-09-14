<?php
/**
 * ACF fields for Home reviews slider.
 *
 * @package wpboilerplate
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Registers fields for Home → Reseñas.
 */
function wpb_register_home_reviews_slider_fields() {
	wpb_acf_home_field_group(
		'group_wpb_home_reviews_slider',
		__( 'Reseñas', 'wpboilerplate' ),
		'wpb-home-reviews-slider',
		array(
			wpb_acf_text_field( 'field_wpb_home_reviews_title', 'home_reviews_title', __( 'Título de la sección', 'wpboilerplate' ) ),
			wpb_acf_repeater_field(
				'field_wpb_home_reviews_slider',
				'home_reviews_slider',
				__( 'Reseñas', 'wpboilerplate' ),
				array(
					wpb_acf_image_field( 'field_wpb_home_reviews_image', 'image', __( 'Foto', 'wpboilerplate' ) ),
					wpb_acf_textarea_field( 'field_wpb_home_reviews_quote', 'quote', __( 'Cita', 'wpboilerplate' ), 3 ),
					wpb_acf_text_field( 'field_wpb_home_reviews_author', 'author', __( 'Autor', 'wpboilerplate' ) ),
					array(
						'key'           => 'field_wpb_home_reviews_rating',
						'label'         => __( 'Estrellas', 'wpboilerplate' ),
						'name'          => 'rating',
						'type'          => 'number',
						'min'           => 1,
						'max'           => 5,
						'step'          => 1,
						'default_value' => 5,
					),
				),
				'field_wpb_home_reviews_author',
				__( 'Agregar reseña', 'wpboilerplate' )
			),
		)
	);
}
add_action( 'acf/init', 'wpb_register_home_reviews_slider_fields' );
wpb_acf_filter_empty_repeater(
	'home_reviews_slider',
	array( 'image', 'quote', 'author', 'field_wpb_home_reviews_image', 'field_wpb_home_reviews_quote', 'field_wpb_home_reviews_author' )
);
