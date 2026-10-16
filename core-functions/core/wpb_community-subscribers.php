<?php
/**
 * Community subscribers: DB table + helpers.
 *
 * @package wpboilerplate
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Returns the subscribers table name.
 *
 * @return string
 */
function wpb_community_subscribers_table() {
	global $wpdb;

	return $wpdb->prefix . 'wpb_community_subscribers';
}

/**
 * Creates or updates the subscribers table.
 */
function wpb_community_subscribers_install() {
	global $wpdb;

	$table           = wpb_community_subscribers_table();
	$charset_collate = $wpdb->get_charset_collate();
	$version         = '1.0.0';
	$installed       = get_option( 'wpb_community_subscribers_db_version' );

	if ( $installed === $version ) {
		return;
	}

	require_once ABSPATH . 'wp-admin/includes/upgrade.php';

	$sql = "CREATE TABLE {$table} (
		id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
		email varchar(191) NOT NULL,
		source varchar(64) NOT NULL DEFAULT 'community-split',
		ip varchar(45) DEFAULT NULL,
		user_agent varchar(255) DEFAULT NULL,
		status varchar(20) NOT NULL DEFAULT 'active',
		created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
		PRIMARY KEY  (id),
		UNIQUE KEY email (email),
		KEY status (status),
		KEY created_at (created_at)
	) {$charset_collate};";

	dbDelta( $sql );
	update_option( 'wpb_community_subscribers_db_version', $version );
}
add_action( 'after_setup_theme', 'wpb_community_subscribers_install' );

/**
 * Allowed subscriber statuses.
 *
 * @return array<string, string>
 */
function wpb_community_subscriber_statuses() {
	return array(
		'active'       => __( 'Activo', 'wpboilerplate' ),
		'inactive'     => __( 'Inactivo', 'wpboilerplate' ),
		'unsubscribed' => __( 'Dado de baja', 'wpboilerplate' ),
	);
}

/**
 * Checks whether an email is already registered.
 *
 * @param string $email      Normalized email.
 * @param int    $exclude_id Optional ID to ignore (for updates).
 * @return bool
 */
function wpb_community_subscriber_exists( $email, $exclude_id = 0 ) {
	global $wpdb;

	$table      = wpb_community_subscribers_table();
	$exclude_id = (int) $exclude_id;

	if ( $exclude_id > 0 ) {
		$count = (int) $wpdb->get_var(
			$wpdb->prepare(
				"SELECT COUNT(id) FROM {$table} WHERE email = %s AND id != %d",
				$email,
				$exclude_id
			)
		);
	} else {
		$count = (int) $wpdb->get_var(
			$wpdb->prepare(
				"SELECT COUNT(id) FROM {$table} WHERE email = %s",
				$email
			)
		);
	}

	return $count > 0;
}

/**
 * Fetches one subscriber by ID.
 *
 * @param int $id Subscriber ID.
 * @return object|null
 */
function wpb_community_subscriber_get( $id ) {
	global $wpdb;

	$id = (int) $id;
	if ( $id <= 0 ) {
		return null;
	}

	$table = wpb_community_subscribers_table();

	return $wpdb->get_row(
		$wpdb->prepare(
			"SELECT id, email, source, status, created_at, ip, user_agent
			FROM {$table}
			WHERE id = %d",
			$id
		)
	);
}

/**
 * Inserts a community subscriber email.
 *
 * @param string               $email  Email address.
 * @param array<string, mixed> $extra  Optional meta (source, status, ip, user_agent).
 * @return int|WP_Error Inserted row ID or error.
 */
