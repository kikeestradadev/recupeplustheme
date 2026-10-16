<?php
/**
 * Contact messages: DB table + helpers.
 *
 * @package wpboilerplate
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Returns the contact messages table name.
 *
 * @return string
 */
function wpb_contact_messages_table() {
	global $wpdb;

	return $wpdb->prefix . 'wpb_contact_messages';
}

/**
 * Creates or updates the contact messages table.
 */
function wpb_contact_messages_install() {
	global $wpdb;

	$table           = wpb_contact_messages_table();
	$charset_collate = $wpdb->get_charset_collate();
	$version         = '1.0.0';
	$installed       = get_option( 'wpb_contact_messages_db_version' );

	if ( $installed === $version ) {
		return;
	}

	require_once ABSPATH . 'wp-admin/includes/upgrade.php';

	$sql = "CREATE TABLE {$table} (
		id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
		name varchar(191) NOT NULL,
		email varchar(191) NOT NULL,
		phone varchar(50) NOT NULL DEFAULT '',
		message text NOT NULL,
		ip varchar(45) DEFAULT NULL,
		user_agent varchar(255) DEFAULT NULL,
		created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
		PRIMARY KEY  (id),
		KEY email (email),
		KEY name (name),
		KEY phone (phone),
		KEY created_at (created_at)
	) {$charset_collate};";

	dbDelta( $sql );
	update_option( 'wpb_contact_messages_db_version', $version );
}
add_action( 'after_setup_theme', 'wpb_contact_messages_install' );

/**
 * Fetches one contact message by ID.
 *
 * @param int $id Message ID.
 * @return object|null
 */
function wpb_contact_message_get( $id ) {
	global $wpdb;

	$id = (int) $id;
	if ( $id <= 0 ) {
		return null;
	}

	$table = wpb_contact_messages_table();

	return $wpdb->get_row(
		$wpdb->prepare(
			"SELECT id, name, email, phone, message, ip, user_agent, created_at
			FROM {$table}
			WHERE id = %d",
			$id
		)
	);
}

/**
 * Inserts a contact message.
 *
 * @param array<string, mixed> $data Message data.
 * @return int|WP_Error
 */
function wpb_contact_message_insert( $data ) {
	global $wpdb;

	$name    = isset( $data['name'] ) ? sanitize_text_field( $data['name'] ) : '';
	$email   = isset( $data['email'] ) ? strtolower( sanitize_email( $data['email'] ) ) : '';
	$phone   = isset( $data['phone'] ) ? sanitize_text_field( $data['phone'] ) : '';
	$message = isset( $data['message'] ) ? sanitize_textarea_field( $data['message'] ) : '';
	$ip      = isset( $data['ip'] ) ? sanitize_text_field( $data['ip'] ) : '';
	$user_agent = isset( $data['user_agent'] ) ? sanitize_text_field( $data['user_agent'] ) : '';

	if ( '' === $name ) {
		return new WP_Error( 'invalid_name', __( 'El nombre es obligatorio.', 'wpboilerplate' ) );
	}

	if ( ! is_email( $email ) ) {
		return new WP_Error( 'invalid_email', __( 'El correo no es válido.', 'wpboilerplate' ) );
	}

	if ( '' === $message ) {
		return new WP_Error( 'invalid_message', __( 'El comentario es obligatorio.', 'wpboilerplate' ) );
	}

	if ( strlen( $user_agent ) > 255 ) {
		$user_agent = substr( $user_agent, 0, 255 );
	}

	$inserted = $wpdb->insert(
		wpb_contact_messages_table(),
		array(
			'name'       => $name,
			'email'      => $email,
			'phone'      => $phone,
			'message'    => $message,
			'ip'         => $ip,
			'user_agent' => $user_agent,
			'created_at' => current_time( 'mysql' ),
		),
		array( '%s', '%s', '%s', '%s', '%s', '%s', '%s' )
	);

	if ( false === $inserted ) {
		return new WP_Error( 'db_error', __( 'No se pudo guardar el mensaje. Intenta de nuevo.', 'wpboilerplate' ) );
	}

	return (int) $wpdb->insert_id;
}

/**
 * Builds WHERE clause + values from admin filters.
 *
 * @param array<string, mixed> $args Filter args.
 * @return array{0:string,1:array<int, mixed>}
 */
