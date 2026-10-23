<?php
/**
 * ACF fields for the site footer (alt-footer).
 *
 * Location: Options Page → Footer → Contenido del footer
 * (site-wide; not tied to Front Page).
 *
 * @package wpboilerplate
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Select choices for footer column page pickers.
 *
 * Keys are either page IDs (numeric string) or path+anchor strings
 * (e.g. `/tienda/#gomitas`). Values are labels.
 *
 * @return array<string, string>
 */
function wpb_footer_page_link_choices() {
	$choices = array(
		'/tienda/#gomitas'  => __( 'Gomitas', 'wpboilerplate' ),
		'/tienda/#capsulas' => __( 'Cápsulas', 'wpboilerplate' ),
	);

	$priority_slugs = array(
		'page-preguntas-frecuentes',
		'page-politica-de-devoluciones',
		'page-terminos-y-condiciones',
		'page-politica-de-privacidad',
	);
	$priority_ids   = array( 136, 137, 138, 139 );

	foreach ( $priority_slugs as $slug ) {
		$page = get_page_by_path( $slug );
		if ( $page instanceof WP_Post ) {
			$choices[ (string) $page->ID ] = get_the_title( $page );
		}
	}

	foreach ( $priority_ids as $id ) {
		$key = (string) $id;
		if ( isset( $choices[ $key ] ) ) {
			continue;
		}
		$post = get_post( $id );
		if ( $post instanceof WP_Post && 'page' === $post->post_type && 'publish' === $post->post_status ) {
			$choices[ $key ] = get_the_title( $post );
		}
	}

	$pages = get_pages(
		array(
			'sort_column' => 'post_title',
			'sort_order'  => 'ASC',
			'post_status' => 'publish',
		)
	);

	foreach ( $pages as $page ) {
		$key = (string) $page->ID;
		if ( ! isset( $choices[ $key ] ) ) {
			$choices[ $key ] = get_the_title( $page );
		}
	}

	return $choices;
}

/**
 * Link-column repeater: searchable page select (includes legal/help pages).
 *
 * @param string $key_prefix Unique key prefix.
 * @param string $name       Field name.
 * @param string $label      Field label.
 * @return array<string, mixed>
 */
function wpb_footer_links_repeater( $key_prefix, $name, $label ) {
	$link_key = $key_prefix . '_link';

	return wpb_acf_repeater_field(
		$key_prefix,
		$name,
		$label,
		array(
			array(
				'key'           => $link_key,
				'label'         => __( 'Página', 'wpboilerplate' ),
				'name'          => 'link',
				'type'          => 'select',
				'choices'       => wpb_footer_page_link_choices(),
				'allow_null'    => 1,
				'ui'            => 1,
				'ajax'          => 0,
				'return_format' => 'value',
			),
		),
		$link_key,
		__( 'Agregar enlace', 'wpboilerplate' ),
		__( 'Elegí una página del sitio. Las filas vacías no se guardan.', 'wpboilerplate' )
	);
}

/**
 * Registers Footer field group.
 */
