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
$related  = function_exists( 'wpb_wc_related_products' ) ? wpb_wc_related_products( $product, 8 ) : array();
$category = function_exists( 'wpb_wc_primary_category' ) ? wpb_wc_primary_category( $product ) : array(
	'label' => '',
	'url'   => home_url( '/suplementos/' ),
);
$price    = function_exists( 'wpb_wc_format_display_price' ) ? wpb_wc_format_display_price( $product ) : wp_strip_all_tags( $product->get_price_html() );
$name     = $product->get_name();
$permalink = get_permalink( $product->get_id() );
$add_url  = $product->add_to_cart_url();
$short    = $product->get_short_description();
$review_count = (int) $product->get_review_count();
$presentacion  = function_exists( 'wpb_wc_product_presentacion' ) ? wpb_wc_product_presentacion( $product ) : 'tarro';
$present_label = function_exists( 'wpb_wc_product_presentacion_label' ) ? wpb_wc_product_presentacion_label( $presentacion ) : '';
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
					<?php if ( $present_label ) : ?>
						<p class="product-showcase__presentacion"><?php echo esc_html( $present_label ); ?></p>
					<?php endif; ?>
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

					<p class="product-showcase__meta">
						<span><?php esc_html_e( 'Categoría:', 'wpboilerplate' ); ?></span>
						<a href="<?php echo esc_url( $category['url'] ); ?>"><?php echo esc_html( $category['label'] ); ?></a>
					</p>
				</div>
			</div>

			<div class="product-showcase__tabs" data-product-tabs>
				<div class="product-showcase__tabs-bar">
					<button class="product-showcase__tabs-nav product-showcase__tabs-nav--prev" type="button" aria-label="<?php esc_attr_e( 'Tabs anteriores', 'wpboilerplate' ); ?>"></button>
					<button class="product-showcase__tabs-nav product-showcase__tabs-nav--next" type="button" aria-label="<?php esc_attr_e( 'Tabs siguientes', 'wpboilerplate' ); ?>"></button>
					<div class="product-showcase__tabs-fade product-showcase__tabs-fade--start" aria-hidden="true"></div>
					<div class="product-showcase__tabs-fade product-showcase__tabs-fade--end" aria-hidden="true"></div>
					<div class="product-showcase__tabs-swiper swiper" data-product-tabs-swiper>
						<div class="product-showcase__tabs-wrapper swiper-wrapper" role="tablist" aria-label="<?php esc_attr_e( 'Información del producto', 'wpboilerplate' ); ?>">
							<?php if ( $ficha_url ) : ?>
								<div class="product-showcase__tabs-slide swiper-slide">
									<button class="product-showcase__tab product-showcase__tab--active" type="button" role="tab" id="product-tab-ficha" aria-controls="product-panel-ficha" aria-selected="true" tabindex="0" data-product-tab="ficha"><?php esc_html_e( 'Ficha técnica', 'wpboilerplate' ); ?></button>
								</div>
							<?php endif; ?>
							<div class="product-showcase__tabs-slide swiper-slide">
								<button class="product-showcase__tab<?php echo $ficha_url ? '' : ' product-showcase__tab--active'; ?>" type="button" role="tab" id="product-tab-adicional" aria-controls="product-panel-adicional" aria-selected="<?php echo $ficha_url ? 'false' : 'true'; ?>" tabindex="<?php echo $ficha_url ? '-1' : '0'; ?>" data-product-tab="adicional"><?php esc_html_e( 'Información adicional', 'wpboilerplate' ); ?></button>
							</div>
							<div class="product-showcase__tabs-slide swiper-slide">
								<button class="product-showcase__tab" type="button" role="tab" id="product-tab-valoraciones" aria-controls="product-panel-valoraciones" aria-selected="false" tabindex="-1" data-product-tab="valoraciones">
									<?php echo esc_html( sprintf( __( 'Valoraciones (%d)', 'wpboilerplate' ), $review_count ) ); ?>
								</button>
							</div>
						</div>
					</div>
				</div>

				<?php if ( $ficha_url ) : ?>
					<div class="product-showcase__panel product-showcase__panel--active" role="tabpanel" id="product-panel-ficha" aria-labelledby="product-tab-ficha" data-product-panel="ficha">
						<img class="product-showcase__ficha" src="<?php echo esc_url( $ficha_url ); ?>" alt="<?php echo esc_attr( sprintf( __( 'Ficha técnica de %s', 'wpboilerplate' ), $name ) ); ?>" width="1855" height="2400" loading="lazy" decoding="async" fetchpriority="low">
					</div>
				<?php endif; ?>

				<div class="product-showcase__panel<?php echo $ficha_url ? '' : ' product-showcase__panel--active'; ?>" role="tabpanel" id="product-panel-adicional" aria-labelledby="product-tab-adicional" data-product-panel="adicional"<?php echo $ficha_url ? ' hidden' : ''; ?>>
					<table class="product-showcase__table">
						<tbody>
							<tr>
								<th scope="row"><?php esc_html_e( 'Marca', 'wpboilerplate' ); ?></th>
								<td>Recupé Plus</td>
							</tr>
							<tr>
								<th scope="row"><?php esc_html_e( 'SKU', 'wpboilerplate' ); ?></th>
								<td><?php echo esc_html( $product->get_sku() ? $product->get_sku() : (string) $product->get_id() ); ?></td>
							</tr>
							<tr>
								<th scope="row"><?php esc_html_e( 'Categoría', 'wpboilerplate' ); ?></th>
								<td><?php echo esc_html( $category['label'] ); ?></td>
							</tr>
							<?php if ( $present_label ) : ?>
								<tr>
									<th scope="row"><?php esc_html_e( 'Presentación', 'wpboilerplate' ); ?></th>
									<td><?php echo esc_html( $present_label ); ?></td>
								</tr>
							<?php endif; ?>
						</tbody>
					</table>
				</div>

				<div class="product-showcase__panel" role="tabpanel" id="product-panel-valoraciones" aria-labelledby="product-tab-valoraciones" hidden data-product-panel="valoraciones">
					<h2 class="product-showcase__panel-title"><?php esc_html_e( 'Valoraciones', 'wpboilerplate' ); ?></h2>
					<?php if ( $review_count > 0 ) : ?>
						<?php comments_template(); ?>
					<?php else : ?>
						<p class="product-showcase__panel-text"><?php esc_html_e( 'No hay valoraciones aún.', 'wpboilerplate' ); ?></p>
						<p class="product-showcase__panel-cta"><?php echo esc_html( sprintf( __( 'Sé el primero en valorar “%s”', 'wpboilerplate' ), $name ) ); ?></p>
					<?php endif; ?>
				</div>
			</div>

			<?php if ( ! empty( $related ) ) : ?>
				<section class="product-showcase__related" data-lazy-component>
					<h2 class="product-showcase__related-title"><?php esc_html_e( 'Productos relacionados', 'wpboilerplate' ); ?></h2>
					<div class="product-slider__filters" role="group" aria-label="<?php esc_attr_e( 'Presentación', 'wpboilerplate' ); ?>">
						<button class="product-slider__filter product-slider__filter--active" type="button" data-product-filter="tarro" aria-pressed="true">
							<?php esc_html_e( 'Productos en envase', 'wpboilerplate' ); ?>
						</button>
						<button class="product-slider__filter" type="button" data-product-filter="bolsa" aria-pressed="false">
							<?php esc_html_e( 'Paquetes de bolsas', 'wpboilerplate' ); ?>
						</button>
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
								<?php foreach ( $related as $related_product ) : ?>
									<?php
									$related_url   = get_permalink( $related_product->get_id() );
									$related_image = wp_get_attachment_image_url( $related_product->get_image_id(), 'medium' );
									if ( ! $related_image ) {
										$related_image = wc_placeholder_img_src( 'medium' );
									}
									$related_price = function_exists( 'wpb_wc_format_display_price' )
										? wpb_wc_format_display_price( $related_product )
										: wp_strip_all_tags( $related_product->get_price_html() );
									$related_presentacion = function_exists( 'wpb_wc_product_presentacion' )
										? wpb_wc_product_presentacion( $related_product )
										: 'tarro';
									?>
									<article class="product-showcase__related-card swiper-slide<?php echo 'tarro' === $related_presentacion ? '' : ' product-showcase__related-card--hidden'; ?>" data-presentacion="<?php echo esc_attr( $related_presentacion ); ?>">
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
											<span class="product-showcase__related-price-note"><?php esc_html_e( 'IVA incluido', 'wpboilerplate' ); ?></span>
										</p>
										<a class="product-showcase__related-link" href="<?php echo esc_url( $related_url ); ?>"><?php esc_html_e( 'Ver producto →', 'wpboilerplate' ); ?></a>
									</article>
								<?php endforeach; ?>
							</div>
							<div class="product-showcase__related-pagination swiper-pagination" data-product-related-pagination></div>
						</div>
					</div>
					<p class="product-slider__empty" data-product-filter-empty hidden><?php esc_html_e( 'No hay productos de esta presentación.', 'wpboilerplate' ); ?></p>
				</section>
			<?php endif; ?>
		</div>
	</div>
</section>
