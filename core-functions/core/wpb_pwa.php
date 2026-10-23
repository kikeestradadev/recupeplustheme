<?php
/**
 * Progressive Web App: manifest, icons, and service worker.
 * The worker stays in the theme. WordPress prints it at the site root URL
 * so its scope covers the site, without copying a file outside the theme.
 *
 * @package wpboilerplate
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Script URL for the worker. The theme directory is a query argument, not a hardcoded folder.
 *
 * @return string
 */
function wpb_pwa_worker_url() {
	return add_query_arg(
		array(
			'wpb_pwa' => 'sw',
			'theme'   => trailingslashit( get_stylesheet_directory_uri() ),
		),
		home_url( '/' )
	);
}

/**
 * Manifest document for the current site URL.
 *
 * @return array<string, mixed>
 */
function wpb_pwa_manifest() {
	$icons_uri = trailingslashit( get_stylesheet_directory_uri() ) . 'pwa/icons/';
	$favicon   = trailingslashit( get_stylesheet_directory_uri() ) . 'images/favicon.png';

	return array(
		'id'               => trailingslashit( home_url( '/' ) ),
		'name'             => 'Recupé Plus',
		'short_name'       => 'Recupé',
		'start_url'        => trailingslashit( home_url( '/' ) ),
		'scope'            => trailingslashit( home_url( '/' ) ),
		'display'          => 'standalone',
		'orientation'      => 'portrait',
		'background_color' => '#ffffff',
		'theme_color'      => '#0B3D2E',
		'lang'             => 'es',
		'icons'            => array(
			array(
				'src'     => $icons_uri . 'icon-192.png',
				'sizes'   => '192x192',
				'type'    => 'image/png',
				'purpose' => 'any',
			),
			array(
				'src'     => $icons_uri . 'icon-512.png',
				'sizes'   => '512x512',
				'type'    => 'image/png',
				'purpose' => 'any',
			),
			array(
				'src'     => $favicon,
				'sizes'   => '512x512',
				'type'    => 'image/png',
				'purpose' => 'any',
			),
		),
	);
}

/**
 * Classic script made from the theme policy module plus the worker runtime.
 *
 * @return string
 */
function wpb_pwa_worker_source() {
	$dir    = trailingslashit( get_stylesheet_directory() ) . 'pwa/';
	$policy = is_readable( $dir . 'sw-policy.js' ) ? (string) file_get_contents( $dir . 'sw-policy.js' ) : '';
	$policy = preg_replace( '/^export /m', '', $policy );
	$runtime = is_readable( $dir . 'sw-runtime.js' ) ? (string) file_get_contents( $dir . 'sw-runtime.js' ) : '';

	return $policy . "\n" . $runtime;
}

/**
 * Serve the manifest or the service worker. Neither response is an HTML page.
 */
function wpb_pwa_serve() {
	$kind = isset( $_GET['wpb_pwa'] ) ? sanitize_key( wp_unslash( $_GET['wpb_pwa'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended

	if ( 'manifest' !== $kind && 'sw' !== $kind ) {
		return;
	}

	if ( ! defined( 'DONOTCACHEPAGE' ) ) {
		define( 'DONOTCACHEPAGE', true );
	}

	nocache_headers();

	if ( 'manifest' === $kind ) {
		header( 'Content-Type: application/manifest+json; charset=utf-8' );
		echo wp_json_encode( wpb_pwa_manifest() );
		exit;
	}

	$scope = wp_parse_url( home_url( '/' ), PHP_URL_PATH );
	header( 'Content-Type: application/javascript; charset=utf-8' );
	header( 'Service-Worker-Allowed: ' . ( is_string( $scope ) && '' !== $scope ? $scope : '/' ) );
	echo wpb_pwa_worker_source(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	exit;
}
add_action( 'init', 'wpb_pwa_serve', 0 );

/**
 * Manifest link and PWA meta tags.
 */
function wpb_pwa_head() {
	$manifest = add_query_arg( 'wpb_pwa', 'manifest', home_url( '/' ) );
	echo '<link rel="manifest" href="' . esc_url( $manifest ) . '">' . "\n";
	echo '<meta name="theme-color" content="#0B3D2E">' . "\n";
	echo '<meta name="apple-mobile-web-app-title" content="Recupé">' . "\n";
}
add_action( 'wp_head', 'wpb_pwa_head', 2 );

/**
 * Register the theme service worker and drop a worker that used to live at /sw.js.
 */
function wpb_pwa_footer() {
	if ( is_admin() ) {
		return;
	}

	$script = wpb_pwa_worker_url();
	$scope  = trailingslashit( home_url( '/' ) );
	?>
	<script>
	if ('serviceWorker' in navigator) {
		window.addEventListener('load', function () {
			var script = <?php echo wp_json_encode( $script ); ?>;
			var scope = <?php echo wp_json_encode( $scope ); ?>;
			navigator.serviceWorker.getRegistrations().then(function (registrations) {
				return Promise.all(registrations.map(function (registration) {
					var worker = registration.active || registration.waiting || registration.installing;
					var url = worker ? worker.scriptURL : '';
					if (url.indexOf('/sw.js') !== -1) {
						return registration.unregister();
					}
					return undefined;
				}));
			}).then(function () {
				return navigator.serviceWorker.register(script, { scope: scope });
			});
		});
	}
	</script>
	<?php
}
add_action( 'wp_footer', 'wpb_pwa_footer', 20 );
