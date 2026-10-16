<?php
/**
 * AJAX: filtered/paginated community subscribers list (admin).
 *
 * @package wpboilerplate
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Returns HTML for the results block (count + table + pagination).
 */
function wpb_community_subscribers_filter_handler() {
	if ( ! current_user_can( 'edit_pages' ) ) {
		wp_send_json_error(
			array( 'message' => __( 'No tienes permiso.', 'wpboilerplate' ) ),
			403
		);
	}

	check_ajax_referer( 'wpb_community_subscribers_filter', 'nonce' );

	if ( ! function_exists( 'wpb_render_community_subscribers_results' ) ) {
		wp_send_json_error(
			array( 'message' => __( 'El listado no está disponible.', 'wpboilerplate' ) ),
			500
		);
	}

	$filters = wpb_community_subscribers_request_filters( wp_unslash( $_POST ) );
	$paged   = wpb_community_subscribers_request_paged( wp_unslash( $_POST ) );

	ob_start();
	wpb_render_community_subscribers_results( $filters, $paged );
	$html = ob_get_clean();

	$total     = wpb_community_subscribers_count( $filters );
	$per_page  = wpb_community_subscribers_per_page();
	$max_pages = max( 1, (int) ceil( $total / $per_page ) );

	wp_send_json_success(
		array(
			'html'     => $html,
			'total'    => $total,
			'paged'    => $paged,
			'maxPages' => $max_pages,
			'filters'  => $filters,
		)
	);
}
add_action( 'wp_ajax_wpb_community_subscribers_filter', 'wpb_community_subscribers_filter_handler' );
