<?php
/**
 * Admin screen: list, filter, create, edit and delete contact messages.
 *
 * @package wpboilerplate
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Registers the Contactos admin menu under Mantenimientos.
 */
function wpb_register_contact_messages_menu() {
	$parent = function_exists( 'wpb_mantenimientos_menu_slug' )
		? wpb_mantenimientos_menu_slug()
		: 'wpb-mantenimientos';

	$hook = add_submenu_page(
		$parent,
		__( 'Contactos', 'wpboilerplate' ),
		__( 'Contactos', 'wpboilerplate' ),
		'edit_pages',
		'wpb-contact-messages',
		'wpb_render_contact_messages_page'
	);

	if ( $hook ) {
		add_action( "admin_print_scripts-{$hook}", 'wpb_contact_messages_enqueue_admin_assets' );
	}
}
add_action( 'admin_menu', 'wpb_register_contact_messages_menu', 100 );

/**
 * Enqueues list-page JS for Contactos admin.
 */
function wpb_contact_messages_enqueue_admin_assets() {
	$action = isset( $_GET['action'] ) ? sanitize_key( wp_unslash( $_GET['action'] ) ) : '';
	if ( in_array( $action, array( 'add', 'edit' ), true ) ) {
		return;
	}

	$path = get_stylesheet_directory() . '/js/admin/contactMessages.js';
	if ( ! file_exists( $path ) ) {
		return;
	}

	wp_enqueue_script(
		'wpb-contact-messages-admin',
		get_stylesheet_directory_uri() . '/js/admin/contactMessages.js',
		array(),
		(string) filemtime( $path ),
		true
	);

	wp_localize_script(
		'wpb-contact-messages-admin',
		'wpbContactMessagesAdmin',
		array(
			'ajaxUrl' => admin_url( 'admin-ajax.php' ),
			'action'  => 'wpb_contact_messages_filter',
			'nonce'   => wp_create_nonce( 'wpb_contact_messages_filter' ),
		)
	);
}

/**
 * Rows per page for the Contactos list.
 *
 * @return int
 */
function wpb_contact_messages_per_page() {
	return 20;
}

/**
 * Returns the admin page base URL.
 *
 * @param array<string, mixed> $args Extra query args.
 * @return string
 */
function wpb_contact_messages_admin_url( $args = array() ) {
	return add_query_arg(
		array_merge(
			array( 'page' => 'wpb-contact-messages' ),
			$args
		),
		admin_url( 'admin.php' )
	);
}

/**
 * Reads filter args from a request bag.
 *
 * @param array<string, mixed>|null $bag Request data.
 * @return array<string, string>
 */
function wpb_contact_messages_request_filters( $bag = null ) {
	if ( null === $bag ) {
		$bag = $_GET; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
	}

	return array(
		'name'      => isset( $bag['name'] ) ? sanitize_text_field( $bag['name'] ) : '',
		'email'     => isset( $bag['email'] ) ? sanitize_text_field( $bag['email'] ) : '',
		'phone'     => isset( $bag['phone'] ) ? sanitize_text_field( $bag['phone'] ) : '',
		'date_from' => isset( $bag['date_from'] ) ? sanitize_text_field( $bag['date_from'] ) : '',
		'date_to'   => isset( $bag['date_to'] ) ? sanitize_text_field( $bag['date_to'] ) : '',
	);
}

/**
 * Reads the current list page number.
 *
 * @param array<string, mixed>|null $bag Request data.
 * @return int
 */
function wpb_contact_messages_request_paged( $bag = null ) {
	if ( null === $bag ) {
		$bag = $_GET; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
	}

	$paged = isset( $bag['paged'] ) ? (int) $bag['paged'] : 1;

	return max( 1, $paged );
}

/**
 * Builds a list URL preserving filters + page.
 *
 * @param array<string, string> $filters Filters.
 * @param int                   $paged   Page number.
 * @return string
 */
function wpb_contact_messages_list_url( $filters, $paged = 1 ) {
	$args = array_filter(
		array(
			'name'      => $filters['name'],
			'email'     => $filters['email'],
			'phone'     => $filters['phone'],
			'date_from' => $filters['date_from'],
			'date_to'   => $filters['date_to'],
			'paged'     => $paged > 1 ? $paged : null,
		),
		static function ( $value ) {
			return null !== $value && '' !== $value;
		}
	);

	return wpb_contact_messages_admin_url( $args );
}

