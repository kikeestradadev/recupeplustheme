<?php
/**
 * ACF fields for Contacto global (site-wide WhatsApp / contact).
 *
 * Location: Options Page → Contacto global
 *
 * @package wpboilerplate
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Registers Contacto global field group.
 */
function wpb_register_contacto_global_fields() {
	wpb_acf_home_field_group(
		'group_wpb_contacto_global',
		__( 'Contacto global', 'wpboilerplate' ),
		'wpb-contacto-global',
		array(
			array(
				'key'          => 'field_wpb_global_whatsapp',
				'label'        => __( 'WhatsApp (URL wa.me)', 'wpboilerplate' ),
				'name'         => 'global_whatsapp',
				'type'         => 'url',
				'instructions' => __( 'Enlace único para el botón flotante, footer, banners y demás CTAs de WhatsApp del sitio.', 'wpboilerplate' ),
			),
		)
	);
}
add_action( 'acf/init', 'wpb_register_contacto_global_fields' );
