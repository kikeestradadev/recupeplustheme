<?php
/**
 * Load lazysizes (CDN) for section/image lazy loading — same library/config as the LP.
 *
 * @package wpboilerplate
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Enqueues lazysizes from the CDN on the front end.
 */
function wpb_enqueue_lazysizes_assets() {
	if ( is_admin() ) {
		return;
	}

	wp_register_script(
		'wpb-lazysizes',
		'https://cdn.jsdelivr.net/npm/lazysizes@5.3.2/lazysizes.min.js',
		array(),
		'5.3.2',
		array(
			'in_footer' => true,
			'strategy'  => 'defer',
		)
	);

	wp_add_inline_script(
		'wpb-lazysizes',
		'window.lazySizesConfig = window.lazySizesConfig || {};
window.lazySizesConfig.expand = 480;
window.lazySizesConfig.expFactor = 1.5;
window.lazySizesConfig.iframeLoadMode = 1;',
		'before'
	);

	wp_enqueue_script( 'wpb-lazysizes' );
}
add_action( 'wp_enqueue_scripts', 'wpb_enqueue_lazysizes_assets', 15 );

/**
 * Early js class + 8s ready fallback (from the LP template head).
 */
function wpb_component_loader_boot_script() {
	if ( is_admin() ) {
		return;
	}
	?>
	<script>
		document.documentElement.classList.remove('no-js');
		document.documentElement.classList.add('js');
		window.setTimeout(function () {
			if (document.documentElement.dataset.componentsReady === 'true') return;
			document.querySelectorAll('.lazy-sections > section:not(:first-of-type), [data-lazy-component]').forEach(function (section) {
				section.classList.add('component-loader--ready');
				section.setAttribute('aria-busy', 'false');
			});
		}, 8000);
	</script>
	<?php
}
add_action( 'wp_head', 'wpb_component_loader_boot_script', 1 );