/**
 * Handles create via admin-post.
 */
function wpb_contact_message_handle_create() {
	if ( ! current_user_can( 'edit_pages' ) ) {
		wp_die( esc_html__( 'No tienes permiso para agregar contactos.', 'wpboilerplate' ) );
	}

	check_admin_referer( 'wpb_contact_message_create' );

	$result = wpb_contact_message_insert(
		array(
			'name'    => isset( $_POST['name'] ) ? wp_unslash( $_POST['name'] ) : '',
			'email'   => isset( $_POST['email'] ) ? wp_unslash( $_POST['email'] ) : '',
			'phone'   => isset( $_POST['phone'] ) ? wp_unslash( $_POST['phone'] ) : '',
			'message' => isset( $_POST['message'] ) ? wp_unslash( $_POST['message'] ) : '',
		)
	);

	if ( is_wp_error( $result ) ) {
		wp_safe_redirect(
			wpb_contact_messages_admin_url(
				array(
					'action' => 'add',
					'error'  => $result->get_error_code(),
				)
			)
		);
		exit;
	}

	wp_safe_redirect( wpb_contact_messages_admin_url( array( 'created' => 1 ) ) );
	exit;
}
add_action( 'admin_post_wpb_contact_message_create', 'wpb_contact_message_handle_create' );

/**
 * Handles save (edit) via admin-post.
 */
function wpb_contact_message_handle_save() {
	if ( ! current_user_can( 'edit_pages' ) ) {
		wp_die( esc_html__( 'No tienes permiso para editar contactos.', 'wpboilerplate' ) );
	}

	check_admin_referer( 'wpb_contact_message_save' );

	$id = isset( $_POST['id'] ) ? (int) $_POST['id'] : 0;

	$result = wpb_contact_message_update(
		$id,
		array(
			'name'    => isset( $_POST['name'] ) ? wp_unslash( $_POST['name'] ) : '',
			'email'   => isset( $_POST['email'] ) ? wp_unslash( $_POST['email'] ) : '',
			'phone'   => isset( $_POST['phone'] ) ? wp_unslash( $_POST['phone'] ) : '',
			'message' => isset( $_POST['message'] ) ? wp_unslash( $_POST['message'] ) : '',
		)
	);

	if ( is_wp_error( $result ) ) {
		wp_safe_redirect(
			wpb_contact_messages_admin_url(
				array(
					'action' => 'edit',
					'id'     => $id,
					'error'  => $result->get_error_code(),
				)
			)
		);
		exit;
	}

	wp_safe_redirect( wpb_contact_messages_admin_url( array( 'updated' => 1 ) ) );
	exit;
}
add_action( 'admin_post_wpb_contact_message_save', 'wpb_contact_message_handle_save' );

/**
 * Handles delete via admin-post.
 */
function wpb_contact_message_handle_delete() {
	if ( ! current_user_can( 'edit_pages' ) ) {
		wp_die( esc_html__( 'No tienes permiso para eliminar contactos.', 'wpboilerplate' ) );
	}

	$id = isset( $_GET['id'] ) ? (int) $_GET['id'] : 0;
	check_admin_referer( 'wpb_contact_message_delete_' . $id );

	$result = wpb_contact_message_delete( $id );

	wp_safe_redirect(
		wpb_contact_messages_admin_url(
			array(
				'deleted' => is_wp_error( $result ) ? 0 : 1,
				'error'   => is_wp_error( $result ) ? $result->get_error_code() : null,
			)
		)
	);
	exit;
}
add_action( 'admin_post_wpb_contact_message_delete', 'wpb_contact_message_handle_delete' );

/**
 * Renders admin notices.
 */
