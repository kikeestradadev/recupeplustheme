<?php
/**
 * Template part for displaying the main menu
 *
 * @package wpboilerplate
 */

$menu_name = 'main-menu';
$menu      = wp_get_nav_menu_object( $menu_name );

if ( ! $menu ) {
	$locations = get_nav_menu_locations();
	if ( ! empty( $locations[ $menu_name ] ) ) {
		$menu = wp_get_nav_menu_object( $locations[ $menu_name ] );
	}
}

$menu_items = $menu ? wp_get_nav_menu_items( $menu->term_id ) : false;
$menu_tree  = array();

if ( $menu_items ) {
	_wp_menu_item_classes_by_context( $menu_items );

	foreach ( $menu_items as $item ) {
		$menu_tree[ (int) $item->menu_item_parent ][] = $item;
	}
}

if ( ! function_exists( 'recupeplus_render_nav_items' ) ) :
	/**
	 * Render nav items with the static landing-page markup.
	 *
	 * @param array $items        Items that share the same parent.
	 * @param array $children_map All items grouped by parent ID.
	 */
	function recupeplus_render_nav_items( $items, $children_map ) {
		foreach ( $items as $item ) {
			$child_items  = isset( $children_map[ (int) $item->ID ] ) ? $children_map[ (int) $item->ID ] : array();
			$has_children = ! empty( $child_items );
			$is_current   = in_array( 'current-menu-item', (array) $item->classes, true )
				|| in_array( 'current-menu-parent', (array) $item->classes, true )
				|| (int) $item->object_id === (int) get_queried_object_id();

			$item_classes = array( 'main-menu__item' );
			if ( $has_children ) {
				$item_classes[] = 'main-menu__item--has-children';
			}
			if ( $is_current ) {
				$item_classes[] = 'main-menu__item--current';
			}

			$link_atts = 'class="main-menu__link" href="' . esc_url( $item->url ) . '"';
			if ( $has_children ) {
				$link_atts .= ' aria-expanded="false" data-main-menu-submenu-toggle';
			}
			if ( $item->target ) {
				$link_atts .= ' target="' . esc_attr( $item->target ) . '"';
			}
			if ( $item->xfn ) {
				$link_atts .= ' rel="' . esc_attr( $item->xfn ) . '"';
			}
			?>
			<li class="<?php echo esc_attr( implode( ' ', $item_classes ) ); ?>">
				<a <?php echo $link_atts; ?>><?php echo esc_html( $item->title ); ?></a>
				<?php if ( $has_children ) : ?>
					<ul class="main-menu__sub-menu" data-main-menu-submenu>
						<?php recupeplus_render_nav_items( $child_items, $children_map ); ?>
					</ul>
				<?php endif; ?>
			</li>
			<?php
		}
	}
endif;
?>
<header class="main-header">
	<nav class="main-menu" aria-label="Navegación principal" data-main-menu>
		<div class="main-container main-menu__shell">
			<div class="container main-menu__container main-menu__inner">
				<?php
				$logo_id  = 135; // Media Library: logonegro
				$logo_src = wp_get_attachment_image_url( $logo_id, 'medium' );
				if ( ! $logo_src ) {
					$logo_src = function_exists( 'wpb_lp_asset' ) ? wpb_lp_asset( 'logonegro.png' ) : '';
				}
				?>
				<a class="main-menu__logo" href="<?php echo esc_url( home_url( '/' ) ); ?>">
					<img
						class="main-menu__logo-img"
						src="<?php echo esc_url( $logo_src ); ?>"
						alt="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?>"
						width="105"
						height="63"
						decoding="async"
						fetchpriority="high"
					>
				</a>
				<div class="main-menu__panel" data-main-menu-panel>
					<?php if ( ! empty( $menu_tree[0] ) ) : ?>
						<ul class="main-menu__list">
							<?php recupeplus_render_nav_items( $menu_tree[0], $menu_tree ); ?>
						</ul>
					<?php endif; ?>
				</div>
				<div class="main-menu__actions">
					<a class="main-menu__cta" href="#" aria-label="Comprar ahora">
						<span class="main-menu__cta-short">Comprar</span>
						<span class="main-menu__cta-full">Comprar ahora</span>
					</a>
					<?php
					$cart_count = function_exists( 'wpb_cart_contents_count' ) ? wpb_cart_contents_count() : 0;
					?>
					<button
						class="main-menu__cart"
						type="button"
						data-cart-drawer-open
						aria-label="<?php esc_attr_e( 'Carrito de compras', 'wpboilerplate' ); ?>"
						aria-expanded="false"
						aria-haspopup="dialog"
						aria-controls="cart-drawer"
					>
						<svg class="main-menu__cart-icon" width="24" height="24" viewBox="0 0 24 24" fill="none" aria-hidden="true">
							<path d="M6 8h12l-1 12H7L6 8z" stroke="currentColor" stroke-width="1.5" stroke-linejoin="round"></path>
							<path d="M9 8V7a3 3 0 0 1 6 0v1" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"></path>
						</svg>
						<?php
						if ( function_exists( 'wpb_cart_drawer_badge_html' ) ) {
							echo wpb_cart_drawer_badge_html( $cart_count ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
						}
						?>
					</button>
					<button class="main-menu__language" type="button" data-main-menu-language data-locale="es" data-es-href="#" data-en-href="#" data-es-label="Español" data-en-label="English" aria-label="Cambiar a English">
						<span class="main-menu__language-flag" data-flag="es" aria-hidden="true">
							<svg class="main-menu__language-flag-img" width="24" height="18" viewBox="0 0 24 18" aria-hidden="true">
								<rect width="24" height="18" fill="#c60b1e"></rect>
								<rect y="4.5" width="24" height="9" fill="#ffc400"></rect>
							</svg>
						</span>
						<span class="main-menu__language-flag" data-flag="en" hidden aria-hidden="true">
							<svg class="main-menu__language-flag-img" width="24" height="18" viewBox="0 0 24 18" aria-hidden="true">
								<rect width="24" height="18" fill="#b22234"></rect>
								<rect y="1.385" width="24" height="1.385" fill="#fff"></rect>
								<rect y="4.154" width="24" height="1.385" fill="#fff"></rect>
								<rect y="6.923" width="24" height="1.385" fill="#fff"></rect>
								<rect y="9.692" width="24" height="1.385" fill="#fff"></rect>
								<rect y="12.462" width="24" height="1.385" fill="#fff"></rect>
								<rect y="15.231" width="24" height="1.385" fill="#fff"></rect>
								<rect width="9.6" height="9.69" fill="#3c3b6e"></rect>
							</svg>
						</span>
					</button>
					<button class="main-menu__hamburger" type="button" aria-label="Abrir menú" aria-expanded="false" data-main-menu-toggle>
						<span class="main-menu__hamburger-line"></span>
						<span class="main-menu__hamburger-line"></span>
						<span class="main-menu__hamburger-line"></span>
					</button>
				</div>
			</div>
		</div>
	</nav>
</header>
