<?php
/**
 * Site icon from the theme favicon.
 *
 * @package wpboilerplate
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register the theme favicon as the WordPress site icon once.
 */
function wpb_register_site_icon() {
	if ( (int) get_option( 'site_icon' ) > 0 ) {
		return;
	}

	$file = get_stylesheet_directory() . '/images/favicon.png';
	if ( ! is_readable( $file ) ) {
		return;
	}

	require_once ABSPATH . 'wp-admin/includes/file.php';
	require_once ABSPATH . 'wp-admin/includes/media.php';
	require_once ABSPATH . 'wp-admin/includes/image.php';

	$upload = wp_upload_bits( 'favicon.png', null, file_get_contents( $file ) );
	if ( ! empty( $upload['error'] ) ) {
		return;
	}

	$attachment_id = wp_insert_attachment(
		array(
			'post_mime_type' => 'image/png',
			'post_title'     => 'Favicon',
			'post_status'    => 'inherit',
		),
		$upload['file']
	);

	if ( ! $attachment_id || is_wp_error( $attachment_id ) ) {
		return;
	}

	require_once ABSPATH . 'wp-admin/includes/class-wp-site-icon.php';
	$site_icon = new WP_Site_Icon();
	add_filter( 'intermediate_image_sizes_advanced', array( $site_icon, 'additional_sizes' ) );
	wp_update_attachment_metadata( $attachment_id, wp_generate_attachment_metadata( $attachment_id, $upload['file'] ) );
	remove_filter( 'intermediate_image_sizes_advanced', array( $site_icon, 'additional_sizes' ) );
	update_option( 'site_icon', $attachment_id );
}
add_action( 'init', 'wpb_register_site_icon' );
