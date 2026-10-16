<?php
/**
 * Admin screen: list, filter, create, edit and delete community subscribers.
 *
 * @package wpboilerplate
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Registers the Comunidad admin menu.
 */
function wpb_register_community_subscribers_menu() {
	$hook = add_menu_page(
		__( 'Comunidad', 'wpboilerplate' ),
		__( 'Comunidad', 'wpboilerplate' ),
		'edit_pages',
		'wpb-community-subscribers',
		'wpb_render_community_subscribers_page',
		'dashicons-email-alt',
		58
	);

	add_action( "admin_print_scripts-{$hook}", 'wpb_community_subscribers_enqueue_admin_assets' );
}
add_action( 'admin_menu', 'wpb_register_community_subscribers_menu' );

/**
 * Enqueues list-page JS (live email filter + AJAX pagination) for Comunidad admin.
 */
function wpb_community_subscribers_enqueue_admin_assets() {
	$action = isset( $_GET['action'] ) ? sanitize_key( wp_unslash( $_GET['action'] ) ) : '';
	if ( in_array( $action, array( 'add', 'edit' ), true ) ) {
		return;
	}

	$path = get_stylesheet_directory() . '/js/admin/communitySubscribers.js';
	if ( ! file_exists( $path ) ) {
		return;
	}

	wp_enqueue_script(
		'wpb-community-subscribers-admin',
		get_stylesheet_directory_uri() . '/js/admin/communitySubscribers.js',
		array(),
		(string) filemtime( $path ),
		true
	);

	wp_localize_script(
		'wpb-community-subscribers-admin',
		'wpbCommunitySubscribersAdmin',
		array(
			'ajaxUrl' => admin_url( 'admin-ajax.php' ),
			'action'  => 'wpb_community_subscribers_filter',
			'nonce'   => wp_create_nonce( 'wpb_community_subscribers_filter' ),
		)
	);
}

/**
 * Rows per page for the Comunidad list.
 *
 * @return int
 */
function wpb_community_subscribers_per_page() {
	return 20;
}

/**
 * Returns the admin page base URL.
 *
 * @param array<string, mixed> $args Extra query args.
 * @return string
 */
function wpb_community_subscribers_admin_url( $args = array() ) {
	return add_query_arg(
		array_merge(
			array( 'page' => 'wpb-community-subscribers' ),
			$args
		),
		admin_url( 'admin.php' )
	);
}

/**
 * Reads filter args from a request bag (GET by default).
 *
 * @param array<string, mixed>|null $bag Request data.
 * @return array<string, string>
 */