function wpb_contact_messages_build_where( $args = array() ) {
	$where  = array( '1=1' );
	$values = array();

	$name = isset( $args['name'] ) ? sanitize_text_field( $args['name'] ) : '';
	if ( '' !== $name ) {
		$where[]  = 'name LIKE %s';
		$values[] = '%' . $GLOBALS['wpdb']->esc_like( $name ) . '%';
	}

	$email = isset( $args['email'] ) ? sanitize_text_field( $args['email'] ) : '';
	if ( '' !== $email ) {
		$where[]  = 'email LIKE %s';
		$values[] = '%' . $GLOBALS['wpdb']->esc_like( strtolower( $email ) ) . '%';
	}

	$phone = isset( $args['phone'] ) ? sanitize_text_field( $args['phone'] ) : '';
	if ( '' !== $phone ) {
		$where[]  = 'phone LIKE %s';
		$values[] = '%' . $GLOBALS['wpdb']->esc_like( $phone ) . '%';
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
 * Returns contact messages for the admin list.
 *
 * @param int                  $limit  Max rows.
 * @param int                  $offset Offset.
 * @param array<string, mixed> $args   Filters.
 * @return array<int, object>
 */
function wpb_contact_messages_list( $limit = 20, $offset = 0, $args = array() ) {
	global $wpdb;

	$table  = wpb_contact_messages_table();
	$limit  = max( 1, (int) $limit );
	$offset = max( 0, (int) $offset );

	list( $where_sql, $values ) = wpb_contact_messages_build_where( $args );

	$sql = "SELECT id, name, email, phone, message, created_at
		FROM {$table}
		WHERE {$where_sql}
		ORDER BY created_at DESC
		LIMIT %d OFFSET %d";

	$values[] = $limit;
	$values[] = $offset;

	return $wpdb->get_results( $wpdb->prepare( $sql, $values ) );
}

/**
 * Counts contact messages (optionally filtered).
 *
 * @param array<string, mixed> $args Filters.
 * @return int
 */
function wpb_contact_messages_count( $args = array() ) {
	global $wpdb;

	$table = wpb_contact_messages_table();

	list( $where_sql, $values ) = wpb_contact_messages_build_where( $args );

	$sql = "SELECT COUNT(id) FROM {$table} WHERE {$where_sql}";

	if ( empty( $values ) ) {
		return (int) $wpdb->get_var( $sql );
	}

	return (int) $wpdb->get_var( $wpdb->prepare( $sql, $values ) );
}

/**
 * Updates a contact message.
 *
 * @param int                  $id   Message ID.
 * @param array<string, mixed> $data Fields.
 * @return true|WP_Error
 */
function wpb_contact_message_update( $id, $data ) {
	global $wpdb;

	$id  = (int) $id;
	$row = wpb_contact_message_get( $id );

	if ( ! $row ) {
		return new WP_Error( 'not_found', __( 'Registro no encontrado.', 'wpboilerplate' ) );
	}

	$name    = isset( $data['name'] ) ? sanitize_text_field( $data['name'] ) : $row->name;
	$email   = isset( $data['email'] ) ? strtolower( sanitize_email( $data['email'] ) ) : $row->email;
	$phone   = isset( $data['phone'] ) ? sanitize_text_field( $data['phone'] ) : $row->phone;
	$message = isset( $data['message'] ) ? sanitize_textarea_field( $data['message'] ) : $row->message;

	if ( '' === $name ) {
		return new WP_Error( 'invalid_name', __( 'El nombre es obligatorio.', 'wpboilerplate' ) );
	}

	if ( ! is_email( $email ) ) {
		return new WP_Error( 'invalid_email', __( 'El correo no es válido.', 'wpboilerplate' ) );
	}

	if ( '' === $message ) {
		return new WP_Error( 'invalid_message', __( 'El comentario es obligatorio.', 'wpboilerplate' ) );
	}

	$updated = $wpdb->update(
		wpb_contact_messages_table(),
		array(
			'name'    => $name,
			'email'   => $email,
			'phone'   => $phone,
			'message' => $message,
		),
		array( 'id' => $id ),
		array( '%s', '%s', '%s', '%s' ),
		array( '%d' )
	);

	if ( false === $updated ) {
		return new WP_Error( 'db_error', __( 'No se pudo actualizar el registro.', 'wpboilerplate' ) );
	}

	return true;
}

/**
 * Deletes a contact message by ID.
 *
 * @param int $id Message ID.
 * @return true|WP_Error
 */
function wpb_contact_message_delete( $id ) {
	global $wpdb;

	$id = (int) $id;

	if ( $id <= 0 || ! wpb_contact_message_get( $id ) ) {
		return new WP_Error( 'not_found', __( 'Registro no encontrado.', 'wpboilerplate' ) );
	}

	$deleted = $wpdb->delete(
		wpb_contact_messages_table(),
		array( 'id' => $id ),
		array( '%d' )
	);

	if ( false === $deleted ) {
		return new WP_Error( 'db_error', __( 'No se pudo eliminar el registro.', 'wpboilerplate' ) );
	}

	return true;
}