function wpb_contact_messages_render_notices() {
	if ( isset( $_GET['created'] ) && '1' === $_GET['created'] ) {
		echo '<div class="notice notice-success is-dismissible"><p>' . esc_html__( 'Contacto agregado.', 'wpboilerplate' ) . '</p></div>';
	}
	if ( isset( $_GET['updated'] ) && '1' === $_GET['updated'] ) {
		echo '<div class="notice notice-success is-dismissible"><p>' . esc_html__( 'Registro actualizado.', 'wpboilerplate' ) . '</p></div>';
	}
	if ( isset( $_GET['deleted'] ) && '1' === $_GET['deleted'] ) {
		echo '<div class="notice notice-success is-dismissible"><p>' . esc_html__( 'Registro eliminado.', 'wpboilerplate' ) . '</p></div>';
	}
	if ( empty( $_GET['error'] ) ) {
		return;
	}

	$code     = sanitize_key( wp_unslash( $_GET['error'] ) );
	$messages = array(
		'invalid_name'    => __( 'El nombre es obligatorio.', 'wpboilerplate' ),
		'invalid_email'   => __( 'El correo no es válido.', 'wpboilerplate' ),
		'invalid_message' => __( 'El comentario es obligatorio.', 'wpboilerplate' ),
		'not_found'       => __( 'Registro no encontrado.', 'wpboilerplate' ),
		'db_error'        => __( 'No se pudo completar la operación.', 'wpboilerplate' ),
	);
	$message  = isset( $messages[ $code ] ) ? $messages[ $code ] : __( 'Ocurrió un error.', 'wpboilerplate' );
	echo '<div class="notice notice-error is-dismissible"><p>' . esc_html( $message ) . '</p></div>';
}

/**
 * Renders the create form.
 */
function wpb_render_contact_message_create() {
	?>
	<div class="wrap">
		<h1><?php esc_html_e( 'Agregar contacto', 'wpboilerplate' ); ?></h1>
		<?php wpb_contact_messages_render_notices(); ?>
		<p><a href="<?php echo esc_url( wpb_contact_messages_admin_url() ); ?>">&larr; <?php esc_html_e( 'Volver al listado', 'wpboilerplate' ); ?></a></p>
		<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" style="max-width:640px;">
			<input type="hidden" name="action" value="wpb_contact_message_create">
			<?php wp_nonce_field( 'wpb_contact_message_create' ); ?>
			<table class="form-table" role="presentation">
				<tr>
					<th scope="row"><label for="wpb-contact-name"><?php esc_html_e( 'Nombre', 'wpboilerplate' ); ?></label></th>
					<td><input class="regular-text" type="text" id="wpb-contact-name" name="name" required></td>
				</tr>
				<tr>
					<th scope="row"><label for="wpb-contact-email"><?php esc_html_e( 'Correo', 'wpboilerplate' ); ?></label></th>
					<td><input class="regular-text" type="email" id="wpb-contact-email" name="email" required></td>
				</tr>
				<tr>
					<th scope="row"><label for="wpb-contact-phone"><?php esc_html_e( 'Teléfono', 'wpboilerplate' ); ?></label></th>
					<td><input class="regular-text" type="text" id="wpb-contact-phone" name="phone"></td>
				</tr>
				<tr>
					<th scope="row"><label for="wpb-contact-message"><?php esc_html_e( 'Comentario', 'wpboilerplate' ); ?></label></th>
					<td><textarea class="large-text" rows="5" id="wpb-contact-message" name="message" required></textarea></td>
				</tr>
			</table>
			<?php submit_button( __( 'Agregar contacto', 'wpboilerplate' ) ); ?>
		</form>
	</div>
	<?php
}

/**
 * Renders the edit form.
 *
 * @param object $row Message row.
 */
