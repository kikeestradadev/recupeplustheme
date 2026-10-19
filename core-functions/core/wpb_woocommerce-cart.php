<?php
/**
 * WooCommerce cart drawer: fragments, AJAX qty, front script data.
 *
 * @package wpboilerplate
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Cart contents count (0 if cart unavailable).
 *
 * @return int
 */
function wpb_cart_contents_count() {
	if ( ! function_exists( 'WC' ) || ! WC()->cart ) {
		return 0;
	}
	return (int) WC()->cart->get_cart_contents_count();
}

/**
 * Format CRC-style display price used across the theme.
 *
 * @param float $amount Amount.
 * @return string
 */
function wpb_cart_format_price( $amount ) {
	$amount = (float) $amount;
	return '₡' . number_format( $amount, 0, ',', ' ' );
}

/**
 * Variation / category line under the product name in the drawer.
 *
 * @param WC_Product $product Product.
 * @return string
 */
function wpb_cart_item_variation_label( WC_Product $product ) {
	$terms = get_the_terms( $product->get_id(), 'product_cat' );
	$cat   = ( ! empty( $terms ) && ! is_wp_error( $terms ) ) ? $terms[0]->name : '';

	$attrs = array();
	if ( $product->is_type( 'variation' ) ) {
		foreach ( $product->get_attributes() as $value ) {
			if ( $value ) {
				$attrs[] = $value;
			}
		}
	} else {
		$hongos = $product->get_attribute( 'Hongos' );
		if ( $hongos ) {
			// WC stores multi-value attributes as "A | B | C".
			$attrs = array_merge(
				$attrs,
				array_filter( array_map( 'trim', preg_split( '/\s*\|\s*/', $hongos ) ?: array() ) )
			);
		}
	}

	$parts = array_filter( array( $cat, implode( ' + ', $attrs ) ) );
	return implode( ' · ', $parts );
}

/**
 * Render cart drawer inner content (list or empty state). Used by template + fragments.
 */
