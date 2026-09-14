<?php
/**
 * Load Swiper only on pages that actually use a slider.
 *
 * @package wpboilerplate
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Whether the current front request should load Swiper.
 *
 * @return bool
 */
function wpb_front_needs_swiper() {
	$needs = is_front_page()
		|| is_page( array( 'home', 'suplementos', 'donde-comprar', 'guias', 'nosotros' ) )
		|| is_page_template(
			array(
				'page-home.php',
				'page-suplementos.php',
				'page-donde-comprar.php',
				'page-guias.php',
				'page-nosotros.php',
			)
		);

	return (bool) apply_filters( 'wpb_front_needs_swiper', $needs );
}

/**
 * Enqueues Swiper CSS/JS from the CDN when a slider page is shown.
 */
function wpb_enqueue_swiper_assets() {
	if ( is_admin() || ! wpb_front_needs_swiper() ) {
		return;
	}

	wp_enqueue_style(
		'wpb-swiper',
		'https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css',
		array(),
		'11'
	);

	wp_enqueue_script(
		'wpb-swiper',
		'https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js',
		array(),
		'11',
		array(
			'in_footer' => true,
			'strategy'  => 'defer',
		)
	);
}
add_action( 'wp_enqueue_scripts', 'wpb_enqueue_swiper_assets', 15 );
