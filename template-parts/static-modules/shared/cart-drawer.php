<?php
/**
 * Cart drawer (LP design) — WooCommerce mini-cart panel.
 *
 * @package wpboilerplate
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'WooCommerce' ) ) {
	return;
}

$count      = function_exists( 'wpb_cart_contents_count' ) ? wpb_cart_contents_count() : 0;
$product_qtys = array();
if ( function_exists( 'WC' ) && WC()->cart ) {
	foreach ( WC()->cart->get_cart() as $cart_item ) {
		$pid = isset( $cart_item['product_id'] ) ? (int) $cart_item['product_id'] : 0;
		if ( $pid ) {
			$product_qtys[ $pid ] = isset( $product_qtys[ $pid ] )
				? $product_qtys[ $pid ] + (int) $cart_item['quantity']
				: (int) $cart_item['quantity'];
		}
	}
}
?>
<aside
	class="cart-drawer woocommerce widget_shopping_cart"
	id="cart-drawer"
	data-cart-drawer
	aria-hidden="true"
>
	<div class="cart-drawer__backdrop" data-cart-drawer-close tabindex="-1" aria-hidden="true"></div>
	<div
		class="cart-drawer__panel"
		role="dialog"
		aria-modal="true"
		aria-labelledby="cart-drawer-title"
		data-cart-drawer-panel
	>
		<header class="cart-drawer__header">
			<h2 class="cart-drawer__title" id="cart-drawer-title"><?php esc_html_e( 'Tu carrito', 'wpboilerplate' ); ?></h2>
			<?php
			if ( function_exists( 'wpb_cart_drawer_count_html' ) ) {
				echo wpb_cart_drawer_count_html( $count ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped in helper
			}
			?>
			<button
				class="cart-drawer__close"
				type="button"
				data-cart-drawer-close
				aria-label="<?php esc_attr_e( 'Cerrar carrito', 'wpboilerplate' ); ?>"
			>
				<svg class="cart-drawer__close-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" aria-hidden="true">
					<path d="M6 6l12 12M18 6L6 18" stroke="currentColor" stroke-width="1.75" stroke-linecap="round"></path>
				</svg>
			</button>
		</header>
		<p class="cart-drawer__notice" data-cart-drawer-notice role="status" aria-live="polite" hidden></p>

		<?php
		// Body HTML is shared with WC fragments. Avoid class widget_shopping_cart_content
		// so cart-fragments.js does not replace this node with the default mini-cart.
		if ( function_exists( 'wpb_cart_drawer_body_html' ) ) {
			echo wpb_cart_drawer_body_html( $count, $product_qtys ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		} else {
			?>
			<div
				class="cart-drawer__body"
				data-cart-drawer-content
				data-cart-product-qtys="<?php echo esc_attr( wp_json_encode( $product_qtys ) ); ?>"
				data-cart-total-count="<?php echo esc_attr( (string) $count ); ?>"
			>
				<?php
				if ( function_exists( 'wpb_cart_drawer_render_content' ) ) {
					wpb_cart_drawer_render_content();
				}
				?>
			</div>
			<?php
		}
		?>
	</div>
</aside>