function wpb_render_contact_message_edit( $row ) {
	?>
	<div class="wrap">
		<h1><?php esc_html_e( 'Editar contacto', 'wpboilerplate' ); ?></h1>
		<?php wpb_contact_messages_render_notices(); ?>
		<p><a href="<?php echo esc_url( wpb_contact_messages_admin_url() ); ?>">&larr; <?php esc_html_e( 'Volver al listado', 'wpboilerplate' ); ?></a></p>
		<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" style="max-width:640px;">
			<input type="hidden" name="action" value="wpb_contact_message_save">
			<input type="hidden" name="id" value="<?php echo esc_attr( (string) $row->id ); ?>">
			<?php wp_nonce_field( 'wpb_contact_message_save' ); ?>
			<table class="form-table" role="presentation">
				<tr>
					<th scope="row"><label for="wpb-contact-name"><?php esc_html_e( 'Nombre', 'wpboilerplate' ); ?></label></th>
					<td><input class="regular-text" type="text" id="wpb-contact-name" name="name" value="<?php echo esc_attr( $row->name ); ?>" required></td>
				</tr>
				<tr>
					<th scope="row"><label for="wpb-contact-email"><?php esc_html_e( 'Correo', 'wpboilerplate' ); ?></label></th>
					<td><input class="regular-text" type="email" id="wpb-contact-email" name="email" value="<?php echo esc_attr( $row->email ); ?>" required></td>
				</tr>
				<tr>
					<th scope="row"><label for="wpb-contact-phone"><?php esc_html_e( 'Teléfono', 'wpboilerplate' ); ?></label></th>
					<td><input class="regular-text" type="text" id="wpb-contact-phone" name="phone" value="<?php echo esc_attr( $row->phone ); ?>"></td>
				</tr>
				<tr>
					<th scope="row"><label for="wpb-contact-message"><?php esc_html_e( 'Comentario', 'wpboilerplate' ); ?></label></th>
					<td><textarea class="large-text" rows="5" id="wpb-contact-message" name="message" required><?php echo esc_textarea( $row->message ); ?></textarea></td>
				</tr>
				<tr>
					<th scope="row"><?php esc_html_e( 'Fecha', 'wpboilerplate' ); ?></th>
					<td><?php echo esc_html( $row->created_at ); ?></td>
				</tr>
			</table>
			<?php submit_button( __( 'Guardar cambios', 'wpboilerplate' ) ); ?>
		</form>
	</div>
	<?php
}

/**
 * Renders pagination.
 *
 * @param int                   $total   Total rows.
 * @param int                   $paged   Current page.
 * @param array<string, string> $filters Filters.
 */
function wpb_render_contact_messages_pagination( $total, $paged, $filters ) {
	$per_page    = wpb_contact_messages_per_page();
	$total_pages = (int) ceil( $total / $per_page );

	if ( $total_pages <= 1 ) {
		return;
	}

	$paged = min( max( 1, $paged ), $total_pages );
	$prev  = $paged - 1;
	$next  = $paged + 1;
	?>
	<div class="tablenav bottom">
		<div class="tablenav-pages">
			<span class="displaying-num">
				<?php
				printf(
					esc_html( _n( '%d elemento', '%d elementos', $total, 'wpboilerplate' ) ),
					(int) $total
				);
				?>
			</span>
			<span class="pagination-links">
				<?php if ( $paged > 1 ) : ?>
					<a class="prev-page button" href="<?php echo esc_url( wpb_contact_messages_list_url( $filters, $prev ) ); ?>" data-paged="<?php echo esc_attr( (string) $prev ); ?>">&lsaquo;</a>
				<?php else : ?>
					<span class="tablenav-pages-navspan button disabled" aria-hidden="true">&lsaquo;</span>
				<?php endif; ?>
				<span class="paging-input">
					<span class="current-page"><?php echo esc_html( (string) $paged ); ?></span>
					<?php esc_html_e( 'de', 'wpboilerplate' ); ?>
					<span class="total-pages"><?php echo esc_html( (string) $total_pages ); ?></span>
				</span>
				<?php if ( $paged < $total_pages ) : ?>
					<a class="next-page button" href="<?php echo esc_url( wpb_contact_messages_list_url( $filters, $next ) ); ?>" data-paged="<?php echo esc_attr( (string) $next ); ?>">&rsaquo;</a>
				<?php else : ?>
					<span class="tablenav-pages-navspan button disabled" aria-hidden="true">&rsaquo;</span>
				<?php endif; ?>
			</span>
			<?php if ( $total_pages > 2 ) : ?>
				<span class="pagination-links" style="margin-left:0.75rem;">
					<?php
					$start = max( 1, $paged - 2 );
					$end   = min( $total_pages, $paged + 2 );
					for ( $page = $start; $page <= $end; $page++ ) :
						if ( $page === $paged ) :
							?>
							<span class="button button-primary" aria-current="page"><?php echo esc_html( (string) $page ); ?></span>
						<?php else : ?>
							<a class="button" href="<?php echo esc_url( wpb_contact_messages_list_url( $filters, $page ) ); ?>" data-paged="<?php echo esc_attr( (string) $page ); ?>"><?php echo esc_html( (string) $page ); ?></a>
							<?php
						endif;
					endfor;
					?>
				</span>
			<?php endif; ?>
		</div>
	</div>
	<?php
}