function wpb_register_footer_fields() {
	wpb_acf_home_field_group(
		'group_wpb_footer',
		__( 'Footer', 'wpboilerplate' ),
		'wpb-footer-content',
		array(
			// Brand.
			wpb_acf_image_field(
				'field_wpb_footer_logo',
				'footer_logo',
				__( 'Logo del footer', 'wpboilerplate' )
			),
			wpb_acf_image_alt_field( 'field_wpb_footer_logo_alt', 'footer_logo_alt' ),
			wpb_acf_textarea_field(
				'field_wpb_footer_brand_text',
				'footer_brand_text',
				__( 'Texto bajo el logo', 'wpboilerplate' ),
				3
			),

			// Social.
			wpb_acf_repeater_field(
				'field_wpb_footer_social',
				'footer_social',
				__( 'Redes sociales', 'wpboilerplate' ),
				array(
					array(
						'key'           => 'field_wpb_footer_social_platform',
						'label'         => __( 'Plataforma', 'wpboilerplate' ),
						'name'          => 'platform',
						'type'          => 'select',
						'choices'       => array(
							'facebook'  => 'Facebook',
							'instagram' => 'Instagram',
							'twitter'   => 'Twitter / X',
							'youtube'   => 'YouTube',
							'linkedin'  => 'LinkedIn',
							'tiktok'    => 'TikTok',
							'whatsapp'  => 'WhatsApp',
						),
						'allow_null'    => 0,
						'return_format' => 'value',
					),
					array(
						'key'   => 'field_wpb_footer_social_url',
						'label' => __( 'URL', 'wpboilerplate' ),
						'name'  => 'url',
						'type'  => 'url',
					),
				),
				'field_wpb_footer_social_platform',
				__( 'Agregar red social', 'wpboilerplate' )
			),

			// Columns.
			wpb_footer_links_repeater(
				'field_wpb_footer_supplements',
				'footer_supplements_links',
				__( 'Columna Tienda', 'wpboilerplate' )
			),
			wpb_footer_links_repeater(
				'field_wpb_footer_help',
				'footer_help_links',
				__( 'Columna Ayuda', 'wpboilerplate' )
			),
			wpb_footer_links_repeater(
				'field_wpb_footer_company',
				'footer_company_links',
				__( 'Columna Empresa', 'wpboilerplate' )
			),

			// Contact.
			wpb_acf_textarea_field(
				'field_wpb_footer_contact_address',
				'footer_contact_address',
				__( 'Contacto — dirección / texto', 'wpboilerplate' ),
				3
			),
			wpb_acf_text_field(
				'field_wpb_footer_contact_phone',
				'footer_contact_phone',
				__( 'Contacto — teléfono', 'wpboilerplate' )
			),
			wpb_acf_text_field(
				'field_wpb_footer_contact_email',
				'footer_contact_email',
				__( 'Contacto — email', 'wpboilerplate' )
			),
			wpb_acf_textarea_field(
				'field_wpb_footer_contact_extra',
				'footer_contact_extra',
				__( 'Contacto — líneas extra (una por renglón)', 'wpboilerplate' ),
				3
			),
			array(
				'key'          => 'field_wpb_footer_contact_map_embed',
				'label'        => __( 'Contacto — embed de mapa (opcional)', 'wpboilerplate' ),
				'name'         => 'footer_contact_map_embed',
				'type'         => 'textarea',
				'rows'         => 4,
				'instructions' => __( 'Código iframe de Google Maps, si hace falta en el footer.', 'wpboilerplate' ),
			),

			// Bottom.
			wpb_acf_text_field(
				'field_wpb_footer_copyright',
				'footer_copyright',
				__( 'Texto de copyright', 'wpboilerplate' )
			),
		)
	);
}
add_action( 'acf/init', 'wpb_register_footer_fields' );

/**
 * Point the saved Gomitas and Cápsulas footer links at the tienda page.
 */
function wpb_footer_use_tienda_links() {
	$map = array(
		'/suplementos/#gomitas'  => '/tienda/#gomitas',
		'/suplementos/#capsulas' => '/tienda/#capsulas',
	);

	foreach ( array( 0, 1 ) as $index ) {
		$name  = 'options_footer_supplements_links_' . $index . '_link';
		$value = get_option( $name );
		if ( is_string( $value ) && isset( $map[ $value ] ) ) {
			update_option( $name, $map[ $value ] );
		}
	}
}
add_action( 'init', 'wpb_footer_use_tienda_links' );

wpb_acf_filter_empty_repeater(
	'footer_social',
	array( 'platform', 'url', 'field_wpb_footer_social_platform', 'field_wpb_footer_social_url' )
);
wpb_acf_filter_empty_repeater(
	'footer_supplements_links',
	array( 'link', 'field_wpb_footer_supplements_link' )
);
wpb_acf_filter_empty_repeater(
	'footer_help_links',
	array( 'link', 'field_wpb_footer_help_link' )
);
wpb_acf_filter_empty_repeater(
	'footer_company_links',
	array( 'link', 'field_wpb_footer_company_link' )
);
