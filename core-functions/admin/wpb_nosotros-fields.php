<?php
/**
 * ACF fields for the Nosotros page.
 *
 * @package wpboilerplate
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Registers Nosotros field groups.
 */
function wpb_register_nosotros_fields() {
	wpb_acf_home_field_group(
		'group_wpb_nosotros_banner',
		__( 'Banner principal', 'wpboilerplate' ),
		'wpb-nosotros-banner',
		array(
			wpb_acf_image_field( 'field_wpb_nos_banner_image', 'nos_banner_image', __( 'Imagen', 'wpboilerplate' ) ),
			wpb_acf_textarea_field( 'field_wpb_nos_banner_title', 'nos_banner_title', __( 'Título (una línea por renglón)', 'wpboilerplate' ), 4 ),
			wpb_acf_textarea_field( 'field_wpb_nos_banner_text', 'nos_banner_text', __( 'Texto', 'wpboilerplate' ), 3 ),
			wpb_acf_link_field( 'field_wpb_nos_banner_products', 'nos_banner_products', __( 'Botón productos', 'wpboilerplate' ) ),
			wpb_acf_link_field( 'field_wpb_nos_banner_whatsapp', 'nos_banner_whatsapp', __( 'Botón WhatsApp', 'wpboilerplate' ) ),
		)
	);

	wpb_acf_home_field_group(
		'group_wpb_nosotros_story',
		__( 'Nuestra historia', 'wpboilerplate' ),
		'wpb-nosotros-story',
		array(
			wpb_acf_image_field( 'field_wpb_nos_story_image', 'nos_story_image', __( 'Imagen', 'wpboilerplate' ) ),
			wpb_acf_text_field( 'field_wpb_nos_story_title', 'nos_story_title', __( 'Título', 'wpboilerplate' ) ),
			wpb_acf_repeater_field(
				'field_wpb_nos_story_texts',
				'nos_story_texts',
				__( 'Párrafos', 'wpboilerplate' ),
				array(
					wpb_acf_textarea_field( 'field_wpb_nos_story_text', 'text', __( 'Texto', 'wpboilerplate' ), 3 ),
				),
				'field_wpb_nos_story_text',
				__( 'Agregar párrafo', 'wpboilerplate' )
			),
		)
	);

	wpb_acf_home_field_group(
		'group_wpb_nosotros_believe',
		__( 'Lo que creemos', 'wpboilerplate' ),
		'wpb-nosotros-believe',
		array(
			wpb_acf_text_field( 'field_wpb_nos_believe_title', 'nos_believe_title', __( 'Título', 'wpboilerplate' ) ),
			wpb_acf_textarea_field( 'field_wpb_nos_believe_subtitle', 'nos_believe_subtitle', __( 'Subtítulo', 'wpboilerplate' ), 2 ),
			wpb_acf_repeater_field(
				'field_wpb_nos_believe_slides',
				'nos_believe_slides',
				__( 'Slides', 'wpboilerplate' ),
				array(
					wpb_acf_text_field( 'field_wpb_nos_believe_name', 'name', __( 'Título', 'wpboilerplate' ) ),
					wpb_acf_textarea_field( 'field_wpb_nos_believe_text', 'text', __( 'Texto', 'wpboilerplate' ), 3 ),
					wpb_acf_image_field( 'field_wpb_nos_believe_image', 'image', __( 'Imagen', 'wpboilerplate' ) ),
				),
				'field_wpb_nos_believe_name',
				__( 'Agregar slide', 'wpboilerplate' )
			),
		)
	);

	wpb_acf_home_field_group(
		'group_wpb_nosotros_founders',
		__( 'Fundadoras', 'wpboilerplate' ),
		'wpb-nosotros-founders',
		array(
			wpb_acf_text_field( 'field_wpb_nos_founders_title', 'nos_founders_title', __( 'Título', 'wpboilerplate' ) ),
			wpb_acf_textarea_field( 'field_wpb_nos_founders_subtitle', 'nos_founders_subtitle', __( 'Subtítulo', 'wpboilerplate' ), 2 ),
			wpb_acf_repeater_field(
				'field_wpb_nos_founders_cards',
				'nos_founders_cards',
				__( 'Fundadoras', 'wpboilerplate' ),
				array(
					wpb_acf_image_field( 'field_wpb_nos_founders_image', 'image', __( 'Foto', 'wpboilerplate' ) ),
					wpb_acf_text_field( 'field_wpb_nos_founders_name', 'name', __( 'Nombre', 'wpboilerplate' ) ),
					wpb_acf_text_field( 'field_wpb_nos_founders_role', 'role', __( 'Rol', 'wpboilerplate' ) ),
					wpb_acf_textarea_field( 'field_wpb_nos_founders_text', 'text', __( 'Texto', 'wpboilerplate' ), 4 ),
				),
				'field_wpb_nos_founders_name',
				__( 'Agregar fundadora', 'wpboilerplate' )
			),
		)
	);
}
add_action( 'acf/init', 'wpb_register_nosotros_fields' );
wpb_acf_filter_empty_repeater(
	'nos_story_texts',
	array( 'text', 'field_wpb_nos_story_text' )
);
wpb_acf_filter_empty_repeater(
	'nos_believe_slides',
	array( 'name', 'text', 'image', 'field_wpb_nos_believe_name', 'field_wpb_nos_believe_text', 'field_wpb_nos_believe_image' )
);
wpb_acf_filter_empty_repeater(
	'nos_founders_cards',
	array( 'image', 'name', 'role', 'text', 'field_wpb_nos_founders_image', 'field_wpb_nos_founders_name', 'field_wpb_nos_founders_role', 'field_wpb_nos_founders_text' )
);
