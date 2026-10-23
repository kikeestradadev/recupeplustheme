<?php
/**
 * Account drawer. Same shell as the cart drawer: backdrop, side panel, close control.
 *
 * @package wpboilerplate
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! function_exists( 'wc_get_account_endpoint_url' ) ) {
	return;
}

$logged_in = is_user_logged_in();
$account_errors = array();

if (
	! $logged_in
	&& function_exists( 'wc_get_notices' )
	&& ! is_cart()
	&& ! is_checkout()
	&& ! is_account_page()
) {
	$account_errors = wc_get_notices( 'error' );
	if ( $account_errors && function_exists( 'wc_clear_notices' ) ) {
		wc_clear_notices( 'error' );
	}
}

$panel_open = ! empty( $account_errors );
?>
<aside
	class="account-drawer<?php echo $panel_open ? ' account-drawer--open' : ''; ?>"
	id="account-drawer"
	data-account-drawer
	data-account-state="<?php echo $logged_in ? 'logged-in' : 'logged-out'; ?>"
	aria-hidden="<?php echo $panel_open ? 'false' : 'true'; ?>"
>
	<div class="account-drawer__backdrop" data-account-drawer-close tabindex="-1" aria-hidden="true"></div>
	<div
		class="account-drawer__panel"
		role="dialog"
		aria-modal="true"
		aria-labelledby="account-drawer-title"
		data-account-drawer-panel
	>
		<header class="account-drawer__header">
			<h2 class="account-drawer__title" id="account-drawer-title"><?php echo esc_html( $logged_in ? 'Mi cuenta' : 'Ingresar' ); ?></h2>
			<button
				class="account-drawer__close"
				type="button"
				data-account-drawer-close
				aria-label="Cerrar cuenta"
			>
				<svg class="account-drawer__close-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" aria-hidden="true">
					<path d="M6 6l12 12M18 6L6 18" stroke="currentColor" stroke-width="1.75" stroke-linecap="round"></path>
				</svg>
			</button>
		</header>
		<div class="account-drawer__body">
			<?php if ( $logged_in ) : ?>
				<?php
				$user  = wp_get_current_user();
				$links = array(
					'orders'          => 'Pedidos',
					'edit-address'    => 'Direcciones',
					'edit-account'    => 'Detalles de la cuenta',
					'customer-logout' => 'Salir',
				);
				?>
				<p class="account-drawer__hello">
					<?php
					echo esc_html(
						sprintf(
							'Hola, %s',
							$user->display_name ? $user->display_name : $user->user_login
						)
					);
					?>
				</p>
				<nav class="account-drawer__links" aria-label="Cuenta">
					<?php foreach ( $links as $endpoint => $label ) : ?>
						<a
							class="account-drawer__link"
							data-account-link
							data-account-endpoint="<?php echo esc_attr( $endpoint ); ?>"
							href="<?php echo esc_url( wc_get_account_endpoint_url( $endpoint ) ); ?>"
						><?php echo esc_html( $label ); ?></a>
					<?php endforeach; ?>
				</nav>
			<?php else : ?>
				<?php if ( $account_errors ) : ?>
					<div class="account-drawer__errors" role="alert">
						<?php foreach ( $account_errors as $notice ) : ?>
							<p><?php echo wp_kses_post( isset( $notice['notice'] ) ? $notice['notice'] : '' ); ?></p>
						<?php endforeach; ?>
					</div>
				<?php endif; ?>

				<form class="woocommerce-form woocommerce-form-login login account-drawer__form" method="post">
					<p class="account-drawer__field">
						<label for="wpb-account-login">Correo o usuario</label>
						<input id="wpb-account-login" type="text" name="username" autocomplete="username" required>
					</p>
					<p class="account-drawer__field">
						<label for="wpb-account-password">Contraseña</label>
						<input id="wpb-account-password" type="password" name="password" autocomplete="current-password" required>
					</p>
					<label class="account-drawer__remember">
						<input type="checkbox" name="rememberme" value="forever">
						<span>Recordarme</span>
					</label>
					<?php wp_nonce_field( 'woocommerce-login', 'woocommerce-login-nonce' ); ?>
					<button type="submit" name="login" value="Ingresar">Ingresar</button>
					<?php if ( function_exists( 'wc_lostpassword_url' ) ) : ?>
						<a class="account-drawer__lost" href="<?php echo esc_url( wc_lostpassword_url() ); ?>">¿Olvidaste tu contraseña?</a>
					<?php endif; ?>
				</form>

				<p class="account-drawer__note">Aún no tienes una cuenta</p>
				<a class="account-drawer__create" data-account-register href="<?php echo esc_url( home_url( '/crear-cuenta/' ) ); ?>">Crear una cuenta</a>
			<?php endif; ?>
		</div>
	</div>
</aside>
