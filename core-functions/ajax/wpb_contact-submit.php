<?php
/**
 * AJAX handler: contact form submission.
 *
 * @package wpboilerplate
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Handles contact form AJAX requests.
 */
function wpb_contact_submit_handler() {
	check_ajax_referer( 'wpb_contact_submit', 'nonce' );

	if ( ! empty( $_POST['website'] ) ) {
		wp_send_json_success(
			array(
				'message' => __( '¡Gracias! Recibimos tu mensaje.', 'wpboilerplate' ),
			)
		);
	}

	$result = wpb_contact_message_insert(
		array(
			'name'       => isset( $_POST['name'] ) ? wp_unslash( $_POST['name'] ) : '',
			'email'      => isset( $_POST['email'] ) ? wp_unslash( $_POST['email'] ) : '',
			'phone'      => isset( $_POST['phone'] ) ? wp_unslash( $_POST['phone'] ) : '',
			'message'    => isset( $_POST['message'] ) ? wp_unslash( $_POST['message'] ) : '',
			'ip'         => isset( $_SERVER['REMOTE_ADDR'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) ) : '',
			'user_agent' => isset( $_SERVER['HTTP_USER_AGENT'] ) ? sanitize_text_field( wp_unslash( $_SERVER['HTTP_USER_AGENT'] ) ) : '',
		)
	);

	if ( is_wp_error( $result ) ) {
		wp_send_json_error(
			array(
				'code'    => $result->get_error_code(),
				'message' => $result->get_error_message(),
			),
			400
		);
	}

	wp_send_json_success(
		array(
			'id'      => $result,
			'message' => __( '¡Gracias! Recibimos tu mensaje.', 'wpboilerplate' ),
		)
	);
}
add_action( 'wp_ajax_wpb_contact_submit', 'wpb_contact_submit_handler' );
add_action( 'wp_ajax_nopriv_wpb_contact_submit', 'wpb_contact_submit_handler' );
