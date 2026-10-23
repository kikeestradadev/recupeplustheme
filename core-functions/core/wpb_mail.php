<?php
/**
 * Mail stays on the local Mailpit settings unless staging defines WP Mail SMTP constants.
 *
 * Do not define these constants in this file or in the local wp-config.
 * WP Mail SMTP reads them only when WPMS_ON is true, and it reads them from wp-config
 * before the theme loads. While they are absent, the saved local SMTP settings stay in use.
 *
 * Add this block to staging wp-config.php, with the real SMTP account and a From address
 * on a domain that the provider has verified:
 *
 * define( 'WPMS_ON', true );
 * define( 'WPMS_MAILER', 'smtp' );
 * define( 'WPMS_SMTP_HOST', 'smtp.example.com' );
 * define( 'WPMS_SMTP_PORT', 587 );
 * define( 'WPMS_SSL', 'tls' ); // tls, ssl, or '' for none.
 * define( 'WPMS_SMTP_AUTH', true );
 * define( 'WPMS_SMTP_AUTOTLS', true );
 * define( 'WPMS_SMTP_USER', 'smtp-user' );
 * define( 'WPMS_SMTP_PASS', 'smtp-password' );
 * define( 'WPMS_MAIL_FROM', 'hola@recupeplus.com' );
 * define( 'WPMS_MAIL_FROM_FORCE', true );
 * define( 'WPMS_MAIL_FROM_NAME', 'Recupé Plus' );
 * define( 'WPMS_MAIL_FROM_NAME_FORCE', true );
 *
 * @package wpboilerplate
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Whether this site is the local MAMP install.
 *
 * @return bool
 */
function wpb_mail_is_local_site() {
	$host = wp_parse_url( home_url(), PHP_URL_HOST );
	return in_array( $host, array( 'localhost', '127.0.0.1' ), true );
}

/**
 * Warn in wp-admin when a non-local site is still using the loopback SMTP host.
 */
function wpb_mail_staging_notice() {
	if ( ! is_admin() || ! current_user_can( 'manage_options' ) || wpb_mail_is_local_site() || defined( 'WPMS_SMTP_HOST' ) ) {
		return;
	}

	$options = get_option( 'wp_mail_smtp' );
	$host    = is_array( $options ) && isset( $options['smtp']['host'] ) ? (string) $options['smtp']['host'] : '';

	if ( ! in_array( $host, array( '127.0.0.1', 'localhost' ), true ) ) {
		return;
	}

	echo '<div class="notice notice-warning"><p>';
	echo esc_html( 'El correo sigue apuntando a un SMTP local. En staging definí WPMS_ON y las constantes WPMS_SMTP_* en wp-config.php, con credenciales reales y un remitente de un dominio verificado. La guía está en core-functions/core/wpb_mail.php.' );
	echo '</p></div>';
}
add_action( 'admin_notices', 'wpb_mail_staging_notice' );
