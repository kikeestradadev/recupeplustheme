<?php
/**
 * ACF fields for Home community split.
 *
 * @package wpboilerplate
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Registers fields for Home → Comunidad.
 */
function wpb_register_home_community_split_fields() {
	wpb_acf_home_field_group(
		'group_wpb_home_community_split',
		__( 'Comunidad', 'wpboilerplate' ),
		'wpb-home-community-split',
		array(
			wpb_acf_image_field( 'field_wpb_home_community_image', 'home_community_image', __( 'Imagen', 'wpboilerplate' ) ),
			wpb_acf_text_field( 'field_wpb_home_community_title', 'home_community_title', __( 'Título', 'wpboilerplate' ) ),
			wpb_acf_textarea_field( 'field_wpb_home_community_text', 'home_community_text', __( 'Texto', 'wpboilerplate' ), 3 ),
			wpb_acf_text_field( 'field_wpb_home_community_cta_title', 'home_community_cta_title', __( 'Título del formulario', 'wpboilerplate' ) ),
			wpb_acf_text_field( 'field_wpb_home_community_placeholder', 'home_community_placeholder', __( 'Placeholder del correo', 'wpboilerplate' ) ),
			wpb_acf_text_field( 'field_wpb_home_community_button', 'home_community_button', __( 'Texto del botón', 'wpboilerplate' ) ),
		)
	);
}
add_action( 'acf/init', 'wpb_register_home_community_split_fields' );