/**
 * Renders count + table + pagination.
 *
 * @param array<string, string> $filters Filters.
 * @param int                   $paged   Page.
 */
function wpb_render_contact_messages_results( $filters, $paged = 1 ) {
	$per_page = wpb_contact_messages_per_page();
	$total    = wpb_contact_messages_count( $filters );
	$max_page = max( 1, (int) ceil( $total / $per_page ) );
	$paged    = min( max( 1, (int) $paged ), $max_page );
	$offset   = ( $paged - 1 ) * $per_page;
	$rows     = wpb_contact_messages_list( $per_page, $offset, $filters );
	?>
	<div id="wpb-contact-messages-results" data-contact-messages-results data-paged="<?php echo esc_attr( (string) $paged ); ?>">
		<p data-contact-messages-count>
			<?php
			printf(
				esc_html( _n( '%d mensaje encontrado.', '%d mensajes encontrados.', $total, 'wpboilerplate' ) ),
				(int) $total
			);
			?>
		</p>
		<table class="widefat striped">
			<thead>
				<tr>
					<th scope="col"><?php esc_html_e( 'ID', 'wpboilerplate' ); ?></th>
					<th scope="col"><?php esc_html_e( 'Nombre', 'wpboilerplate' ); ?></th>
					<th scope="col"><?php esc_html_e( 'Correo', 'wpboilerplate' ); ?></th>
					<th scope="col"><?php esc_html_e( 'Teléfono', 'wpboilerplate' ); ?></th>
					<th scope="col"><?php esc_html_e( 'Comentario', 'wpboilerplate' ); ?></th>
					<th scope="col"><?php esc_html_e( 'Fecha', 'wpboilerplate' ); ?></th>
					<th scope="col"><?php esc_html_e( 'Acciones', 'wpboilerplate' ); ?></th>
				</tr>
			</thead>
			<tbody>
				<?php if ( empty( $rows ) ) : ?>
					<tr>
						<td colspan="7"><?php esc_html_e( 'No hay registros con esos filtros.', 'wpboilerplate' ); ?></td>
					</tr>
				<?php else : ?>
					<?php foreach ( $rows as $row ) : ?>
						<?php
						$edit_url   = wpb_contact_messages_admin_url(
							array(
								'action' => 'edit',
								'id'     => (int) $row->id,
							)
						);
						$delete_url = wp_nonce_url(
							add_query_arg(
								array(
									'action' => 'wpb_contact_message_delete',
									'id'     => (int) $row->id,
								),
								admin_url( 'admin-post.php' )
							),
							'wpb_contact_message_delete_' . (int) $row->id
						);
						$excerpt = wp_html_excerpt( $row->message, 80, '…' );
						?>
						<tr>
							<td><?php echo esc_html( (string) $row->id ); ?></td>
							<td><?php echo esc_html( $row->name ); ?></td>
							<td><?php echo esc_html( $row->email ); ?></td>
							<td><?php echo esc_html( $row->phone ); ?></td>
							<td><?php echo esc_html( $excerpt ); ?></td>
							<td><?php echo esc_html( $row->created_at ); ?></td>
							<td>
								<a href="<?php echo esc_url( $edit_url ); ?>"><?php esc_html_e( 'Editar', 'wpboilerplate' ); ?></a>
								|
								<a href="<?php echo esc_url( $delete_url ); ?>" onclick="return confirm('<?php echo esc_js( __( '¿Eliminar este mensaje?', 'wpboilerplate' ) ); ?>');" style="color:#b32d2e;"><?php esc_html_e( 'Eliminar', 'wpboilerplate' ); ?></a>
							</td>
						</tr>
					<?php endforeach; ?>
				<?php endif; ?>
			</tbody>
		</table>
		<?php wpb_render_contact_messages_pagination( $total, $paged, $filters ); ?>
	</div>
	<?php
}

/**
 * Renders the Contactos admin page.
 */