function wpb_community_subscriber_insert( $email, $extra = array() ) {
	global $wpdb;

	$email = sanitize_email( $email );

	if ( ! is_email( $email ) ) {
		return new WP_Error( 'invalid_email', __( 'El correo no es válido.', 'wpboilerplate' ) );
	}

	$email = strtolower( $email );

	if ( wpb_community_subscriber_exists( $email ) ) {
		return new WP_Error( 'duplicate_email', __( 'Este correo ya está registrado.', 'wpboilerplate' ) );
	}

	$source     = isset( $extra['source'] ) ? sanitize_key( $extra['source'] ) : 'community-split';
	$status     = isset( $extra['status'] ) ? sanitize_key( $extra['status'] ) : 'active';
	$ip         = isset( $extra['ip'] ) ? sanitize_text_field( $extra['ip'] ) : '';
	$user_agent = isset( $extra['user_agent'] ) ? sanitize_text_field( $extra['user_agent'] ) : '';

	if ( ! array_key_exists( $status, wpb_community_subscriber_statuses() ) ) {
		return new WP_Error( 'invalid_status', __( 'El estado no es válido.', 'wpboilerplate' ) );
	}

	if ( strlen( $user_agent ) > 255 ) {
		$user_agent = substr( $user_agent, 0, 255 );
	}

	$inserted = $wpdb->insert(
		wpb_community_subscribers_table(),
		array(
			'email'      => $email,
			'source'     => $source ? $source : 'community-split',
			'ip'         => $ip,
			'user_agent' => $user_agent,
			'status'     => $status,
			'created_at' => current_time( 'mysql' ),
		),
		array( '%s', '%s', '%s', '%s', '%s', '%s' )
	);

	if ( false === $inserted ) {
		return new WP_Error( 'db_error', __( 'No se pudo guardar el correo. Intenta de nuevo.', 'wpboilerplate' ) );
	}

	return (int) $wpdb->insert_id;
}

/**
 * Builds WHERE clause + values from admin filters.
 *
 * @param array<string, mixed> $args Filter args.
 * @return array{0:string,1:array<int, mixed>}
 */
function wpb_community_subscribers_build_where( $args = array() ) {
	$where  = array( '1=1' );
	$values = array();

	$email = isset( $args['email'] ) ? sanitize_text_field( $args['email'] ) : '';
	if ( '' !== $email ) {
		$where[]  = 'email LIKE %s';
		$values[] = '%' . $GLOBALS['wpdb']->esc_like( strtolower( $email ) ) . '%';
	}

	$status = isset( $args['status'] ) ? sanitize_key( $args['status'] ) : '';
	if ( '' !== $status && array_key_exists( $status, wpb_community_subscriber_statuses() ) ) {
		$where[]  = 'status = %s';
		$values[] = $status;
	}

	$source = isset( $args['source'] ) ? sanitize_key( $args['source'] ) : '';
	if ( '' !== $source ) {
		$where[]  = 'source = %s';
		$values[] = $source;
	}

	$date_from = isset( $args['date_from'] ) ? sanitize_text_field( $args['date_from'] ) : '';
	if ( $date_from && preg_match( '/^\d{4}-\d{2}-\d{2}$/', $date_from ) ) {
		$where[]  = 'created_at >= %s';
		$values[] = $date_from . ' 00:00:00';
	}

	$date_to = isset( $args['date_to'] ) ? sanitize_text_field( $args['date_to'] ) : '';
	if ( $date_to && preg_match( '/^\d{4}-\d{2}-\d{2}$/', $date_to ) ) {
		$where[]  = 'created_at <= %s';
		$values[] = $date_to . ' 23:59:59';
	}

	return array( implode( ' AND ', $where ), $values );
}

/**
 * Returns distinct sources present in the table.
 *
 * @return array<int, string>
 */
function wpb_community_subscriber_sources() {
	global $wpdb;

	$table = wpb_community_subscribers_table();
	$rows  = $wpdb->get_col( "SELECT DISTINCT source FROM {$table} WHERE source != '' ORDER BY source ASC" );

	return is_array( $rows ) ? array_values( array_filter( $rows ) ) : array();
}

/**
 * Returns subscribers for the admin list (optionally filtered).
 *
 * @param int                  $limit  Max rows.
 * @param int                  $offset Offset.
 * @param array<string, mixed> $args   Filters: email, status, source, date_from, date_to.
 * @return array<int, object>
 */
