<?php
/**
 * Registers the Top Widget sidebar.
 *
 * @package wpboilerplate
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Registers the top widget area.
 */
function wpb_top_widget() {
	register_sidebar(
		array(
			'name'          => __( 'Top Widget', 'wpboilerplate' ),
			'id'            => 'top-widget',
			'description'   => __( 'Widgets in this area will be shown in the top widget area.', 'wpboilerplate' ),
			'before_widget' => '<div id="%1$s" class="widget %2$s">',
			'after_widget'  => '</div>',
			'before_title'  => '<h2 class="widget-title">',
			'after_title'   => '</h2>',
		)
	);
}
add_action( 'widgets_init', 'wpb_top_widget' );
