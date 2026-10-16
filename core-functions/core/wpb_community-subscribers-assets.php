<?php
/**
 * Front-end assets for community subscribe forms (localize).
 *
 * Shared by Community Split and Newsletter Banner.
 *
 * @package wpboilerplate
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Localizes front-end data for community subscribe forms.
 */
function wpb_community_subscribe_localize() {
	if ( is_admin() || ! wp_script_is( 'child-theme-scripts', 'enqueued' ) ) {
		return;
	}

	wp_localize_script(
		'child-theme-scripts',
		'wpbCommunitySubscribe',
		array(
			'ajaxUrl' => admin_url( 'admin-ajax.php' ),
			'action'  => 'wpb_community_subscribe',
			'nonce'   => wp_create_nonce( 'wpb_community_subscribe' ),
			'i18n'    => array(
				'sending'   => __( 'Enviando…', 'wpboilerplate' ),
				'success'   => __( '¡Listo! Te sumaste a la comunidad.', 'wpboilerplate' ),
				'duplicate' => __( 'Este correo ya está registrado.', 'wpboilerplate' ),
				'invalid'   => __( 'Ingresa un correo válido.', 'wpboilerplate' ),
				'error'     => __( 'No se pudo completar el registro. Intenta de nuevo.', 'wpboilerplate' ),
			),
		)
	);
}
add_action( 'wp_enqueue_scripts', 'wpb_community_subscribe_localize', 25 );
