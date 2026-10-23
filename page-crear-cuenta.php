<?php
/**
 * Template for the page with slug `crear-cuenta`.
 * Email, password, and password confirmation. WooCommerce creates the customer.
 *
 * @package wpboilerplate
 */

get_header();

$register_url = home_url( '/crear-cuenta/' );
?>
<section class="register-page">
	<div class="container register-page__inner">
		<h1 class="register-page__title">Crear una cuenta</h1>
		<?php
		if ( function_exists( 'woocommerce_output_all_notices' ) ) {
			woocommerce_output_all_notices();
		}
		?>
		<?php if ( is_user_logged_in() ) : ?>
			<p>Ya tienes una sesión abierta.</p>
			<p><a href="<?php echo esc_url( function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'myaccount' ) : home_url( '/' ) ); ?>">Ir a mi cuenta</a></p>
		<?php else : ?>
			<form class="register-form" data-register-form method="post" action="<?php echo esc_url( $register_url ); ?>" novalidate>
				<p class="register-form__field">
					<label for="wpb-register-email">Dirección de correo electrónico</label>
					<input id="wpb-register-email" type="email" name="email" autocomplete="email" required>
				</p>
				<p class="register-form__field">
					<label for="wpb-register-password">Contraseña</label>
					<input id="wpb-register-password" data-register-password type="password" name="password" autocomplete="new-password" required>
				</p>
				<p class="register-form__field">
					<label for="wpb-register-password-2">Repite la contraseña</label>
					<input id="wpb-register-password-2" data-register-password-confirm type="password" name="password_2" autocomplete="new-password" required>
				</p>
				<ul class="register-form__errors" data-register-errors hidden role="alert"></ul>
				<p class="register-form__note">Mínimo 8 caracteres, una mayúscula, un número y un carácter especial.</p>
				<?php wp_nonce_field( 'woocommerce-register', 'woocommerce-register-nonce' ); ?>
				<button type="submit" name="register" value="Registrarse">Registrarse</button>
			</form>
		<?php endif; ?>
	</div>
</section>
<?php
get_footer();
