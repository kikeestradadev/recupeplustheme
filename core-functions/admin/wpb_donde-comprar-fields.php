<?php
/**
 * ACF fields for the Dónde comprar page.
 *
 * @package wpboilerplate
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Registers Dónde comprar field groups.
 */
function wpb_register_donde_comprar_fields() {
	wpb_acf_home_field_group(
		'group_wpb_buy_banner',
		__( 'Panel principal', 'wpboilerplate' ),
		'wpb-buy-banner',
		array(
			wpb_acf_image_field( 'field_wpb_buy_banner_desktop', 'buy_banner_desktop', __( 'Imagen desktop', 'wpboilerplate' ), __( 'Desde 1280px. Si subís una sola, se usa en todos los tamaños.', 'wpboilerplate' ) ),
			wpb_acf_image_field( 'field_wpb_buy_banner_tablet', 'buy_banner_tablet', __( 'Imagen tablet', 'wpboilerplate' ) ),
			wpb_acf_image_field( 'field_wpb_buy_banner_mobile', 'buy_banner_mobile', __( 'Imagen móvil', 'wpboilerplate' ) ),
		)
	);

	wpb_acf_home_field_group(
		'group_wpb_buy_ways',
		__( '3 formas simples', 'wpboilerplate' ),
		'wpb-buy-ways',
		array(
			wpb_acf_text_field( 'field_wpb_buy_ways_title', 'buy_ways_title', __( 'Título de la sección', 'wpboilerplate' ) ),
			wpb_acf_repeater_field(
				'field_wpb_buy_ways_slides',
				'buy_ways_slides',
				__( 'Slides', 'wpboilerplate' ),
				array(
					wpb_acf_text_field( 'field_wpb_buy_ways_name', 'name', __( 'Título', 'wpboilerplate' ) ),
					wpb_acf_textarea_field( 'field_wpb_buy_ways_text', 'text', __( 'Texto', 'wpboilerplate' ), 2 ),
					wpb_acf_image_field( 'field_wpb_buy_ways_image', 'image', __( 'Imagen', 'wpboilerplate' ) ),
					wpb_acf_link_field( 'field_wpb_buy_ways_cta', 'cta', __( 'Botón', 'wpboilerplate' ) ),
					array(
						'key'           => 'field_wpb_buy_ways_icon',
						'label'         => __( 'Ícono', 'wpboilerplate' ),
						'name'          => 'icon',
						'type'          => 'select',
						'choices'       => array(
							'default'  => __( 'Tienda', 'wpboilerplate' ),
							'whatsapp' => __( 'WhatsApp', 'wpboilerplate' ),
							'pin'      => __( 'Ubicación', 'wpboilerplate' ),
						),
						'allow_null'    => 1,
						'return_format' => 'value',
					),
				),
				'field_wpb_buy_ways_name',
				__( 'Agregar forma', 'wpboilerplate' )
			),
		)
	);

	wpb_acf_home_field_group(
		'group_wpb_buy_allies',
		__( 'Aliados', 'wpboilerplate' ),
		'wpb-buy-allies',
		array(
			wpb_acf_text_field( 'field_wpb_buy_allies_title', 'buy_allies_title', __( 'Título de la sección', 'wpboilerplate' ) ),
			wpb_acf_repeater_field(
				'field_wpb_buy_allies_slides',
				'buy_allies_slides',
				__( 'Logos', 'wpboilerplate' ),
				array(
					wpb_acf_image_field( 'field_wpb_buy_allies_logo', 'logo', __( 'Logo', 'wpboilerplate' ) ),
				),
				'field_wpb_buy_allies_logo',
				__( 'Agregar aliado', 'wpboilerplate' )
			),
		)
	);
}
add_action( 'acf/init', 'wpb_register_donde_comprar_fields' );
wpb_acf_filter_empty_repeater(
	'buy_ways_slides',
	array( 'name', 'text', 'image', 'cta', 'field_wpb_buy_ways_name', 'field_wpb_buy_ways_text', 'field_wpb_buy_ways_image', 'field_wpb_buy_ways_cta' )
);
wpb_acf_filter_empty_repeater(
	'buy_allies_slides',
	array( 'logo', 'field_wpb_buy_allies_logo' )
);
