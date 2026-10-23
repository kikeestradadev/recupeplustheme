<?php
/**
 * Header account button. The panel lives in the footer, same as the cart drawer.
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
?>
<div class="main-menu__account">
	<button
		class="main-menu__account-toggle"
		type="button"
		data-account-menu-toggle
		aria-expanded="false"
		aria-haspopup="dialog"
		aria-controls="account-drawer"
		aria-label="<?php echo esc_attr( $logged_in ? 'Mi cuenta' : 'Ingresar o crear cuenta' ); ?>"
	>
		<svg class="main-menu__account-icon" width="24" height="24" viewBox="0 0 24 24" fill="none" aria-hidden="true">
			<circle cx="12" cy="8" r="3.25" stroke="currentColor" stroke-width="1.5"></circle>
			<path d="M5.5 19.25c.85-3.15 3.4-4.75 6.5-4.75s5.65 1.6 6.5 4.75" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"></path>
		</svg>
	</button>
</div>