function wpb_cart_drawer_render_content() {
	$cart  = ( function_exists( 'WC' ) && WC()->cart ) ? WC()->cart : null;
	$items = $cart ? $cart->get_cart() : array();

	if ( empty( $items ) ) :
		?>
		<div class="cart-drawer__empty">
			<p class="cart-drawer__empty-message woocommerce-mini-cart__empty-message"><?php esc_html_e( 'Tu carrito está vacío.', 'wpboilerplate' ); ?></p>
			<button class="cart-drawer__continue" type="button" data-cart-drawer-close><?php esc_html_e( 'Seguir comprando', 'wpboilerplate' ); ?></button>
		</div>
		<?php
		return;
	endif;
	?>
	<ul class="cart-drawer__list woocommerce-mini-cart cart_list product_list_widget" data-cart-drawer-list>
		<?php foreach ( $items as $cart_item_key => $cart_item ) : ?>
			<?php
			$product = isset( $cart_item['data'] ) && $cart_item['data'] instanceof WC_Product ? $cart_item['data'] : null;
			if ( ! $product || ! $product->exists() || (int) $cart_item['quantity'] <= 0 ) {
				continue;
			}
			$product_id   = $product->get_id();
			$permalink    = $product->is_visible() ? $product->get_permalink( $cart_item ) : '';
			$thumbnail_id = $product->get_image_id();
			$thumb_url    = $thumbnail_id ? wp_get_attachment_image_url( $thumbnail_id, 'woocommerce_gallery_thumbnail' ) : '';
			if ( ! $thumb_url ) {
				$thumb_url = wc_placeholder_img_src( 'woocommerce_gallery_thumbnail' );
			}
			$name       = $product->get_name();
			$qty        = (int) $cart_item['quantity'];
			$unit_raw   = (float) $product->get_price();
			$line_total = (float) $cart_item['line_total'];
			$variation  = wpb_cart_item_variation_label( $product );
			$remove_url = wc_get_cart_remove_url( $cart_item_key );
			?>
			<li
				class="cart-drawer__item woocommerce-mini-cart-item mini_cart_item"
				data-cart-item
				data-cart-item-key="<?php echo esc_attr( $cart_item_key ); ?>"
				data-cart-item-id="<?php echo esc_attr( (string) $product_id ); ?>"
				data-unit-price="<?php echo esc_attr( (string) $unit_raw ); ?>"
			>
				<a
					class="cart-drawer__remove remove remove_from_cart_button"
					href="<?php echo esc_url( $remove_url ); ?>"
					aria-label="<?php echo esc_attr( sprintf( __( 'Quitar %s del carrito', 'wpboilerplate' ), $name ) ); ?>"
					data-product_id="<?php echo esc_attr( (string) $product_id ); ?>"
					data-cart_item_key="<?php echo esc_attr( $cart_item_key ); ?>"
					data-cart-item-remove
				>
					<span aria-hidden="true">×</span>
				</a>
				<?php if ( $permalink ) : ?>
					<a class="cart-drawer__thumb-link" href="<?php echo esc_url( $permalink ); ?>">
						<img
							class="cart-drawer__thumb attachment-woocommerce_thumbnail"
							src="<?php echo esc_url( $thumb_url ); ?>"
							alt="<?php echo esc_attr( $name ); ?>"
							width="72"
							height="72"
							loading="lazy"
						>
					</a>
				<?php else : ?>
					<span class="cart-drawer__thumb-link">
						<img
							class="cart-drawer__thumb attachment-woocommerce_thumbnail"
							src="<?php echo esc_url( $thumb_url ); ?>"
							alt="<?php echo esc_attr( $name ); ?>"
							width="72"
							height="72"
							loading="lazy"
						>
					</span>
				<?php endif; ?>
				<div class="cart-drawer__details">
					<?php if ( $permalink ) : ?>
						<a class="cart-drawer__name" href="<?php echo esc_url( $permalink ); ?>"><?php echo esc_html( $name ); ?></a>
					<?php else : ?>
						<span class="cart-drawer__name"><?php echo esc_html( $name ); ?></span>
					<?php endif; ?>
					<?php if ( $variation ) : ?>
						<p class="cart-drawer__variation"><?php echo esc_html( $variation ); ?></p>
					<?php endif; ?>
					<div class="cart-drawer__meta">
						<div class="cart-drawer__quantity quantity" data-cart-item-qty>
							<button
								class="cart-drawer__qty-btn"
								type="button"
								data-cart-item-decrease
								aria-label="<?php echo esc_attr( sprintf( __( 'Disminuir cantidad de %s', 'wpboilerplate' ), $name ) ); ?>"
							>−</button>
							<input
								class="cart-drawer__qty-input qty"
								type="number"
								min="1"
								max="99"
								step="1"
								value="<?php echo esc_attr( (string) $qty ); ?>"
								inputmode="numeric"
								aria-label="<?php echo esc_attr( sprintf( __( 'Cantidad de %s', 'wpboilerplate' ), $name ) ); ?>"
								data-cart-item-input
								readonly
							>
							<button
								class="cart-drawer__qty-btn"
								type="button"
								data-cart-item-increase
								aria-label="<?php echo esc_attr( sprintf( __( 'Aumentar cantidad de %s', 'wpboilerplate' ), $name ) ); ?>"
							>+</button>
						</div>
						<span class="cart-drawer__price amount" data-cart-item-total><?php echo esc_html( wpb_cart_format_price( $line_total ) ); ?></span>
					</div>
				</div>
			</li>
		<?php endforeach; ?>
	</ul>
	<footer class="cart-drawer__footer">
		<p class="cart-drawer__subtotal woocommerce-mini-cart__total total">
			<strong><?php esc_html_e( 'Subtotal', 'wpboilerplate' ); ?></strong>
			<span class="cart-drawer__subtotal-value amount" data-cart-drawer-subtotal><?php echo esc_html( wpb_cart_format_price( (float) $cart->get_subtotal() ) ); ?></span>
		</p>
		<p class="cart-drawer__actions woocommerce-mini-cart__buttons buttons">
			<a class="cart-drawer__checkout button checkout wc-forward" href="<?php echo esc_url( wc_get_checkout_url() ); ?>">
				<?php esc_html_e( 'Finalizar compra', 'wpboilerplate' ); ?>
			</a>
		</p>
	</footer>
	<?php
}

/**
 * Cart badge HTML for header / fragments.
 *
 * @param int $count Cart count.
 * @return string
 */
function wpb_cart_drawer_badge_html( $count = null ) {
	if ( null === $count ) {
		$count = wpb_cart_contents_count();
	}
	$count = (int) $count;
	$hidden = $count < 1 ? ' hidden' : '';
	return sprintf(
		'<span class="main-menu__cart-badge" data-cart-drawer-badge aria-hidden="true"%s>%d</span>',
		$hidden,
		$count
	);
}

/**
 * Drawer header count pill HTML.
 *
 * @param int $count Cart count.
 * @return string
 */
function wpb_cart_drawer_count_html( $count = null ) {
	if ( null === $count ) {
		$count = wpb_cart_contents_count();
	}
	$count = (int) $count;
	if ( $count < 1 ) {
		return '<span class="cart-drawer__count" data-cart-drawer-count hidden>0</span>';
	}
	return sprintf(
		'<span class="cart-drawer__count" data-cart-drawer-count>%d</span>',
		$count
	);
}

/**
 * Build drawer body markup used by the template and cart fragments.
 *
 * @param int   $count       Cart contents count.
 * @param array $by_product  Product ID => qty map.
 * @return string
 */
