<?php
/**
 * Landing-page (GH Pages) asset base URL helpers.
 *
 * Static modules still reference LP assets until they are moved into the Media Library.
 * Override via filter `wpb_lp_assets_url` or by defining WPB_LP_ASSETS_URL early.
 *
 * @package wpboilerplate
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! defined( 'WPB_LP_ASSETS_URL' ) ) {
	define( 'WPB_LP_ASSETS_URL', 'https://kikeestradadev.github.io/recupepluslp/assets' );
}

/**
 * Returns the LP assets base URL (no trailing slash required from callers of wpb_lp_asset).
 *
 * @return string
 */
function wpb_lp_assets_url() {
	$base = apply_filters( 'wpb_lp_assets_url', WPB_LP_ASSETS_URL );
	return untrailingslashit( (string) $base );
}

/**
 * Builds an absolute URL to a path under the LP assets base.
 *
 * @param string $path Relative path, e.g. 'logonegro.png' or 'drive_24_julio/…'.
 * @return string
 */
function wpb_lp_asset( $path ) {
	$path = ltrim( (string) $path, '/' );
	return wpb_lp_assets_url() . ( '' !== $path ? '/' . $path : '' );
}
