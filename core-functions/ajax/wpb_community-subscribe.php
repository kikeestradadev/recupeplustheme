<?php
/**
 * AJAX handler: Community Split email registration.
 *
 * @package wpboilerplate
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Handles community subscribe AJAX requests.
 */
function wpb_community_subscribe_handler() {
	check_ajax_referer( 'wpb_community_subscribe', 'nonce' );

	// Honeypot: bots fill this hidden field.
	if ( ! empty( $_POST['website'] ) ) {
		wp_send_json_success(
			array(
				'message' => __( '¡Listo! Te sumaste a la comunidad.', 'wpboilerplate' ),
			)
		);
	}

	$email = isset( $_POST['email'] ) ? sanitize_email( wp_unslash( $_POST['email'] ) ) : '';

	if ( ! is_email( $email ) ) {
		wp_send_json_error(
			array(
				'code'    => 'invalid_email',
				'message' => __( 'Ingresa un correo válido.', 'wpboilerplate' ),
			),
			400
		);
	}

	$allowed_sources = array( 'community-split', 'newsletter-banner', 'community-banner' );
	$source          = isset( $_POST['source'] ) ? sanitize_key( wp_unslash( $_POST['source'] ) ) : 'community-split';
	if ( ! in_array( $source, $allowed_sources, true ) ) {
		$source = 'community-split';
	}

	$result = wpb_community_subscriber_insert(
		$email,
		array(
			'source'     => $source,
			'ip'         => isset( $_SERVER['REMOTE_ADDR'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) ) : '',
			'user_agent' => isset( $_SERVER['HTTP_USER_AGENT'] ) ? sanitize_text_field( wp_unslash( $_SERVER['HTTP_USER_AGENT'] ) ) : '',
		)
	);

	if ( is_wp_error( $result ) ) {
		$code    = $result->get_error_code();
		$message = $result->get_error_message();
		$status  = ( 'duplicate_email' === $code ) ? 409 : 400;

		wp_send_json_error(
			array(
				'code'    => $code,
				'message' => $message,
			),
			$status
		);
	}

	wp_send_json_success(
		array(
			'id'      => $result,
			'message' => __( '¡Listo! Te sumaste a la comunidad.', 'wpboilerplate' ),
		)
	);
}
add_action( 'wp_ajax_wpb_community_subscribe', 'wpb_community_subscribe_handler' );
add_action( 'wp_ajax_nopriv_wpb_community_subscribe', 'wpb_community_subscribe_handler' );
