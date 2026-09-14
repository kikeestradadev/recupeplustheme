<?php
/**
 * ACF fields for Home format cards.
 *
 * @package wpboilerplate
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Registers fields for Home → Dos formas.
 */
function wpb_register_home_format_cards_fields() {
	wpb_acf_home_field_group(
		'group_wpb_home_format_cards',
		__( 'Dos formas', 'wpboilerplate' ),
		'wpb-home-format-cards',
		array(
			wpb_acf_text_field( 'field_wpb_home_format_title', 'home_format_title', __( 'Título de la sección', 'wpboilerplate' ) ),
			wpb_acf_repeater_field(
				'field_wpb_home_format_cards',
				'home_format_cards',
				__( 'Cards', 'wpboilerplate' ),
				array(
					array(
						'key'           => 'field_wpb_home_format_card_style',
						'label'         => __( 'Estilo', 'wpboilerplate' ),
						'name'          => 'style',
						'type'          => 'select',
						'choices'       => array(
							'gomitas'  => __( 'Gomitas', 'wpboilerplate' ),
							'capsulas' => __( 'Cápsulas', 'wpboilerplate' ),
						),
						'allow_null'    => 1,
						'return_format' => 'value',
					),
					wpb_acf_text_field( 'field_wpb_home_format_card_name', 'name', __( 'Nombre', 'wpboilerplate' ) ),
					wpb_acf_textarea_field( 'field_wpb_home_format_card_text', 'text', __( 'Texto', 'wpboilerplate' ), 3 ),
					wpb_acf_textarea_field( 'field_wpb_home_format_card_list', 'list', __( 'Lista (un ítem por línea)', 'wpboilerplate' ), 6 ),
					wpb_acf_link_field( 'field_wpb_home_format_card_cta', 'cta', __( 'Botón', 'wpboilerplate' ) ),
					wpb_acf_image_field( 'field_wpb_home_format_card_image', 'image', __( 'Imagen', 'wpboilerplate' ) ),
				),
				'field_wpb_home_format_card_name',
				__( 'Agregar card', 'wpboilerplate' )
			),
		)
	);
}
add_action( 'acf/init', 'wpb_register_home_format_cards_fields' );
wpb_acf_filter_empty_repeater(
	'home_format_cards',
	array( 'name', 'text', 'list', 'image', 'cta', 'field_wpb_home_format_card_name', 'field_wpb_home_format_card_text', 'field_wpb_home_format_card_list', 'field_wpb_home_format_card_image', 'field_wpb_home_format_card_cta' )
);