function wpb_render_contact_messages_page() {
	if ( ! current_user_can( 'edit_pages' ) ) {
		return;
	}

	if ( ! function_exists( 'wpb_contact_messages_list' ) ) {
		echo '<div class="wrap"><p>' . esc_html__( 'El sistema de contactos no está disponible.', 'wpboilerplate' ) . '</p></div>';
		return;
	}

	$action = isset( $_GET['action'] ) ? sanitize_key( wp_unslash( $_GET['action'] ) ) : 'list';
	$id     = isset( $_GET['id'] ) ? (int) $_GET['id'] : 0;

	if ( 'add' === $action ) {
		wpb_render_contact_message_create();
		return;
	}

	if ( 'edit' === $action ) {
		$row = wpb_contact_message_get( $id );
		if ( ! $row ) {
			echo '<div class="wrap"><div class="notice notice-error"><p>' . esc_html__( 'Registro no encontrado.', 'wpboilerplate' ) . '</p></div>';
			echo '<p><a href="' . esc_url( wpb_contact_messages_admin_url() ) . '">' . esc_html__( 'Volver al listado', 'wpboilerplate' ) . '</a></p></div>';
			return;
		}
		wpb_render_contact_message_edit( $row );
		return;
	}

	$filters = wpb_contact_messages_request_filters();
	$paged   = wpb_contact_messages_request_paged();
	$add_url = wpb_contact_messages_admin_url( array( 'action' => 'add' ) );
	?>
	<div class="wrap">
		<h1 class="wp-heading-inline"><?php esc_html_e( 'Contactos', 'wpboilerplate' ); ?></h1>
		<a href="<?php echo esc_url( $add_url ); ?>" class="page-title-action"><?php esc_html_e( 'Agregar contacto', 'wpboilerplate' ); ?></a>
		<hr class="wp-header-end">
		<?php wpb_contact_messages_render_notices(); ?>

		<form method="get" action="<?php echo esc_url( admin_url( 'admin.php' ) ); ?>" style="margin:1.25rem 0;" data-contact-messages-filters>
			<input type="hidden" name="page" value="wpb-contact-messages">
			<input type="hidden" name="paged" value="1">
			<div style="display:flex;flex-wrap:wrap;gap:0.75rem;align-items:flex-end;">
				<p style="margin:0;">
					<label for="wpb-filter-name"><?php esc_html_e( 'Nombre', 'wpboilerplate' ); ?></label><br>
					<input type="search" id="wpb-filter-name" name="name" value="<?php echo esc_attr( $filters['name'] ); ?>" autocomplete="off">
				</p>
				<p style="margin:0;">
					<label for="wpb-filter-email"><?php esc_html_e( 'Correo', 'wpboilerplate' ); ?></label><br>
					<input type="search" id="wpb-filter-email" name="email" value="<?php echo esc_attr( $filters['email'] ); ?>" autocomplete="off">
				</p>
				<p style="margin:0;">
					<label for="wpb-filter-phone"><?php esc_html_e( 'Teléfono', 'wpboilerplate' ); ?></label><br>
					<input type="search" id="wpb-filter-phone" name="phone" value="<?php echo esc_attr( $filters['phone'] ); ?>" autocomplete="off">
				</p>
				<p style="margin:0;">
					<label for="wpb-filter-date-from"><?php esc_html_e( 'Desde', 'wpboilerplate' ); ?></label><br>
					<input type="date" id="wpb-filter-date-from" name="date_from" value="<?php echo esc_attr( $filters['date_from'] ); ?>">
				</p>
				<p style="margin:0;">
					<label for="wpb-filter-date-to"><?php esc_html_e( 'Hasta', 'wpboilerplate' ); ?></label><br>
					<input type="date" id="wpb-filter-date-to" name="date_to" value="<?php echo esc_attr( $filters['date_to'] ); ?>">
				</p>
				<p style="margin:0;">
					<?php submit_button( __( 'Filtrar', 'wpboilerplate' ), 'secondary', '', false ); ?>
					<a class="button" href="<?php echo esc_url( wpb_contact_messages_admin_url() ); ?>"><?php esc_html_e( 'Limpiar', 'wpboilerplate' ); ?></a>
				</p>
			</div>
		</form>

		<?php wpb_render_contact_messages_results( $filters, $paged ); ?>
	</div>
	<?php
}
