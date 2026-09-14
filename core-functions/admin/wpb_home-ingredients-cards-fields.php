<?php
/**
 * ACF fields for Home ingredients cards.
 *
 * @package wpboilerplate
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Registers fields for Home → Ingredientes.
 */
function wpb_register_home_ingredients_cards_fields() {
	wpb_acf_home_field_group(
		'group_wpb_home_ingredients_cards',
		__( 'Ingredientes', 'wpboilerplate' ),
		'wpb-home-ingredients-cards',
		array(
			wpb_acf_text_field( 'field_wpb_home_ingredients_title', 'home_ingredients_title', __( 'Título de la sección', 'wpboilerplate' ) ),
			wpb_acf_repeater_field(
				'field_wpb_home_ingredients_cards',
				'home_ingredients_cards',
				__( 'Cards', 'wpboilerplate' ),
				array(
					wpb_acf_image_field( 'field_wpb_home_ingredients_card_image', 'image', __( 'Imagen', 'wpboilerplate' ) ),
					wpb_acf_text_field( 'field_wpb_home_ingredients_card_name', 'name', __( 'Nombre', 'wpboilerplate' ) ),
					wpb_acf_textarea_field( 'field_wpb_home_ingredients_card_list', 'list', __( 'Lista (un ítem por línea)', 'wpboilerplate' ), 6 ),
				),
				'field_wpb_home_ingredients_card_name',
				__( 'Agregar card', 'wpboilerplate' )
			),
		)
	);
}
add_action( 'acf/init', 'wpb_register_home_ingredients_cards_fields' );
wpb_acf_filter_empty_repeater(
	'home_ingredients_cards',
	array( 'image', 'name', 'list', 'field_wpb_home_ingredients_card_image', 'field_wpb_home_ingredients_card_name', 'field_wpb_home_ingredients_card_list' )
);
