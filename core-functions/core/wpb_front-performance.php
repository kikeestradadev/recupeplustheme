<?php
/**
 * Keep page views and cart AJAX from waiting on scheduled tasks.
 *
 * @package wpboilerplate
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * wp-cron's loopback runs on init and blocks the response on this server,
 * including WooCommerce cart fragments. Direct requests to wp-cron.php still run.
 */
remove_action( 'init', 'wp_cron' );
