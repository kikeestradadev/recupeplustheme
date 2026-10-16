<?php
/**
 * AJAX: filtered/paginated contact messages list (admin).
 *
 * @package wpboilerplate
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Returns HTML for the contact messages results block.
 */
function wpb_contact_messages_filter_handler() {
	if ( ! current_user_can( 'edit_pages' ) ) {
		wp_send_json_error(
			array( 'message' => __( 'No tienes permiso.', 'wpboilerplate' ) ),
			403
		);
	}

	check_ajax_referer( 'wpb_contact_messages_filter', 'nonce' );

	if ( ! function_exists( 'wpb_render_contact_messages_results' ) ) {
		wp_send_json_error(
			array( 'message' => __( 'El listado no está disponible.', 'wpboilerplate' ) ),
			500
		);
	}

	$filters = wpb_contact_messages_request_filters( wp_unslash( $_POST ) );
	$paged   = wpb_contact_messages_request_paged( wp_unslash( $_POST ) );

	ob_start();
	wpb_render_contact_messages_results( $filters, $paged );
	$html = ob_get_clean();

	$total     = wpb_contact_messages_count( $filters );
	$per_page  = wpb_contact_messages_per_page();
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
add_action( 'wp_ajax_wpb_contact_messages_filter', 'wpb_contact_messages_filter_handler' );