function wpb_cart_drawer_body_html( $count = null, $by_product = null ) {
	if ( null === $count ) {
		$count = wpb_cart_contents_count();
	}
	if ( null === $by_product ) {
		$by_product = array();
		if ( function_exists( 'WC' ) && WC()->cart ) {
			foreach ( WC()->cart->get_cart() as $cart_item ) {
				$pid = isset( $cart_item['product_id'] ) ? (int) $cart_item['product_id'] : 0;
				if ( $pid ) {
					$by_product[ $pid ] = isset( $by_product[ $pid ] )
						? $by_product[ $pid ] + (int) $cart_item['quantity']
						: (int) $cart_item['quantity'];
				}
			}
		}
	}

	ob_start();
	// Do NOT use class "widget_shopping_cart_content" as the only hook —
	// WooCommerce cart-fragments.js replaces that selector with the default
	// mini-cart HTML (unstyled, English), wiping cart-drawer__* markup.
	echo '<div class="cart-drawer__body" data-cart-drawer-content data-cart-product-qtys="' . esc_attr( wp_json_encode( $by_product ) ) . '" data-cart-total-count="' . esc_attr( (string) (int) $count ) . '">';
	wpb_cart_drawer_render_content();
	echo '</div>';
	return ob_get_clean();
}

/**
 * WooCommerce cart fragments for drawer + badge (and showcase qty sync data).
 *
 * Priority 99 so we override WooCommerce's default
 * `div.widget_shopping_cart_content` mini-cart fragment.
 *
 * @param array $fragments Fragments.
 * @return array
 */
function wpb_cart_drawer_fragments( $fragments ) {
	$count = wpb_cart_contents_count();

	$fragments['[data-cart-drawer-badge]'] = wpb_cart_drawer_badge_html( $count );
	$fragments['[data-cart-drawer-count]'] = wpb_cart_drawer_count_html( $count );
	$fragments['[data-cart-count]']        = sprintf(
		'<span class="product-showcase__cart-count" data-cart-count%s>%d</span>',
		$count < 1 ? ' hidden' : '',
		$count
	);

	$body = wpb_cart_drawer_body_html( $count );

	$fragments['[data-cart-drawer-content]']     = $body;
	// Override WC core mini-cart fragment so it cannot wipe the LP drawer UI.
	$fragments['div.widget_shopping_cart_content'] = $body;
	$fragments['.widget_shopping_cart_content']    = $body;

	return $fragments;
}
add_filter( 'woocommerce_add_to_cart_fragments', 'wpb_cart_drawer_fragments', 99 );

/**
 * Enqueue WC cart scripts + localize theme cart config.
 */
function wpb_cart_enqueue_scripts() {
	if ( is_admin() || ! class_exists( 'WooCommerce' ) ) {
		return;
	}

	wp_enqueue_script( 'wc-add-to-cart' );
	wp_enqueue_script( 'wc-cart-fragments' );

	$product_qtys = array();
	if ( WC()->cart ) {
		foreach ( WC()->cart->get_cart() as $cart_item ) {
			$pid = isset( $cart_item['product_id'] ) ? (int) $cart_item['product_id'] : 0;
			if ( $pid ) {
				$product_qtys[ $pid ] = isset( $product_qtys[ $pid ] )
					? $product_qtys[ $pid ] + (int) $cart_item['quantity']
					: (int) $cart_item['quantity'];
			}
		}
	}

	wp_localize_script(
		'child-theme-scripts',
		'wpbCart',
		array(
			'ajaxUrl'       => WC_AJAX::get_endpoint( '%%endpoint%%' ),
			'wcAjaxUrl'     => home_url( '/?wc-ajax=' ),
			'cartUrl'       => wc_get_cart_url(),
			'checkoutUrl'   => wc_get_checkout_url(),
			'cartNonce'     => wp_create_nonce( 'wpb_cart' ),
			'cartCount'     => wpb_cart_contents_count(),
			'productQtys'   => $product_qtys,
			'i18n'          => array(
				'added'   => __( 'Añadido al carrito', 'wpboilerplate' ),
				'error'   => __( 'No se pudo actualizar el carrito.', 'wpboilerplate' ),
				'empty'   => __( 'Tu carrito está vacío.', 'wpboilerplate' ),
				'continue'=> __( 'Seguir comprando', 'wpboilerplate' ),
			),
		)
	);
}
add_action( 'wp_enqueue_scripts', 'wpb_cart_enqueue_scripts', 30 );

/**
 * AJAX: update cart item quantity, return fragments.
 */
function wpb_wc_ajax_update_cart_item() {
	check_ajax_referer( 'wpb_cart', 'nonce' );

	$key = isset( $_POST['cart_item_key'] ) ? sanitize_text_field( wp_unslash( $_POST['cart_item_key'] ) ) : '';
	$qty = isset( $_POST['quantity'] ) ? absint( $_POST['quantity'] ) : 0;

	if ( ! $key || ! WC()->cart ) {
		wp_send_json_error( array( 'message' => 'missing' ), 400 );
	}

	if ( $qty < 1 ) {
		WC()->cart->remove_cart_item( $key );
	} else {
		WC()->cart->set_quantity( $key, $qty, true );
	}

	WC_AJAX::get_refreshed_fragments();
}
add_action( 'wc_ajax_wpb_update_cart_item', 'wpb_wc_ajax_update_cart_item' );
add_action( 'wc_ajax_nopriv_wpb_update_cart_item', 'wpb_wc_ajax_update_cart_item' );

/**
 * Force AJAX add-to-cart so grid/showcase never need a full reload.
 */
add_filter( 'option_woocommerce_enable_ajax_add_to_cart', function () {
	return 'yes';
} );
