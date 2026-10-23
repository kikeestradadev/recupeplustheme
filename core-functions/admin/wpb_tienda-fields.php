<?php
/**
 * ACF fields for the Tienda page banner.
 *
 * @package wpboilerplate
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Registers the Tienda banner field group.
 */
function wpb_register_tienda_fields() {
	wpb_acf_home_field_group(
		'group_wpb_tienda_banner',
		__( 'Banner principal', 'wpboilerplate' ),
		'wpb-tienda-banner',
		array(
			wpb_acf_text_field( 'field_wpb_tienda_banner_title', 'tienda_banner_title', __( 'Título', 'wpboilerplate' ) ),
			wpb_acf_textarea_field( 'field_wpb_tienda_banner_text', 'tienda_banner_text', __( 'Texto', 'wpboilerplate' ), 3 ),
			wpb_acf_image_field( 'field_wpb_tienda_banner_desktop', 'tienda_banner_desktop', __( 'Imagen desktop', 'wpboilerplate' ), __( 'Desde 1280px. Si subís una sola, se usa en todos los tamaños.', 'wpboilerplate' ) ),
			wpb_acf_image_field( 'field_wpb_tienda_banner_tablet', 'tienda_banner_tablet', __( 'Imagen tablet', 'wpboilerplate' ) ),
			wpb_acf_image_field( 'field_wpb_tienda_banner_mobile', 'tienda_banner_mobile', __( 'Imagen móvil', 'wpboilerplate' ) ),
		)
	);
}
add_action( 'acf/init', 'wpb_register_tienda_fields' );

/**
 * Copy the supplements banner images once, until Tienda has its own uploads.
 */
function wpb_seed_tienda_banner_images() {
	$fields = array(
		'tienda_banner_desktop' => 'field_wpb_tienda_banner_desktop',
		'tienda_banner_tablet'  => 'field_wpb_tienda_banner_tablet',
		'tienda_banner_mobile'  => 'field_wpb_tienda_banner_mobile',
	);
	$sources = array(
		'tienda_banner_desktop' => 'supl_banner_desktop',
		'tienda_banner_tablet'  => 'supl_banner_tablet',
		'tienda_banner_mobile'  => 'supl_banner_mobile',
	);

	foreach ( $fields as $name => $key ) {
		if ( false !== get_option( 'options_' . $name ) ) {
			continue;
		}

		$image_id = get_option( 'options_' . $sources[ $name ] );
		if ( ! $image_id ) {
			continue;
		}

		update_option( 'options_' . $name, $image_id );
		update_option( '_options_' . $name, $key );
	}
}
add_action( 'acf/init', 'wpb_seed_tienda_banner_images', 20 );
