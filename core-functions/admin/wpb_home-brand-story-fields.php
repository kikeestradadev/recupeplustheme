<?php
/**
 * ACF fields for Home brand story.
 *
 * @package wpboilerplate
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Registers fields for Home → Historia de marca.
 */
function wpb_register_home_brand_story_fields() {
	wpb_acf_home_field_group(
		'group_wpb_home_brand_story',
		__( 'Historia de marca', 'wpboilerplate' ),
		'wpb-home-brand-story',
		array(
			wpb_acf_image_field( 'field_wpb_home_brand_image', 'home_brand_image', __( 'Imagen', 'wpboilerplate' ) ),
			wpb_acf_text_field( 'field_wpb_home_brand_title', 'home_brand_title', __( 'Título', 'wpboilerplate' ) ),
			wpb_acf_repeater_field(
				'field_wpb_home_brand_texts',
				'home_brand_texts',
				__( 'Párrafos', 'wpboilerplate' ),
				array(
					wpb_acf_textarea_field( 'field_wpb_home_brand_text', 'text', __( 'Texto', 'wpboilerplate' ), 3 ),
				),
				'field_wpb_home_brand_text',
				__( 'Agregar párrafo', 'wpboilerplate' )
			),
			wpb_acf_link_field( 'field_wpb_home_brand_cta', 'home_brand_cta', __( 'Botón', 'wpboilerplate' ) ),
		)
	);
}
add_action( 'acf/init', 'wpb_register_home_brand_story_fields' );
wpb_acf_filter_empty_repeater(
	'home_brand_texts',
	array( 'text', 'field_wpb_home_brand_text' )
);
