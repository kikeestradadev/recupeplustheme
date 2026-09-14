<?php
/**
 * ACF fields for Home FAQs.
 *
 * @package wpboilerplate
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Registers fields for Home → Preguntas frecuentes.
 */
function wpb_register_home_faqs_fields() {
	wpb_acf_home_field_group(
		'group_wpb_home_faqs',
		__( 'Preguntas frecuentes', 'wpboilerplate' ),
		'wpb-home-faqs',
		array(
			wpb_acf_text_field( 'field_wpb_home_faqs_title', 'home_faqs_title', __( 'Título de la sección', 'wpboilerplate' ) ),
			wpb_acf_textarea_field( 'field_wpb_home_faqs_intro', 'home_faqs_intro', __( 'Introducción', 'wpboilerplate' ), 3 ),
			wpb_acf_repeater_field(
				'field_wpb_home_faqs',
				'home_faqs',
				__( 'Preguntas', 'wpboilerplate' ),
				array(
					wpb_acf_text_field( 'field_wpb_home_faqs_question', 'question', __( 'Pregunta', 'wpboilerplate' ) ),
					wpb_acf_textarea_field( 'field_wpb_home_faqs_answer', 'answer', __( 'Respuesta', 'wpboilerplate' ), 4 ),
				),
				'field_wpb_home_faqs_question',
				__( 'Agregar pregunta', 'wpboilerplate' )
			),
		)
	);
}
add_action( 'acf/init', 'wpb_register_home_faqs_fields' );
wpb_acf_filter_empty_repeater(
	'home_faqs',
	array( 'question', 'answer', 'field_wpb_home_faqs_question', 'field_wpb_home_faqs_answer' )
);
