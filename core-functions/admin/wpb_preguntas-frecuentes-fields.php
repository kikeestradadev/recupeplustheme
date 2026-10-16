<?php
/**
 * ACF fields for the Preguntas frecuentes page.
 *
 * Location: page template `page-preguntas-frecuentes.php`
 * (same question/answer repeater pattern as Home → Preguntas frecuentes).
 *
 * @package wpboilerplate
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Registers fields for the FAQ page.
 */
function wpb_register_preguntas_frecuentes_fields() {
	if ( ! function_exists( 'acf_add_local_field_group' ) ) {
		return;
	}

	acf_add_local_field_group(
		array(
			'key'                   => 'group_wpb_preguntas_frecuentes',
			'title'                 => __( 'Preguntas frecuentes', 'wpboilerplate' ),
			'fields'                => array(
				wpb_acf_text_field( 'field_wpb_pf_title', 'pf_title', __( 'Título', 'wpboilerplate' ) ),
				wpb_acf_textarea_field( 'field_wpb_pf_intro', 'pf_intro', __( 'Introducción', 'wpboilerplate' ), 3 ),
				wpb_acf_repeater_field(
					'field_wpb_pf_faqs',
					'pf_faqs',
					__( 'Preguntas', 'wpboilerplate' ),
					array(
						wpb_acf_text_field( 'field_wpb_pf_faqs_question', 'question', __( 'Pregunta', 'wpboilerplate' ) ),
						wpb_acf_textarea_field( 'field_wpb_pf_faqs_answer', 'answer', __( 'Respuesta', 'wpboilerplate' ), 4 ),
					),
					'field_wpb_pf_faqs_question',
					__( 'Agregar pregunta', 'wpboilerplate' )
				),
			),
			'location'              => array(
				array(
					array(
						'param'    => 'page_template',
						'operator' => '==',
						'value'    => 'page-preguntas-frecuentes.php',
					),
				),
			),
			'menu_order'            => 0,
			'position'              => 'normal',
			'style'                 => 'default',
			'label_placement'       => 'top',
			'instruction_placement' => 'label',
			'active'                => true,
		)
	);
}
add_action( 'acf/init', 'wpb_register_preguntas_frecuentes_fields' );

wpb_acf_filter_empty_repeater(
	'pf_faqs',
	array( 'question', 'answer', 'field_wpb_pf_faqs_question', 'field_wpb_pf_faqs_answer' )
);
