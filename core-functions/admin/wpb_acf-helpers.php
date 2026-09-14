<?php
/**
 * Shared ACF helpers for theme options pages.
 *
 * @package wpboilerplate
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Registers a parent options menu and its ACF subpages.
 *
 * @param array<string, mixed> $args Menu args.
 */
function wpb_acf_register_options_menu( $args ) {
	if ( ! is_admin() || ! function_exists( 'acf_add_options_page' ) ) {
		return;
	}

	acf_add_options_page(
		array(
			'page_title' => $args['title'],
			'menu_title' => $args['title'],
			'menu_slug'  => $args['slug'],
			'capability' => 'edit_pages',
			'icon_url'   => isset( $args['icon'] ) ? $args['icon'] : 'dashicons-admin-generic',
			'position'   => isset( $args['position'] ) ? $args['position'] : 10,
			'redirect'   => true,
		)
	);

	if ( empty( $args['pages'] ) || ! is_array( $args['pages'] ) ) {
		return;
	}

	foreach ( $args['pages'] as $page ) {
		acf_add_options_sub_page(
			array(
				'page_title'  => $page['title'],
				'menu_title'  => $page['title'],
				'menu_slug'   => $page['slug'],
				'parent_slug' => $args['slug'],
				'capability'  => 'edit_pages',
			)
		);
	}
}

/**
 * Registers a field group on an options subpage.
 *
 * @param string               $key    Group key.
 * @param string               $title  Group title.
 * @param string               $slug   Options page slug.
 * @param array<int, mixed>    $fields ACF fields.
 */
