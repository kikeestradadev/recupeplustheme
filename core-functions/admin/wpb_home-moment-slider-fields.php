<?php
/**
 * ACF fields for the Home moment slider.
 *
 * @package wpboilerplate
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Registers fields used on Home → Slider Moment Slider.
 */
function wpb_register_home_moment_slider_fields() {
	if ( ! function_exists( 'acf_add_local_field_group' ) ) {
		return;
	}

	acf_add_local_field_group(
		array(
			'key'                   => 'group_wpb_home_moment_slider',
			'title'                 => __( 'Slider Moment Slider', 'wpboilerplate' ),
			'fields'                => array(
				array(
					'key'   => 'field_wpb_home_moment_title',
					'label' => __( 'Título de la sección', 'wpboilerplate' ),
					'name'  => 'home_moment_title',
					'type'  => 'text',
				),
				array(
					'key'   => 'field_wpb_home_moment_subtitle',
					'label' => __( 'Subtítulo de la sección', 'wpboilerplate' ),
					'name'  => 'home_moment_subtitle',
					'type'  => 'textarea',
					'rows'  => 2,
				),
				array(
					'key'           => 'field_wpb_home_moment_slider',
					'label'         => __( 'Slides', 'wpboilerplate' ),
					'name'          => 'home_moment_slider',
					'type'          => 'repeater',
					'instructions'  => __( 'Agrega un momento por fila. Imagen, título, texto y enlace son opcionales; una fila vacía no se guarda.', 'wpboilerplate' ),
					'required'      => 0,
					'layout'        => 'block',
					'collapsed'     => 'field_wpb_home_moment_slide_title',
					'button_label'  => __( 'Agregar slide', 'wpboilerplate' ),
					'min'           => 0,
					'max'           => 0,
					'default_value' => array(),
					'sub_fields'    => array(
						array(
							'key'           => 'field_wpb_home_moment_slide_image',
							'label'         => __( 'Imagen', 'wpboilerplate' ),
							'name'          => 'image',
							'type'          => 'image',
							'required'      => 0,
							'return_format' => 'array',
							'preview_size'  => 'medium',
							'library'       => 'all',
						),
						array(
							'key'   => 'field_wpb_home_moment_slide_title',
							'label' => __( 'Título', 'wpboilerplate' ),
							'name'  => 'title',
							'type'  => 'text',
						),
						array(
							'key'   => 'field_wpb_home_moment_slide_text',
							'label' => __( 'Texto', 'wpboilerplate' ),
							'name'  => 'text',
							'type'  => 'textarea',
							'rows'  => 2,
						),
						array(
							'key'           => 'field_wpb_home_moment_slide_link',
							'label'         => __( 'Enlace', 'wpboilerplate' ),
							'name'          => 'link',
							'type'          => 'link',
							'return_format' => 'array',
						),
					),
				),
			),
			'location'              => array(
				array(
					array(
						'param'    => 'options_page',
						'operator' => '==',
						'value'    => 'wpb-home-moment-slider',
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
add_action( 'acf/init', 'wpb_register_home_moment_slider_fields' );
wpb_acf_filter_empty_repeater(
	'home_moment_slider',
	array(
		'image',
		'title',
		'text',
		'link',
		'field_wpb_home_moment_slide_image',
		'field_wpb_home_moment_slide_title',
		'field_wpb_home_moment_slide_text',
		'field_wpb_home_moment_slide_link',
	)
);

/**
 * Header copy for the moment slider.
 *
 * @return array{title:string,subtitle:string}
 */
function wpb_get_home_moment_slider_header() {
	return array(
		'title'    => function_exists( 'wpb_acf_option_text' ) ? wpb_acf_option_text( 'home_moment_title' ) : '',
		'subtitle' => function_exists( 'wpb_acf_option_text' ) ? wpb_acf_option_text( 'home_moment_subtitle' ) : '',
	);
}

/**
 * Builds one front-end moment card from an ACF repeater row.
 *
 * @param array<string, mixed> $row Repeater row.
 * @return array<string, mixed>|null
 */
function wpb_normalize_home_moment_slide( $row ) {
	if ( ! is_array( $row ) ) {
		return null;
	}

	$image = wpb_acf_image_data( isset( $row['image'] ) ? $row['image'] : null );

	$title = isset( $row['title'] ) ? $row['title'] : '';
	$text  = isset( $row['text'] ) ? $row['text'] : '';
	$link  = array(
		'url'    => '',
		'target' => '',
	);

	if ( ! empty( $row['link'] ) && is_array( $row['link'] ) && ! empty( $row['link']['url'] ) ) {
		$link['url']    = $row['link']['url'];
		$link['target'] = isset( $row['link']['target'] ) ? $row['link']['target'] : '';
	}

	if ( ! $image && '' === $title && '' === $text ) {
		return null;
	}

	$alt = '';
	if ( $image && ! empty( $image['alt'] ) ) {
		$alt = $image['alt'];
	} elseif ( $title ) {
		$alt = $title;
	}

	return array(
		'image'  => $image,
		'title'  => $title,
		'text'   => $text,
		'alt'    => $alt,
		'link'   => $link,
	);
}

/**
 * Cards for the Home moment slider from the ACF repeater only.
 *
 * @return array<int, array<string, mixed>>
 */
function wpb_get_home_moment_slider_slides() {
	$slides = array();
	$rows   = function_exists( 'wpb_acf_option_rows' ) ? wpb_acf_option_rows( 'home_moment_slider' ) : array();

	if ( ! $rows ) {
		return $slides;
	}

	foreach ( $rows as $row ) {
		$slide = wpb_normalize_home_moment_slide( $row );
		if ( $slide ) {
			$slides[] = $slide;
		}
	}

	return $slides;
}
