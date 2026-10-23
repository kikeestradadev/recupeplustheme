<?php
/**
 * Customer registration: the person chooses the password.
 * WooCommerce creates the account. This file checks the password rules,
 * skips the activation email, and shows the on-screen success message.
 *
 * @package wpboilerplate
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Same rules as js/components/registerForm.js.
 *
 * @param string $password Password.
 * @param string $confirm  Repeated password. Empty string skips the match check.
 * @return string[]
 */
function wpb_registration_password_errors( $password, $confirm = null ) {
	$password = is_string( $password ) ? $password : '';
	$errors   = array();

	if ( strlen( $password ) < 8 ) {
		$errors[] = 'La contraseña debe tener al menos 8 caracteres.';
	}
	if ( ! preg_match( '/[A-Z]/', $password ) ) {
		$errors[] = 'La contraseña debe incluir al menos una letra mayúscula.';
	}
	if ( ! preg_match( '/[0-9]/', $password ) ) {
		$errors[] = 'La contraseña debe incluir al menos un número.';
	}
	if ( ! preg_match( '/[^A-Za-z0-9]/', $password ) ) {
		$errors[] = 'La contraseña debe incluir al menos un carácter especial.';
	}
	if ( null !== $confirm && $password !== $confirm ) {
		$errors[] = 'Las contraseñas no coinciden.';
	}

	return $errors;
}

/**
 * Keep the password the customer typed. WooCommerce generates one when this is yes.
 */
function wpb_registration_use_customer_password() {
	if ( 'no' !== get_option( 'woocommerce_registration_generate_password' ) ) {
		update_option( 'woocommerce_registration_generate_password', 'no' );
	}
}
add_action( 'init', 'wpb_registration_use_customer_password' );

/**
 * Reject a registration WooCommerce would otherwise accept.
 *
 * @param WP_Error $errors   Errors.
 * @param string   $username Username.
 * @param string   $password Password.
 * @param string   $email    Email.
 * @return WP_Error
 */
function wpb_validate_registration_password( $errors, $username, $password, $email ) {
	unset( $username, $email );

	if ( ! $errors instanceof WP_Error ) {
		$errors = new WP_Error();
	}

	$confirm = null;
	if ( isset( $_POST['password_2'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Missing
		$confirm = $_POST['password_2']; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized, WordPress.Security.ValidatedSanitizedInput.MissingUnslash
	}

	foreach ( wpb_registration_password_errors( $password, $confirm ) as $message ) {
		$errors->add( 'wpb_registration_password', $message );
	}

	return $errors;
}
add_filter( 'woocommerce_process_registration_errors', 'wpb_validate_registration_password', 10, 4 );

/**
 * On-screen confirmation. Registration does not send an activation email.
 *
 * @return string
 */
function wpb_registration_success_message() {
	return 'El registro se hizo correctamente.';
}

/**
 * Stay on the registration page so the success message can be shown.
 * The new account is active and can log in from the account panel.
 *
 * @return bool
 */
function wpb_registration_skip_auto_login() {
	return false;
}
add_filter( 'woocommerce_registration_auth_new_customer', 'wpb_registration_skip_auto_login' );

/**
 * Do not send the WooCommerce new-account email. Registration only confirms on screen.
 *
 * @return bool
 */
function wpb_registration_disable_new_account_email() {
	return false;
}
add_filter( 'woocommerce_email_enabled_customer_new_account', 'wpb_registration_disable_new_account_email' );

/**
 * Replace the WooCommerce success notice with the on-screen confirmation.
 *
 * @param string $translated Translated text.
 * @param string $text       Original text.
 * @param string $domain     Text domain.
 * @return string
 */
function wpb_registration_success_notice( $translated, $text, $domain ) {
	if ( 'woocommerce' !== $domain ) {
		return $translated;
	}

	if ( 'Your account was created successfully. Your login details have been sent to your email address.' === $text
		|| 'Your account was created successfully and a password has been sent to your email address.' === $text ) {
		return wpb_registration_success_message();
	}

	return $translated;
}
add_filter( 'gettext', 'wpb_registration_success_notice', 10, 3 );