function wpb_acf_home_field_group( $key, $title, $slug, $fields ) {
	if ( ! function_exists( 'acf_add_local_field_group' ) ) {
		return;
	}

	acf_add_local_field_group(
		array(
			'key'                   => $key,
			'title'                 => $title,
			'fields'                => $fields,
			'location'              => array(
				array(
					array(
						'param'    => 'options_page',
						'operator' => '==',
						'value'    => $slug,
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

/**
 * Text field definition.
 *
 * @param string $key   Field key.
 * @param string $name  Field name.
 * @param string $label Field label.
 * @return array<string, mixed>
 */
function wpb_acf_text_field( $key, $name, $label ) {
	return array(
		'key'   => $key,
		'label' => $label,
		'name'  => $name,
		'type'  => 'text',
	);
}

/**
 * Textarea field definition.
 *
 * @param string $key   Field key.
 * @param string $name  Field name.
 * @param string $label Field label.
 * @param int    $rows  Rows.
 * @return array<string, mixed>
 */
function wpb_acf_textarea_field( $key, $name, $label, $rows = 3 ) {
	return array(
		'key'   => $key,
		'label' => $label,
		'name'  => $name,
		'type'  => 'textarea',
		'rows'  => $rows,
	);
}

/**
 * Image field definition.
 *
 * @param string $key          Field key.
 * @param string $name         Field name.
 * @param string $label        Field label.
 * @param string $instructions Optional help text.
 * @return array<string, mixed>
 */
function wpb_acf_image_field( $key, $name, $label, $instructions = '' ) {
	return array(
		'key'           => $key,
		'label'         => $label,
		'name'          => $name,
		'type'          => 'image',
		'instructions'  => $instructions,
		'required'      => 0,
		'return_format' => 'array',
		'preview_size'  => 'medium',
		'library'       => 'all',
	);
}

/**
 * Link field definition.
 *
 * @param string $key   Field key.
 * @param string $name  Field name.
 * @param string $label Field label.
 * @return array<string, mixed>
 */
function wpb_acf_link_field( $key, $name, $label ) {
	return array(
		'key'           => $key,
		'label'         => $label,
		'name'          => $name,
		'type'          => 'link',
		'return_format' => 'array',
	);
}

/**
 * Repeater field definition.
 *
 * @param string            $key          Field key.
 * @param string            $name         Field name.
 * @param string            $label        Field label.
 * @param array<int, mixed> $sub_fields   Sub fields.
 * @param string            $collapsed    Collapsed subfield key.
 * @param string            $button       Button label.
 * @param string            $instructions Help text.
 * @return array<string, mixed>
 */
function wpb_acf_repeater_field( $key, $name, $label, $sub_fields, $collapsed = '', $button = '', $instructions = '' ) {
	return array(
		'key'           => $key,
		'label'         => $label,
		'name'          => $name,
		'type'          => 'repeater',
		'instructions'  => $instructions ? $instructions : __( 'Agrega una fila. Las filas vacías no se guardan.', 'wpboilerplate' ),
		'required'      => 0,
		'layout'        => 'block',
		'collapsed'     => $collapsed,
		'button_label'  => $button ? $button : __( 'Agregar', 'wpboilerplate' ),
		'min'           => 0,
		'max'           => 0,
		'default_value' => array(),
		'sub_fields'    => $sub_fields,
	);
}

/**
 * Drops empty repeater rows for a named field.
 *
 * @param string             $field_name Field name.
 * @param array<int, string> $keys       Keys that count as content.
 */
function wpb_acf_filter_empty_repeater( $field_name, $keys ) {
	$callback = function ( $value ) use ( $keys ) {
		if ( ! is_array( $value ) ) {
			return $value;
		}

		$kept = array();

		foreach ( $value as $row ) {
			if ( ! is_array( $row ) ) {
				continue;
			}

			foreach ( $keys as $key ) {
				if ( ! empty( $row[ $key ] ) ) {
					$kept[] = $row;
					break;
				}
			}
		}

		return $kept;
	};

	add_filter( 'acf/update_value/name=' . $field_name, $callback, 20 );

	if ( is_admin() ) {
		add_filter( 'acf/load_value/name=' . $field_name, $callback, 20 );
	}
}

/**
 * Normalizes an ACF image field to url / alt / width / height.
 *
 * @param mixed $image ACF image array or empty.
 * @return array<string, mixed>|null
 */
function wpb_acf_image_data( $image ) {
	if ( ! is_array( $image ) || empty( $image['url'] ) ) {
		return null;
	}

	return array(
		'url'    => $image['url'],
		'alt'    => isset( $image['alt'] ) ? $image['alt'] : '',
		'width'  => isset( $image['width'] ) ? (int) $image['width'] : 1200,
		'height' => isset( $image['height'] ) ? (int) $image['height'] : 900,
	);
}

/**
 * Reads one options field. Same field is not fetched twice in one request.
 *
 * @param string $name Field name.
 * @return mixed
 */
function wpb_acf_option( $name ) {
	static $cache = array();

	if ( array_key_exists( $name, $cache ) ) {
		return $cache[ $name ];
	}

	$cache[ $name ] = function_exists( 'get_field' ) ? get_field( $name, 'option' ) : null;

	return $cache[ $name ];
}

/**
 * Option text value.
 *
 * @param string $name Field name.
 * @return string
 */
function wpb_acf_option_text( $name ) {
	$value = wpb_acf_option( $name );

	return is_string( $value ) ? $value : '';
}

/**
 * Option image value.
 *
 * @param string $name Field name.
 * @return array<string, mixed>|null
 */
function wpb_acf_option_image( $name ) {
	return wpb_acf_image_data( wpb_acf_option( $name ) );
}

/**
 * Desktop / tablet / mobile images with fallback to whichever exists.
 *
 * @param string $desktop_name Desktop field.
 * @param string $tablet_name  Tablet field.
 * @param string $mobile_name  Mobile field.
 * @return array{desktop:?array,tablet:?array,mobile:?array}
 */
function wpb_acf_option_picture( $desktop_name, $tablet_name, $mobile_name ) {
	$desktop  = wpb_acf_option_image( $desktop_name );
	$tablet   = wpb_acf_option_image( $tablet_name );
	$mobile   = wpb_acf_option_image( $mobile_name );
	$fallback = $mobile ? $mobile : ( $tablet ? $tablet : $desktop );

	return array(
		'desktop' => $desktop ? $desktop : $fallback,
		'tablet'  => $tablet ? $tablet : $fallback,
		'mobile'  => $mobile ? $mobile : $fallback,
	);
}

/**
 * Option link value.
 *
 * @param mixed $link ACF link array.
 * @return array{url:string,title:string,target:string}
 */
function wpb_acf_link_data( $link ) {
	$data = array(
		'url'    => '',
		'title'  => '',
		'target' => '',
	);

	if ( ! is_array( $link ) || empty( $link['url'] ) ) {
		return $data;
	}

	$data['url']    = $link['url'];
	$data['title']  = isset( $link['title'] ) ? $link['title'] : '';
	$data['target'] = isset( $link['target'] ) ? $link['target'] : '';

	return $data;
}

/**
 * Option link field.
 *
 * @param string $name Field name.
 * @return array{url:string,title:string,target:string}
 */
function wpb_acf_option_link( $name ) {
	return wpb_acf_link_data( wpb_acf_option( $name ) );
}

/**
 * Option repeater rows.
 *
 * @param string $name Field name.
 * @return array<int, mixed>
 */
function wpb_acf_option_rows( $name ) {
	$rows = wpb_acf_option( $name );

	return is_array( $rows ) ? $rows : array();
}

/**
 * Splits a textarea into non-empty lines.
 *
 * @param mixed $text Textarea value.
 * @return array<int, string>
 */
function wpb_acf_lines( $text ) {
	if ( ! is_string( $text ) || '' === $text ) {
		return array();
	}

	$lines = preg_split( '/\r\n|\r|\n/', $text );
	$out   = array();

	foreach ( $lines as $line ) {
		$line = trim( $line );
		if ( '' !== $line ) {
			$out[] = $line;
		}
	}

	return $out;
}

/**
 * Star rating markup for reviews.
 *
 * @param int $rating 1-5.
 * @return string
 */
function wpb_reviews_stars_html( $rating ) {
	$rating = max( 1, min( 5, (int) $rating ) );
	$path   = 'M12 2.5l2.9 5.9 6.5.9-4.7 4.6 1.1 6.5L12 17.8 6.2 20.4l1.1-6.5L2.6 9.3l6.5-.9L12 2.5z';
	$html   = '<div class="reviews-slider__rating" aria-label="' . esc_attr( $rating . ' de 5 estrellas' ) . '">';

	for ( $i = 0; $i < $rating; $i++ ) {
		$html .= '<svg class="reviews-slider__star" width="16" height="16" viewBox="0 0 24 24" aria-hidden="true"><path d="' . $path . '" fill="currentColor"></path></svg>';
	}

	$html .= '</div>';

	return $html;
}