function wpb_community_subscribers_list( $limit = 100, $offset = 0, $args = array() ) {
	global $wpdb;

	$table  = wpb_community_subscribers_table();
	$limit  = max( 1, (int) $limit );
	$offset = max( 0, (int) $offset );

	list( $where_sql, $values ) = wpb_community_subscribers_build_where( $args );

	$sql = "SELECT id, email, source, status, created_at
		FROM {$table}
		WHERE {$where_sql}
		ORDER BY created_at DESC
		LIMIT %d OFFSET %d";

	$values[] = $limit;
	$values[] = $offset;

	return $wpdb->get_results( $wpdb->prepare( $sql, $values ) );
}

/**
 * Counts registered subscribers (optionally filtered).
 *
 * @param array<string, mixed> $args Filters.
 * @return int
 */
function wpb_community_subscribers_count( $args = array() ) {
	global $wpdb;

	$table = wpb_community_subscribers_table();

	list( $where_sql, $values ) = wpb_community_subscribers_build_where( $args );

	$sql = "SELECT COUNT(id) FROM {$table} WHERE {$where_sql}";

	if ( empty( $values ) ) {
		return (int) $wpdb->get_var( $sql );
	}

	return (int) $wpdb->get_var( $wpdb->prepare( $sql, $values ) );
}

/**
 * Updates a subscriber (email + status).
 *
 * @param int                  $id   Subscriber ID.
 * @param array<string, mixed> $data Fields to update.
 * @return true|WP_Error
 */
function wpb_community_subscriber_update( $id, $data ) {
	global $wpdb;

	$id  = (int) $id;
	$row = wpb_community_subscriber_get( $id );

	if ( ! $row ) {
		return new WP_Error( 'not_found', __( 'Registro no encontrado.', 'wpboilerplate' ) );
	}

	$email  = isset( $data['email'] ) ? strtolower( sanitize_email( $data['email'] ) ) : $row->email;
	$status = isset( $data['status'] ) ? sanitize_key( $data['status'] ) : $row->status;

	if ( ! is_email( $email ) ) {
		return new WP_Error( 'invalid_email', __( 'El correo no es válido.', 'wpboilerplate' ) );
	}

	if ( ! array_key_exists( $status, wpb_community_subscriber_statuses() ) ) {
		return new WP_Error( 'invalid_status', __( 'El estado no es válido.', 'wpboilerplate' ) );
	}

	if ( wpb_community_subscriber_exists( $email, $id ) ) {
		return new WP_Error( 'duplicate_email', __( 'Este correo ya está registrado en otro registro.', 'wpboilerplate' ) );
	}

	$updated = $wpdb->update(
		wpb_community_subscribers_table(),
		array(
			'email'  => $email,
			'status' => $status,
		),
		array( 'id' => $id ),
		array( '%s', '%s' ),
		array( '%d' )
	);

	if ( false === $updated ) {
		return new WP_Error( 'db_error', __( 'No se pudo actualizar el registro.', 'wpboilerplate' ) );
	}

	return true;
}

/**
 * Deletes a subscriber by ID.
 *
 * @param int $id Subscriber ID.
 * @return true|WP_Error
 */
function wpb_community_subscriber_delete( $id ) {
	global $wpdb;

	$id = (int) $id;

	if ( $id <= 0 || ! wpb_community_subscriber_get( $id ) ) {
		return new WP_Error( 'not_found', __( 'Registro no encontrado.', 'wpboilerplate' ) );
	}

	$deleted = $wpdb->delete(
		wpb_community_subscribers_table(),
		array( 'id' => $id ),
		array( '%d' )
	);

	if ( false === $deleted ) {
		return new WP_Error( 'db_error', __( 'No se pudo eliminar el registro.', 'wpboilerplate' ) );
	}

	return true;
}