function wpb_community_subscribers_request_filters( $bag = null ) {
	if ( null === $bag ) {
		$bag = $_GET; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
	}

	return array(
		'email'     => isset( $bag['email'] ) ? sanitize_text_field( $bag['email'] ) : '',
		'status'    => isset( $bag['status'] ) ? sanitize_key( $bag['status'] ) : '',
		'source'    => isset( $bag['source'] ) ? sanitize_key( $bag['source'] ) : '',
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
function wpb_community_subscribers_request_paged( $bag = null ) {
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
function wpb_community_subscribers_list_url( $filters, $paged = 1 ) {
	$args = array_filter(
		array(
			'email'     => $filters['email'],
			'status'    => $filters['status'],
			'source'    => $filters['source'],
			'date_from' => $filters['date_from'],
			'date_to'   => $filters['date_to'],
			'paged'     => $paged > 1 ? $paged : null,
		),
		static function ( $value ) {
			return null !== $value && '' !== $value;
		}
	);

	return wpb_community_subscribers_admin_url( $args );
}

/**
 * Handles save (edit) via admin-post.
 */
function wpb_community_subscriber_handle_save() {
	if ( ! current_user_can( 'edit_pages' ) ) {
		wp_die( esc_html__( 'No tienes permiso para editar suscriptores.', 'wpboilerplate' ) );
	}

	check_admin_referer( 'wpb_community_subscriber_save' );

	$id     = isset( $_POST['id'] ) ? (int) $_POST['id'] : 0;
	$email  = isset( $_POST['email'] ) ? sanitize_email( wp_unslash( $_POST['email'] ) ) : '';
	$status = isset( $_POST['status'] ) ? sanitize_key( wp_unslash( $_POST['status'] ) ) : '';

	$result = wpb_community_subscriber_update(
		$id,
		array(
			'email'  => $email,
			'status' => $status,
		)
	);

	if ( is_wp_error( $result ) ) {
		wp_safe_redirect(
			wpb_community_subscribers_admin_url(
				array(
					'action' => 'edit',
					'id'     => $id,
					'error'  => $result->get_error_code(),
				)
			)
		);
		exit;
	}

	wp_safe_redirect(
		wpb_community_subscribers_admin_url(
			array(
				'updated' => 1,
			)
		)
	);
	exit;
}
add_action( 'admin_post_wpb_community_subscriber_save', 'wpb_community_subscriber_handle_save' );

/**
 * Handles create (manual alta) via admin-post.
 */
function wpb_community_subscriber_handle_create() {
	if ( ! current_user_can( 'edit_pages' ) ) {
		wp_die( esc_html__( 'No tienes permiso para agregar suscriptores.', 'wpboilerplate' ) );
	}

	check_admin_referer( 'wpb_community_subscriber_create' );

	$email  = isset( $_POST['email'] ) ? sanitize_email( wp_unslash( $_POST['email'] ) ) : '';
	$source = isset( $_POST['source'] ) ? sanitize_key( wp_unslash( $_POST['source'] ) ) : 'admin';
	$status = isset( $_POST['status'] ) ? sanitize_key( wp_unslash( $_POST['status'] ) ) : 'active';

	if ( ! $source ) {
		$source = 'admin';
	}

	$result = wpb_community_subscriber_insert(
		$email,
		array(
			'source' => $source,
			'status' => $status,
		)
	);

	if ( is_wp_error( $result ) ) {
		wp_safe_redirect(
			wpb_community_subscribers_admin_url(
				array(
					'action' => 'add',
					'error'  => $result->get_error_code(),
					'email'  => $email,
					'source' => $source,
					'status' => $status,
				)
			)
		);
		exit;
	}

	wp_safe_redirect(
		wpb_community_subscribers_admin_url(
			array(
				'created' => 1,
			)
		)
	);
	exit;
}
add_action( 'admin_post_wpb_community_subscriber_create', 'wpb_community_subscriber_handle_create' );

/**
 * Handles delete via admin-post.
 */
function wpb_community_subscriber_handle_delete() {
	if ( ! current_user_can( 'edit_pages' ) ) {
		wp_die( esc_html__( 'No tienes permiso para eliminar suscriptores.', 'wpboilerplate' ) );
	}

	$id = isset( $_GET['id'] ) ? (int) $_GET['id'] : 0;
	check_admin_referer( 'wpb_community_subscriber_delete_' . $id );

	$result = wpb_community_subscriber_delete( $id );

	wp_safe_redirect(
		wpb_community_subscribers_admin_url(
			array(
				'deleted' => is_wp_error( $result ) ? 0 : 1,
				'error'   => is_wp_error( $result ) ? $result->get_error_code() : null,
			)
		)
	);
	exit;
}
add_action( 'admin_post_wpb_community_subscriber_delete', 'wpb_community_subscriber_handle_delete' );

/**
 * Renders admin notices for this screen.
 */
function wpb_community_subscribers_render_notices() {
	if ( isset( $_GET['created'] ) && '1' === $_GET['created'] ) {
		echo '<div class="notice notice-success is-dismissible"><p>' . esc_html__( 'Correo agregado.', 'wpboilerplate' ) . '</p></div>';
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
		'invalid_email'   => __( 'El correo no es válido.', 'wpboilerplate' ),
		'invalid_status'  => __( 'El estado no es válido.', 'wpboilerplate' ),
		'duplicate_email' => __( 'Este correo ya está registrado.', 'wpboilerplate' ),
		'not_found'       => __( 'Registro no encontrado.', 'wpboilerplate' ),
		'db_error'        => __( 'No se pudo completar la operación.', 'wpboilerplate' ),
	);

	$message = isset( $messages[ $code ] ) ? $messages[ $code ] : __( 'Ocurrió un error.', 'wpboilerplate' );
	echo '<div class="notice notice-error is-dismissible"><p>' . esc_html( $message ) . '</p></div>';
}

/**
 * Renders the create form for a new subscriber.
 */
function wpb_render_community_subscriber_create() {
	$statuses       = wpb_community_subscriber_statuses();
	$sources        = wpb_community_subscriber_sources();
	$email          = isset( $_GET['email'] ) ? sanitize_email( wp_unslash( $_GET['email'] ) ) : '';
	$source         = isset( $_GET['source'] ) ? sanitize_key( wp_unslash( $_GET['source'] ) ) : 'admin';
	$status         = isset( $_GET['status'] ) ? sanitize_key( wp_unslash( $_GET['status'] ) ) : 'active';
	$source_choices = array_values( array_unique( array_merge( array( 'admin', 'community-split' ), $sources ) ) );

	if ( ! array_key_exists( $status, $statuses ) ) {
		$status = 'active';
	}
	?>
	<div class="wrap">
		<h1><?php esc_html_e( 'Agregar correo', 'wpboilerplate' ); ?></h1>
		<?php wpb_community_subscribers_render_notices(); ?>
		<p>
			<a href="<?php echo esc_url( wpb_community_subscribers_admin_url() ); ?>">
				&larr; <?php esc_html_e( 'Volver al listado', 'wpboilerplate' ); ?>
			</a>
		</p>
		<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" style="max-width: 520px;">
			<input type="hidden" name="action" value="wpb_community_subscriber_create">
			<?php wp_nonce_field( 'wpb_community_subscriber_create' ); ?>

			<table class="form-table" role="presentation">
				<tr>
					<th scope="row"><label for="wpb-community-email"><?php esc_html_e( 'Correo', 'wpboilerplate' ); ?></label></th>
					<td>
						<input
							class="regular-text"
							type="email"
							id="wpb-community-email"
							name="email"
							value="<?php echo esc_attr( $email ); ?>"
							required
						>
					</td>
				</tr>
				<tr>
					<th scope="row"><label for="wpb-community-source"><?php esc_html_e( 'Origen', 'wpboilerplate' ); ?></label></th>
					<td>
						<input
							class="regular-text"
							type="text"
							id="wpb-community-source"
							name="source"
							value="<?php echo esc_attr( $source ? $source : 'admin' ); ?>"
							list="wpb-community-source-list"
							required
						>
						<datalist id="wpb-community-source-list">
							<?php foreach ( $source_choices as $choice ) : ?>
								<option value="<?php echo esc_attr( $choice ); ?>"></option>
							<?php endforeach; ?>
						</datalist>
						<p class="description"><?php esc_html_e( 'Ejemplo: admin, community-split.', 'wpboilerplate' ); ?></p>
					</td>
				</tr>
				<tr>
					<th scope="row"><label for="wpb-community-status"><?php esc_html_e( 'Estado', 'wpboilerplate' ); ?></label></th>
					<td>
						<select id="wpb-community-status" name="status">
							<?php foreach ( $statuses as $value => $label ) : ?>
								<option value="<?php echo esc_attr( $value ); ?>" <?php selected( $status, $value ); ?>>
									<?php echo esc_html( $label ); ?>
								</option>
							<?php endforeach; ?>
						</select>
					</td>
				</tr>
			</table>

			<?php submit_button( __( 'Agregar correo', 'wpboilerplate' ) ); ?>
		</form>
	</div>
	<?php
}

/**
 * Renders the edit form for one subscriber.
 *
 * @param object $row Subscriber row.
 */
function wpb_render_community_subscriber_edit( $row ) {
	$statuses = wpb_community_subscriber_statuses();
	?>
	<div class="wrap">
		<h1><?php esc_html_e( 'Editar suscriptor', 'wpboilerplate' ); ?></h1>
		<?php wpb_community_subscribers_render_notices(); ?>
		<p>
			<a href="<?php echo esc_url( wpb_community_subscribers_admin_url() ); ?>">
				&larr; <?php esc_html_e( 'Volver al listado', 'wpboilerplate' ); ?>
			</a>
		</p>
		<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" style="max-width: 520px;">
			<input type="hidden" name="action" value="wpb_community_subscriber_save">
			<input type="hidden" name="id" value="<?php echo esc_attr( (string) $row->id ); ?>">
			<?php wp_nonce_field( 'wpb_community_subscriber_save' ); ?>

			<table class="form-table" role="presentation">
				<tr>
					<th scope="row"><label for="wpb-community-email"><?php esc_html_e( 'Correo', 'wpboilerplate' ); ?></label></th>
					<td>
						<input
							class="regular-text"
							type="email"
							id="wpb-community-email"
							name="email"
							value="<?php echo esc_attr( $row->email ); ?>"
							required
						>
					</td>
				</tr>
				<tr>
					<th scope="row"><label for="wpb-community-status"><?php esc_html_e( 'Estado', 'wpboilerplate' ); ?></label></th>
					<td>
						<select id="wpb-community-status" name="status">
							<?php foreach ( $statuses as $value => $label ) : ?>
								<option value="<?php echo esc_attr( $value ); ?>" <?php selected( $row->status, $value ); ?>>
									<?php echo esc_html( $label ); ?>
								</option>
							<?php endforeach; ?>
						</select>
					</td>
				</tr>
				<tr>
					<th scope="row"><?php esc_html_e( 'Origen', 'wpboilerplate' ); ?></th>
					<td><code><?php echo esc_html( $row->source ); ?></code></td>
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
 * Renders pagination for the subscribers list.
 *
 * @param int                   $total   Total matching rows.
 * @param int                   $paged   Current page.
 * @param array<string, string> $filters Active filters.
 */
function wpb_render_community_subscribers_pagination( $total, $paged, $filters ) {
	$per_page    = wpb_community_subscribers_per_page();
	$total_pages = (int) ceil( $total / $per_page );

	if ( $total_pages <= 1 ) {
		return;
	}

	$paged = min( max( 1, $paged ), $total_pages );
	?>
	<div class="tablenav bottom">
		<div class="tablenav-pages">
			<span class="displaying-num">
				<?php
				printf(
					/* translators: %d: subscriber count */
					esc_html( _n( '%d elemento', '%d elementos', $total, 'wpboilerplate' ) ),
					(int) $total
				);
				?>
			</span>
			<span class="pagination-links">
				<?php
				$prev = $paged - 1;
				$next = $paged + 1;
				?>
				<?php if ( $paged > 1 ) : ?>
					<a class="prev-page button" href="<?php echo esc_url( wpb_community_subscribers_list_url( $filters, $prev ) ); ?>" data-paged="<?php echo esc_attr( (string) $prev ); ?>">&lsaquo;</a>
				<?php else : ?>
					<span class="tablenav-pages-navspan button disabled" aria-hidden="true">&lsaquo;</span>
				<?php endif; ?>

				<span class="paging-input">
					<label for="wpb-community-current-page" class="screen-reader-text"><?php esc_html_e( 'Página actual', 'wpboilerplate' ); ?></label>
					<span class="current-page" id="wpb-community-current-page"><?php echo esc_html( (string) $paged ); ?></span>
					<?php esc_html_e( 'de', 'wpboilerplate' ); ?>
					<span class="total-pages"><?php echo esc_html( (string) $total_pages ); ?></span>
				</span>

				<?php if ( $paged < $total_pages ) : ?>
					<a class="next-page button" href="<?php echo esc_url( wpb_community_subscribers_list_url( $filters, $next ) ); ?>" data-paged="<?php echo esc_attr( (string) $next ); ?>">&rsaquo;</a>
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
							<a class="button" href="<?php echo esc_url( wpb_community_subscribers_list_url( $filters, $page ) ); ?>" data-paged="<?php echo esc_attr( (string) $page ); ?>"><?php echo esc_html( (string) $page ); ?></a>
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
 * Renders count + table + pagination (used by full page and AJAX).
 *
 * @param array<string, string> $filters Filters.
 * @param int                   $paged   Page number.
 */
function wpb_render_community_subscribers_results( $filters, $paged = 1 ) {
	$per_page = wpb_community_subscribers_per_page();
	$total    = wpb_community_subscribers_count( $filters );
	$max_page = max( 1, (int) ceil( $total / $per_page ) );
	$paged    = min( max( 1, (int) $paged ), $max_page );
	$offset   = ( $paged - 1 ) * $per_page;
	$rows     = wpb_community_subscribers_list( $per_page, $offset, $filters );
	$statuses = wpb_community_subscriber_statuses();
	?>
	<div id="wpb-community-subscribers-results" data-community-subscribers-results data-paged="<?php echo esc_attr( (string) $paged ); ?>">
		<p data-community-subscribers-count>
			<?php
			printf(
				/* translators: %d: subscriber count */
				esc_html( _n( '%d correo encontrado.', '%d correos encontrados.', $total, 'wpboilerplate' ) ),
				(int) $total
			);
			?>
		</p>

		<table class="widefat striped">
			<thead>
				<tr>
					<th scope="col"><?php esc_html_e( 'ID', 'wpboilerplate' ); ?></th>
					<th scope="col"><?php esc_html_e( 'Correo', 'wpboilerplate' ); ?></th>
					<th scope="col"><?php esc_html_e( 'Origen', 'wpboilerplate' ); ?></th>
					<th scope="col"><?php esc_html_e( 'Estado', 'wpboilerplate' ); ?></th>
					<th scope="col"><?php esc_html_e( 'Fecha', 'wpboilerplate' ); ?></th>
					<th scope="col"><?php esc_html_e( 'Acciones', 'wpboilerplate' ); ?></th>
				</tr>
			</thead>
			<tbody>
				<?php if ( empty( $rows ) ) : ?>
					<tr>
						<td colspan="6"><?php esc_html_e( 'No hay registros con esos filtros.', 'wpboilerplate' ); ?></td>
					</tr>
				<?php else : ?>
					<?php foreach ( $rows as $row ) : ?>
						<?php
						$edit_url   = wpb_community_subscribers_admin_url(
							array(
								'action' => 'edit',
								'id'     => (int) $row->id,
							)
						);
						$delete_url = wp_nonce_url(
							add_query_arg(
								array(
									'action' => 'wpb_community_subscriber_delete',
									'id'     => (int) $row->id,
								),
								admin_url( 'admin-post.php' )
							),
							'wpb_community_subscriber_delete_' . (int) $row->id
						);
						$status_label = isset( $statuses[ $row->status ] ) ? $statuses[ $row->status ] : $row->status;
						?>
						<tr>
							<td><?php echo esc_html( (string) $row->id ); ?></td>
							<td><?php echo esc_html( $row->email ); ?></td>
							<td><?php echo esc_html( $row->source ); ?></td>
							<td><?php echo esc_html( $status_label ); ?></td>
							<td><?php echo esc_html( $row->created_at ); ?></td>
							<td>
								<a href="<?php echo esc_url( $edit_url ); ?>"><?php esc_html_e( 'Editar', 'wpboilerplate' ); ?></a>
								|
								<a
									href="<?php echo esc_url( $delete_url ); ?>"
									onclick="return confirm('<?php echo esc_js( __( '¿Eliminar este correo?', 'wpboilerplate' ) ); ?>');"
									style="color:#b32d2e;"
								><?php esc_html_e( 'Eliminar', 'wpboilerplate' ); ?></a>
							</td>
						</tr>
					<?php endforeach; ?>
				<?php endif; ?>
			</tbody>
		</table>

		<?php wpb_render_community_subscribers_pagination( $total, $paged, $filters ); ?>
	</div>
	<?php
}

/**
 * Renders the subscribers list page (filters + table + actions).
 */
function wpb_render_community_subscribers_page() {
	if ( ! current_user_can( 'edit_pages' ) ) {
		return;
	}

	if ( ! function_exists( 'wpb_community_subscribers_list' ) ) {
		echo '<div class="wrap"><p>' . esc_html__( 'El sistema de suscriptores no está disponible.', 'wpboilerplate' ) . '</p></div>';
		return;
	}

	$action = isset( $_GET['action'] ) ? sanitize_key( wp_unslash( $_GET['action'] ) ) : 'list';
	$id     = isset( $_GET['id'] ) ? (int) $_GET['id'] : 0;

	if ( 'add' === $action ) {
		wpb_render_community_subscriber_create();
		return;
	}

	if ( 'edit' === $action ) {
		$row = wpb_community_subscriber_get( $id );
		if ( ! $row ) {
			echo '<div class="wrap"><div class="notice notice-error"><p>' . esc_html__( 'Registro no encontrado.', 'wpboilerplate' ) . '</p></div>';
			echo '<p><a href="' . esc_url( wpb_community_subscribers_admin_url() ) . '">' . esc_html__( 'Volver al listado', 'wpboilerplate' ) . '</a></p></div>';
			return;
		}
		wpb_render_community_subscriber_edit( $row );
		return;
	}

	$filters  = wpb_community_subscribers_request_filters();
	$paged    = wpb_community_subscribers_request_paged();
	$statuses = wpb_community_subscriber_statuses();
	$sources  = wpb_community_subscriber_sources();
	$add_url  = wpb_community_subscribers_admin_url( array( 'action' => 'add' ) );
	?>
	<div class="wrap">
		<h1 class="wp-heading-inline"><?php esc_html_e( 'Comunidad — correos registrados', 'wpboilerplate' ); ?></h1>
		<a href="<?php echo esc_url( $add_url ); ?>" class="page-title-action"><?php esc_html_e( 'Agregar correo', 'wpboilerplate' ); ?></a>
		<hr class="wp-header-end">
		<?php wpb_community_subscribers_render_notices(); ?>

		<form method="get" action="<?php echo esc_url( admin_url( 'admin.php' ) ); ?>" style="margin: 1.25rem 0;" data-community-subscribers-filters>
			<input type="hidden" name="page" value="wpb-community-subscribers">
			<input type="hidden" name="paged" value="1">
			<div style="display:flex; flex-wrap:wrap; gap:0.75rem; align-items:flex-end;">
				<p style="margin:0;">
					<label for="wpb-filter-email"><?php esc_html_e( 'Correo', 'wpboilerplate' ); ?></label><br>
					<input
						type="search"
						id="wpb-filter-email"
						name="email"
						value="<?php echo esc_attr( $filters['email'] ); ?>"
						placeholder="<?php esc_attr_e( 'Buscar mientras escribes…', 'wpboilerplate' ); ?>"
						autocomplete="off"
					>
				</p>
				<p style="margin:0;">
					<label for="wpb-filter-status"><?php esc_html_e( 'Estado', 'wpboilerplate' ); ?></label><br>
					<select id="wpb-filter-status" name="status">
						<option value=""><?php esc_html_e( 'Todos', 'wpboilerplate' ); ?></option>
						<?php foreach ( $statuses as $value => $label ) : ?>
							<option value="<?php echo esc_attr( $value ); ?>" <?php selected( $filters['status'], $value ); ?>>
								<?php echo esc_html( $label ); ?>
							</option>
						<?php endforeach; ?>
					</select>
				</p>
				<p style="margin:0;">
					<label for="wpb-filter-source"><?php esc_html_e( 'Origen', 'wpboilerplate' ); ?></label><br>
					<select id="wpb-filter-source" name="source">
						<option value=""><?php esc_html_e( 'Todos', 'wpboilerplate' ); ?></option>
						<?php foreach ( $sources as $source ) : ?>
							<option value="<?php echo esc_attr( $source ); ?>" <?php selected( $filters['source'], $source ); ?>>
								<?php echo esc_html( $source ); ?>
							</option>
						<?php endforeach; ?>
					</select>
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
					<a class="button" href="<?php echo esc_url( wpb_community_subscribers_admin_url() ); ?>"><?php esc_html_e( 'Limpiar', 'wpboilerplate' ); ?></a>
				</p>
			</div>
		</form>

		<?php wpb_render_community_subscribers_results( $filters, $paged ); ?>
	</div>
	<?php
}
