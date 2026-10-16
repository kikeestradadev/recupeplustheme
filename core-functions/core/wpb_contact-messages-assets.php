<?php
/**
 * Front-end assets for the contact form (localize).
 *
 * @package wpboilerplate
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Localizes front-end data for the contact form.
 */
function wpb_contact_form_localize() {
	if ( is_admin() || ! wp_script_is( 'child-theme-scripts', 'enqueued' ) ) {
		return;
	}

	wp_localize_script(
		'child-theme-scripts',
		'wpbContactForm',
		array(
			'ajaxUrl' => admin_url( 'admin-ajax.php' ),
			'action'  => 'wpb_contact_submit',
			'nonce'   => wp_create_nonce( 'wpb_contact_submit' ),
			'i18n'    => array(
				'sending' => __( 'Enviando…', 'wpboilerplate' ),
				'success' => __( '¡Gracias! Recibimos tu mensaje.', 'wpboilerplate' ),
				'invalid' => __( 'Revisa los campos e intenta de nuevo.', 'wpboilerplate' ),
				'error'   => __( 'No se pudo enviar el mensaje. Intenta de nuevo.', 'wpboilerplate' ),
			),
		)
	);
}
add_action( 'wp_enqueue_scripts', 'wpb_contact_form_localize', 25 );
