<?php
/**
 * Product showcase — layout from LP, data from WooCommerce.
 *
 * @package wpboilerplate
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$product = isset( $args['product'] ) && $args['product'] instanceof WC_Product
	? $args['product']
	: ( function_exists( 'wpb_wc_resolve_showcase_product' ) ? wpb_wc_resolve_showcase_product() : null );

if ( ! $product instanceof WC_Product ) {
	echo '<section class="product-showcase"><div class="main-container"><div class="container"><p>' . esc_html__( 'No hay productos de WooCommerce publicados todavía.', 'wpboilerplate' ) . '</p></div></div></section>';
	return;
}

$gallery  = function_exists( 'wpb_wc_product_gallery_images' ) ? wpb_wc_product_gallery_images( $product ) : array();
$related = array();
$related_seen = array( (int) $product->get_id() );
if ( function_exists( 'wpb_wc_products_by_category' ) ) {
	foreach ( array( 'gomitas', 'capsulas' ) as $category_slug ) {
		foreach ( wpb_wc_products_by_category( $category_slug ) as $catalog_product ) {
			if ( ! $catalog_product instanceof WC_Product ) {
				continue;
			}
			$catalog_id = (int) $catalog_product->get_id();
			if ( in_array( $catalog_id, $related_seen, true ) ) {
				continue;
			}
			$related_seen[] = $catalog_id;
			$related[]      = $catalog_product;
		}
	}
}
$category = function_exists( 'wpb_wc_primary_category' ) ? wpb_wc_primary_category( $product ) : array(
	'label' => '',
	'url'   => home_url( '/suplementos/' ),
);
$price    = function_exists( 'wpb_wc_format_display_price' ) ? wpb_wc_format_display_price( $product ) : wp_strip_all_tags( $product->get_price_html() );
$name     = $product->get_name();
$permalink = get_permalink( $product->get_id() );
$add_url  = $product->add_to_cart_url();
$short    = $product->get_short_description();
$related_filter_key = static function ( $item ) {
	if ( ! $item instanceof WC_Product ) {
		return 'gomitas';
	}
	$item_presentacion = function_exists( 'wpb_wc_product_presentacion' ) ? wpb_wc_product_presentacion( $item ) : 'tarro';
	if ( 'bolsa' === $item_presentacion ) {
		return 'bolsas';
	}
	$terms = get_the_terms( $item->get_id(), 'product_cat' );
	if ( ! empty( $terms ) && ! is_wp_error( $terms ) ) {
		foreach ( $terms as $term ) {
			if ( 'capsulas' === $term->slug ) {
				return 'capsulas';
			}
		}
	}
	return 'gomitas';
};
$active_related_filter = $related_filter_key( $product );
$ficha_id      = function_exists( 'wpb_wc_product_ficha_id' ) ? wpb_wc_product_ficha_id( $product ) : 0;
$ficha_url     = $ficha_id ? wp_get_attachment_image_url( $ficha_id, 'full' ) : '';

if ( empty( $gallery ) ) {
	$placeholder = wc_placeholder_img_src( 'large' );
	$gallery     = array(
		array(
			'src' => $placeholder ? $placeholder : '',
			'alt' => $name,
		),
	);
}

if ( $ficha_url ) {
	$ficha_in_gallery = false;
	foreach ( $gallery as $image ) {
		if ( isset( $image['src'] ) && $image['src'] === $ficha_url ) {
			$ficha_in_gallery = true;
			break;
		}
	}
	if ( ! $ficha_in_gallery ) {
		$ficha_thumb = wp_get_attachment_image_url( $ficha_id, 'woocommerce_gallery_thumbnail' );
		$gallery[]   = array(
			'src'   => $ficha_url,
			'thumb' => $ficha_thumb ? $ficha_thumb : $ficha_url,
			'alt'   => sprintf( __( 'Ficha técnica de %s', 'wpboilerplate' ), $name ),
		);
	}
}

$short_paragraphs = array_values(
	array_filter(
		array_map( 'trim', preg_split( '/\R+/', wp_strip_all_tags( $short ) ) ?: array() )
	)
);
if ( empty( $short_paragraphs ) && $short ) {
	$short_paragraphs = array( wp_strip_all_tags( $short ) );
}
?>
<section class="product-showcase" data-product-showcase data-product-id="<?php echo esc_attr( (string) $product->get_id() ); ?>">
	<div class="main-container product-showcase__shell">
		<div class="container product-showcase__container product-showcase__inner">
			<nav class="product-showcase__breadcrumbs" aria-label="Miga de pan">
				<ol class="product-showcase__crumbs">
					<li class="product-showcase__crumb">
						<a class="product-showcase__crumb-link" href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'Inicio', 'wpboilerplate' ); ?></a>
						<span class="product-showcase__crumb-sep" aria-hidden="true">/</span>
					</li>
					<li class="product-showcase__crumb">
						<a class="product-showcase__crumb-link" href="<?php echo esc_url( $category['url'] ); ?>"><?php echo esc_html( $category['label'] ); ?></a>
						<span class="product-showcase__crumb-sep" aria-hidden="true">/</span>
					</li>
					<li class="product-showcase__crumb">
						<span class="product-showcase__crumb-current" aria-current="page"><?php echo esc_html( $name ); ?></span>
					</li>
				</ol>
			</nav>

			<div class="product-showcase__layout">
				<div class="product-showcase__gallery">
					<div class="product-showcase__thumbs" role="list">
						<?php foreach ( $gallery as $index => $image ) : ?>
							<button
								class="product-showcase__thumb<?php echo 0 === $index ? ' product-showcase__thumb--active' : ''; ?>"
								type="button"
								role="listitem"
								data-product-thumb
								data-src="<?php echo esc_url( $image['src'] ); ?>"
								aria-label="<?php echo esc_attr( sprintf( __( 'Ver %s', 'wpboilerplate' ), $image['alt'] ) ); ?>"
								aria-pressed="<?php echo 0 === $index ? 'true' : 'false'; ?>"
							>
								<img class="product-showcase__thumb-image" src="<?php echo esc_url( ! empty( $image['thumb'] ) ? $image['thumb'] : $image['src'] ); ?>" alt="" width="80" height="80" loading="lazy" decoding="async">
							</button>
						<?php endforeach; ?>
					</div>
					<div class="product-showcase__stage" data-product-zoom>
						<img
							class="product-showcase__stage-image"
							src="<?php echo esc_url( $gallery[0]['src'] ); ?>"
							alt="<?php echo esc_attr( $gallery[0]['alt'] ); ?>"
							width="720"
							height="720"
							loading="eager"
							fetchpriority="high"
							decoding="async"
							data-product-stage
						>
						<span class="product-showcase__zoom-hint" aria-hidden="true"><?php esc_html_e( 'Ampliar', 'wpboilerplate' ); ?></span>
					</div>
				</div>

				<div class="product-showcase__summary">
					<h1 class="product-showcase__title"><?php echo esc_html( $name ); ?></h1>
					<p class="product-showcase__price">
						<span class="product-showcase__price-value"><?php echo esc_html( $price ); ?></span>
						<span class="product-showcase__price-note"><?php esc_html_e( 'IVA incluido', 'wpboilerplate' ); ?></span>
					</p>
					<?php if ( ! empty( $short_paragraphs ) ) : ?>
						<div class="product-showcase__excerpt">
							<?php foreach ( $short_paragraphs as $paragraph ) : ?>
								<p><?php echo esc_html( $paragraph ); ?></p>
							<?php endforeach; ?>
						</div>
					<?php endif; ?>

					<?php if ( $product->is_purchasable() && $product->is_in_stock() ) : ?>
						<?php
						$cart_count   = function_exists( 'wpb_cart_contents_count' ) ? wpb_cart_contents_count() : 0;
						$line_in_cart = 0;
						if ( function_exists( 'WC' ) && WC()->cart ) {
							foreach ( WC()->cart->get_cart() as $cart_item ) {
								if ( (int) $cart_item['product_id'] === (int) $product->get_id() ) {
									$line_in_cart += (int) $cart_item['quantity'];
								}
							}
						}
						$qty_value = $line_in_cart > 0 ? $line_in_cart : 1;
						?>
						<form class="product-showcase__cart cart" action="<?php echo esc_url( $permalink ); ?>" method="post" enctype="multipart/form-data" data-product-cart>
							<div class="product-showcase__qty" data-product-qty>
								<button class="product-showcase__qty-btn" type="button" data-product-qty-dec aria-label="<?php esc_attr_e( 'Disminuir cantidad', 'wpboilerplate' ); ?>">−</button>
								<input
									class="product-showcase__qty-input qty"
									type="number"
									name="quantity"
									min="1"
									max="20"
									value="<?php echo esc_attr( (string) $qty_value ); ?>"
									inputmode="numeric"
									aria-label="<?php esc_attr_e( 'Cantidad', 'wpboilerplate' ); ?>"
									data-product-qty-input
								>
								<button class="product-showcase__qty-btn" type="button" data-product-qty-inc aria-label="<?php esc_attr_e( 'Aumentar cantidad', 'wpboilerplate' ); ?>">+</button>
							</div>
							<button class="product-showcase__add single_add_to_cart_button button alt" type="submit" name="add-to-cart" value="<?php echo esc_attr( (string) $product->get_id() ); ?>">
								<?php echo esc_html( $product->single_add_to_cart_text() ); ?>
								<span
									class="product-showcase__cart-count"
									data-cart-count
									<?php echo $cart_count < 1 ? ' hidden' : ''; ?>
								><?php echo esc_html( (string) $cart_count ); ?></span>
							</button>
						</form>
					<?php endif; ?>
				</div>
			</div>

			<?php if ( ! empty( $related ) ) : ?>
				<section class="product-showcase__related" data-lazy-component>
					<h2 class="product-showcase__related-title"><?php esc_html_e( 'Productos relacionados', 'wpboilerplate' ); ?></h2>
					<div class="product-showcase__filter-bar">
						<button
							class="product-showcase__filter-arrow product-showcase__filter-arrow--prev"
							type="button"
							data-product-filter-prev
							aria-label="<?php esc_attr_e( 'Categorías anteriores', 'wpboilerplate' ); ?>"
						></button>
						<div class="product-slider__filters" role="group" aria-label="<?php esc_attr_e( 'Categoría', 'wpboilerplate' ); ?>">
							<?php
							$related_filters = array(
								'gomitas'  => __( 'Gomitas', 'wpboilerplate' ),
								'capsulas' => __( 'Cápsulas', 'wpboilerplate' ),
								'bolsas'   => __( 'Bolsas', 'wpboilerplate' ),
							);
							foreach ( $related_filters as $filter_key => $filter_label ) :
								$filter_active = $filter_key === $active_related_filter;
								?>
								<button
									class="product-slider__filter<?php echo $filter_active ? ' product-slider__filter--active' : ''; ?>"
									type="button"
									data-product-filter="<?php echo esc_attr( $filter_key ); ?>"
									aria-pressed="<?php echo $filter_active ? 'true' : 'false'; ?>"
								>
									<?php echo esc_html( $filter_label ); ?>
								</button>
							<?php endforeach; ?>
						</div>
						<button
							class="product-showcase__filter-arrow product-showcase__filter-arrow--next"
							type="button"
							data-product-filter-next
							aria-label="<?php esc_attr_e( 'Categorías siguientes', 'wpboilerplate' ); ?>"
						></button>
					</div>
					<div class="product-showcase__related-slider">
						<button
							class="product-showcase__related-nav product-showcase__related-nav--prev"
							type="button"
							aria-label="<?php esc_attr_e( 'Productos relacionados anteriores', 'wpboilerplate' ); ?>"
							data-product-related-prev
						></button>
						<button
							class="product-showcase__related-nav product-showcase__related-nav--next"
							type="button"
							aria-label="<?php esc_attr_e( 'Productos relacionados siguientes', 'wpboilerplate' ); ?>"
							data-product-related-next
						></button>
						<div class="product-showcase__related-swiper swiper" data-product-related-swiper>
							<div class="product-showcase__related-wrapper swiper-wrapper">
								<?php
								$related_modifier_map = array();
								if ( function_exists( 'wpb_wc_catalog_definition' ) ) {
									foreach ( wpb_wc_catalog_definition() as $row ) {
										$related_modifier_map[ $row['slug'] ] = $row['modifier'];
									}
								}
								foreach ( $related as $related_product ) :
									$related_url   = get_permalink( $related_product->get_id() );
									$related_image = wp_get_attachment_image_url( $related_product->get_image_id(), 'medium' );
									if ( ! $related_image ) {
										$related_image = wc_placeholder_img_src( 'medium' );
									}
									$related_price = function_exists( 'wpb_wc_format_display_price' )
										? wpb_wc_format_display_price( $related_product )
										: wp_strip_all_tags( $related_product->get_price_html() );
									$related_key  = $related_filter_key( $related_product );
									$related_base = preg_replace( '/-bolsa$/', '', $related_product->get_slug() );
									$related_modifier = isset( $related_modifier_map[ $related_base ] )
										? $related_modifier_map[ $related_base ]
										: sanitize_html_class( $related_base );
									?>
									<article class="product-showcase__related-card product-showcase__related-card--<?php echo esc_attr( $related_modifier ); ?> swiper-slide<?php echo $related_key === $active_related_filter ? '' : ' product-showcase__related-card--hidden'; ?>" data-product-group="<?php echo esc_attr( $related_key ); ?>">
										<a class="product-showcase__related-media" href="<?php echo esc_url( $related_url ); ?>">
											<img
												class="product-showcase__related-image lazyload"
												data-src="<?php echo esc_url( $related_image ); ?>"
												alt="<?php echo esc_attr( $related_product->get_name() ); ?>"
												width="280"
												height="280"
											>
										</a>
										<h3 class="product-showcase__related-name">
											<a href="<?php echo esc_url( $related_url ); ?>"><?php echo esc_html( $related_product->get_name() ); ?></a>
										</h3>
										<p class="product-showcase__related-price">
											<span><?php echo esc_html( $related_price ); ?></span>
											<span class="product-showcase__related-price-note"><?php esc_html_e( 'IVA', 'wpboilerplate' ); ?></span>
										</p>
										<a class="product-showcase__related-link" href="<?php echo esc_url( $related_url ); ?>"><?php esc_html_e( 'Ver producto →', 'wpboilerplate' ); ?></a>
									</article>
								<?php endforeach; ?>
							</div>
							<div class="product-showcase__related-pagination swiper-pagination" data-product-related-pagination></div>
						</div>
					</div>
					<p class="product-slider__empty" data-product-filter-empty hidden><?php esc_html_e( 'No hay productos en esta categoría.', 'wpboilerplate' ); ?></p>
				</section>
			<?php endif; ?>
		</div>
	</div>
</section>
