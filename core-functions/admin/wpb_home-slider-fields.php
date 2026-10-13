<?php
/**
 * ACF fields for the Home main slider (image-only slides).
 *
 * @package wpboilerplate
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Registers the repeater used on Home → Banner principal.
 */
function wpb_register_home_slider_fields() {
	if ( ! function_exists( 'acf_add_local_field_group' ) ) {
		return;
	}

	acf_add_local_field_group(
		array(
			'key'                   => 'group_wpb_home_slider',
			'title'                 => __( 'Banner principal', 'wpboilerplate' ),
			'fields'                => array(
				array(
					'key'           => 'field_wpb_home_main_slider',
					'label'         => __( 'Slides', 'wpboilerplate' ),
					'name'          => 'home_main_slider',
					'type'          => 'repeater',
					'instructions'  => __( 'Solo imágenes. Podés subir una sola o las tres (desktop, tablet y móvil). Si falta alguna, se usa la que sí esté cargada. Completá el texto alternativo de cada slide.', 'wpboilerplate' ),
					'required'      => 0,
					'layout'        => 'block',
					'collapsed'     => 'field_wpb_home_slide_image_desktop',
					'button_label'  => __( 'Agregar slide', 'wpboilerplate' ),
					'min'           => 0,
					'max'           => 0,
					'default_value' => array(),
					'sub_fields'    => array(
						array(
							'key'           => 'field_wpb_home_slide_image_desktop',
							'label'         => __( 'Imagen desktop', 'wpboilerplate' ),
							'name'          => 'image_desktop',
							'type'          => 'image',
							'instructions'  => __( 'Desde 960px. Recomendado WebP liviano.', 'wpboilerplate' ),
							'required'      => 0,
							'return_format' => 'array',
							'preview_size'  => 'medium',
							'library'       => 'all',
						),
						array(
							'key'           => 'field_wpb_home_slide_image_tablet',
							'label'         => __( 'Imagen tablet', 'wpboilerplate' ),
							'name'          => 'image_tablet',
							'type'          => 'image',
							'instructions'  => __( 'De 640px a 959px.', 'wpboilerplate' ),
							'required'      => 0,
							'return_format' => 'array',
							'preview_size'  => 'medium',
							'library'       => 'all',
						),
						array(
							'key'           => 'field_wpb_home_slide_image_mobile',
							'label'         => __( 'Imagen móvil', 'wpboilerplate' ),
							'name'          => 'image_mobile',
							'type'          => 'image',
							'instructions'  => __( 'Debajo de 640px. Proporción típica ~500×625.', 'wpboilerplate' ),
							'required'      => 0,
							'return_format' => 'array',
							'preview_size'  => 'medium',
							'library'       => 'all',
						),
						wpb_acf_image_alt_field( 'field_wpb_home_slide_image_alt' ),
					),
				),
			),
			'location'              => array(
				array(
					array(
						'param'    => 'options_page',
						'operator' => '==',
						'value'    => 'wpb-home-slider',
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
add_action( 'acf/init', 'wpb_register_home_slider_fields' );
wpb_acf_filter_empty_repeater(
	'home_main_slider',
	array(
		'image_desktop',
		'image_tablet',
		'image_mobile',
		'field_wpb_home_slide_image_desktop',
		'field_wpb_home_slide_image_tablet',
		'field_wpb_home_slide_image_mobile',
	)
);

/**
 * Builds one front-end slide from an ACF repeater row.
 *
 * @param array<string, mixed> $row Repeater row.
 * @return array<string, mixed>|null
 */
function wpb_normalize_home_slide( $row ) {
	if ( ! is_array( $row ) ) {
		return null;
	}

	$desktop  = wpb_acf_image_data( isset( $row['image_desktop'] ) ? $row['image_desktop'] : null );
	$tablet   = wpb_acf_image_data( isset( $row['image_tablet'] ) ? $row['image_tablet'] : null );
	$mobile   = wpb_acf_image_data( isset( $row['image_mobile'] ) ? $row['image_mobile'] : null );
	$fallback = $mobile ? $mobile : ( $tablet ? $tablet : $desktop );

	if ( ! $fallback ) {
		return null;
	}

	$alt = wpb_acf_image_alt(
		isset( $row['image_alt'] ) ? $row['image_alt'] : '',
		$fallback,
		__( 'Recupé Plus', 'wpboilerplate' )
	);

	return array(
		'desktop' => $desktop ? $desktop : $fallback,
		'tablet'  => $tablet ? $tablet : $fallback,
		'mobile'  => $mobile ? $mobile : $fallback,
		'alt'     => $alt,
	);
}

/**
 * Slides for the Home main banner from the ACF repeater only.
 *
 * @return array<int, array<string, mixed>>
 */
function wpb_get_home_main_slider_slides() {
	$slides = array();
	$rows   = function_exists( 'wpb_acf_option_rows' ) ? wpb_acf_option_rows( 'home_main_slider' ) : array();

	if ( ! $rows ) {
		return $slides;
	}

	foreach ( $rows as $row ) {
		$slide = wpb_normalize_home_slide( $row );
		if ( $slide ) {
			$slides[] = $slide;
		}
	}

	return $slides;
}
