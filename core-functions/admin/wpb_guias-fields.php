<?php
/**
 * ACF fields for the Guías page.
 *
 * @package wpboilerplate
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Registers Guías field groups.
 */
function wpb_register_guias_fields() {
	wpb_acf_home_field_group(
		'group_wpb_guias_banner',
		__( 'Banner principal', 'wpboilerplate' ),
		'wpb-guias-banner',
		array(
			wpb_acf_image_field( 'field_wpb_guias_banner_desktop', 'guias_banner_desktop', __( 'Imagen desktop', 'wpboilerplate' ), __( 'Desde 1280px. Si subís una sola, se usa en todos los tamaños.', 'wpboilerplate' ) ),
			wpb_acf_image_field( 'field_wpb_guias_banner_tablet', 'guias_banner_tablet', __( 'Imagen tablet', 'wpboilerplate' ) ),
			wpb_acf_image_field( 'field_wpb_guias_banner_mobile', 'guias_banner_mobile', __( 'Imagen móvil', 'wpboilerplate' ) ),
		)
	);

	wpb_acf_home_field_group(
		'group_wpb_guias_community',
		__( 'Comunidad', 'wpboilerplate' ),
		'wpb-guias-community',
		array(
			wpb_acf_text_field( 'field_wpb_guias_community_title', 'guias_community_title', __( 'Título', 'wpboilerplate' ) ),
			wpb_acf_textarea_field( 'field_wpb_guias_community_text', 'guias_community_text', __( 'Texto', 'wpboilerplate' ), 3 ),
			wpb_acf_text_field( 'field_wpb_guias_community_placeholder', 'guias_community_placeholder', __( 'Placeholder del correo', 'wpboilerplate' ) ),
			wpb_acf_text_field( 'field_wpb_guias_community_button', 'guias_community_button', __( 'Texto del botón', 'wpboilerplate' ) ),
		)
	);

	wpb_acf_home_field_group(
		'group_wpb_guias_help',
		__( 'Cómo empezar', 'wpboilerplate' ),
		'wpb-guias-help',
		array(
			wpb_acf_image_field( 'field_wpb_guias_help_image', 'guias_help_image', __( 'Imagen', 'wpboilerplate' ) ),
			wpb_acf_text_field( 'field_wpb_guias_help_title', 'guias_help_title', __( 'Título', 'wpboilerplate' ) ),
			wpb_acf_textarea_field( 'field_wpb_guias_help_text', 'guias_help_text', __( 'Texto', 'wpboilerplate' ), 3 ),
			wpb_acf_link_field( 'field_wpb_guias_help_products', 'guias_help_products', __( 'Botón productos', 'wpboilerplate' ) ),
		)
	);
}
add_action( 'acf/init', 'wpb_register_guias_fields' );

/**
 * Use the supplements banner images until Guías has its own tablet upload.
 */
function wpb_seed_guias_banner_images() {
	if ( false !== get_option( 'options_guias_banner_tablet' ) ) {
		return;
	}

	$fields = array(
		'guias_banner_desktop' => array( 'supl_banner_desktop', 'field_wpb_guias_banner_desktop' ),
		'guias_banner_tablet'  => array( 'supl_banner_tablet', 'field_wpb_guias_banner_tablet' ),
		'guias_banner_mobile'  => array( 'supl_banner_mobile', 'field_wpb_guias_banner_mobile' ),
	);

	foreach ( $fields as $name => $source ) {
		$image_id = get_option( 'options_' . $source[0] );
		if ( ! $image_id ) {
			continue;
		}

		update_option( 'options_' . $name, $image_id );
		update_option( '_options_' . $name, $source[1] );
	}
}
add_action( 'acf/init', 'wpb_seed_guias_banner_images', 20 );
