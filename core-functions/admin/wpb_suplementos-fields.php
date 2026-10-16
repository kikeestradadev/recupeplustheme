<?php
/**
 * ACF fields for the Suplementos page.
 *
 * @package wpboilerplate
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Registers Suplementos field groups.
 */
function wpb_register_suplementos_fields() {
	wpb_acf_home_field_group(
		'group_wpb_suplementos_banner',
		__( 'Banner principal', 'wpboilerplate' ),
		'wpb-suplementos-banner',
		array(
			wpb_acf_text_field( 'field_wpb_supl_banner_title', 'supl_banner_title', __( 'Título', 'wpboilerplate' ) ),
			wpb_acf_textarea_field( 'field_wpb_supl_banner_text', 'supl_banner_text', __( 'Texto', 'wpboilerplate' ), 3 ),
			wpb_acf_image_field( 'field_wpb_supl_banner_desktop', 'supl_banner_desktop', __( 'Imagen desktop', 'wpboilerplate' ), __( 'Desde 1280px. Si subís una sola, se usa en todos los tamaños.', 'wpboilerplate' ) ),
			wpb_acf_image_field( 'field_wpb_supl_banner_tablet', 'supl_banner_tablet', __( 'Imagen tablet', 'wpboilerplate' ) ),
			wpb_acf_image_field( 'field_wpb_supl_banner_mobile', 'supl_banner_mobile', __( 'Imagen móvil', 'wpboilerplate' ) ),
		)
	);

	wpb_acf_home_field_group(
		'group_wpb_suplementos_ingredients',
		__( 'Ingredientes', 'wpboilerplate' ),
		'wpb-suplementos-ingredients',
		array(
			wpb_acf_text_field( 'field_wpb_supl_ing_eyebrow', 'supl_ing_eyebrow', __( 'Antetítulo', 'wpboilerplate' ) ),
			wpb_acf_text_field( 'field_wpb_supl_ing_title', 'supl_ing_title', __( 'Título', 'wpboilerplate' ) ),
			wpb_acf_image_field( 'field_wpb_supl_ing_image', 'supl_ing_image', __( 'Imagen', 'wpboilerplate' ) ),
			wpb_acf_repeater_field(
				'field_wpb_supl_ing_cards',
				'supl_ing_cards',
				__( 'Cards', 'wpboilerplate' ),
				array(
					wpb_acf_image_field( 'field_wpb_supl_ing_card_icon', 'icon', __( 'Ícono (opcional)', 'wpboilerplate' ) ),
					wpb_acf_text_field( 'field_wpb_supl_ing_card_name', 'name', __( 'Nombre', 'wpboilerplate' ) ),
					wpb_acf_textarea_field( 'field_wpb_supl_ing_card_text', 'text', __( 'Texto', 'wpboilerplate' ), 2 ),
				),
				'field_wpb_supl_ing_card_name',
				__( 'Agregar ingrediente', 'wpboilerplate' )
			),
		)
	);

	wpb_acf_home_field_group(
		'group_wpb_suplementos_features',
		__( 'Beneficios', 'wpboilerplate' ),
		'wpb-suplementos-features',
		array(
			wpb_acf_repeater_field(
				'field_wpb_supl_features',
				'supl_features',
				__( 'Ítems', 'wpboilerplate' ),
				array(
					wpb_acf_image_field( 'field_wpb_supl_feature_icon', 'icon', __( 'Ícono (opcional)', 'wpboilerplate' ) ),
					wpb_acf_text_field( 'field_wpb_supl_feature_label', 'label', __( 'Texto', 'wpboilerplate' ) ),
				),
				'field_wpb_supl_feature_label',
				__( 'Agregar ítem', 'wpboilerplate' )
			),
		)
	);

	wpb_acf_home_field_group(
		'group_wpb_suplementos_mushrooms',
		__( 'Hongos funcionales', 'wpboilerplate' ),
		'wpb-suplementos-mushrooms',
		array(
			wpb_acf_text_field( 'field_wpb_supl_mush_eyebrow', 'supl_mush_eyebrow', __( 'Antetítulo', 'wpboilerplate' ) ),
			wpb_acf_text_field( 'field_wpb_supl_mush_title', 'supl_mush_title', __( 'Título', 'wpboilerplate' ) ),
			wpb_acf_text_field( 'field_wpb_supl_mush_badge', 'supl_mush_badge', __( 'Texto del badge', 'wpboilerplate' ) ),
			wpb_acf_textarea_field( 'field_wpb_supl_mush_text', 'supl_mush_text', __( 'Texto', 'wpboilerplate' ), 4 ),
			wpb_acf_repeater_field(
				'field_wpb_supl_mush_items',
				'supl_mush_items',
				__( 'Hongos', 'wpboilerplate' ),
				array(
					wpb_acf_image_field( 'field_wpb_supl_mush_item_image', 'image', __( 'Imagen', 'wpboilerplate' ) ),
					wpb_acf_image_alt_field( 'field_wpb_supl_mush_item_image_alt' ),
					wpb_acf_textarea_field( 'field_wpb_supl_mush_item_description', 'description', __( 'Descripción', 'wpboilerplate' ), 2 ),
				),
				'field_wpb_supl_mush_item_description',
				__( 'Agregar hongo', 'wpboilerplate' )
			),
		)
	);
}
add_action( 'acf/init', 'wpb_register_suplementos_fields' );
wpb_acf_filter_empty_repeater(
	'supl_ing_cards',
	array( 'icon', 'name', 'text', 'field_wpb_supl_ing_card_icon', 'field_wpb_supl_ing_card_name', 'field_wpb_supl_ing_card_text' )
);
wpb_acf_filter_empty_repeater(
	'supl_features',
	array( 'icon', 'label', 'field_wpb_supl_feature_icon', 'field_wpb_supl_feature_label' )
);
wpb_acf_filter_empty_repeater(
	'supl_mush_items',
	array( 'image', 'image_alt', 'description', 'field_wpb_supl_mush_item_image', 'field_wpb_supl_mush_item_image_alt', 'field_wpb_supl_mush_item_description' )
);
