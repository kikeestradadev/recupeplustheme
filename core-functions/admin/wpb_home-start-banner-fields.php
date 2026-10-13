<?php
/**
 * ACF fields for Home start banner.
 *
 * @package wpboilerplate
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Registers fields for Home → Cómo empezar.
 */
function wpb_register_home_start_banner_fields() {
	wpb_acf_home_field_group(
		'group_wpb_home_start_banner',
		__( 'Cómo empezar', 'wpboilerplate' ),
		'wpb-home-start-banner',
		array(
			wpb_acf_text_field( 'field_wpb_home_start_title', 'home_start_title', __( 'Título', 'wpboilerplate' ) ),
			wpb_acf_textarea_field( 'field_wpb_home_start_text', 'home_start_text', __( 'Texto', 'wpboilerplate' ), 3 ),
			wpb_acf_link_field( 'field_wpb_home_start_whatsapp', 'home_start_whatsapp', __( 'Botón WhatsApp', 'wpboilerplate' ) ),
			wpb_acf_link_field( 'field_wpb_home_start_products', 'home_start_products', __( 'Botón productos', 'wpboilerplate' ) ),
			wpb_acf_image_field( 'field_wpb_home_start_image_desktop', 'home_start_image_desktop', __( 'Imagen desktop', 'wpboilerplate' ), __( 'Opcional. Desde 1280px.', 'wpboilerplate' ) ),
			wpb_acf_image_field( 'field_wpb_home_start_image_tablet', 'home_start_image_tablet', __( 'Imagen tablet', 'wpboilerplate' ), __( 'Opcional. De 960px a 1279px.', 'wpboilerplate' ) ),
			wpb_acf_image_field( 'field_wpb_home_start_image_mobile', 'home_start_image_mobile', __( 'Imagen móvil', 'wpboilerplate' ), __( 'Opcional. Debajo de 960px. Si subís una sola, se usa en todos los tamaños.', 'wpboilerplate' ) ),
		)
	);
}
add_action( 'acf/init', 'wpb_register_home_start_banner_fields' );
