<?php
/**
 * Suplementos product slider — WooCommerce products by category.
 *
 * @package wpboilerplate
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$groups = array(
	array(
		'key'      => 'gomitas',
		'title'    => __( 'Gomitas', 'wpboilerplate' ),
		'subtitle' => __( 'Ricas, prácticas y deliciosas. Ideales para sumar bienestar todos los días.', 'wpboilerplate' ),
	),
	array(
		'key'      => 'capsulas',
		'title'    => __( 'Cápsulas', 'wpboilerplate' ),
		'subtitle' => __( 'Productos especializados con hongos y activos naturales para acompañar tu energía, enfoque, recuperación e inmunidad.', 'wpboilerplate' ),
	),
);

$modifier_map = array();
if ( function_exists( 'wpb_wc_catalog_definition' ) ) {
	foreach ( wpb_wc_catalog_definition() as $row ) {
		$modifier_map[ $row['slug'] ] = $row['modifier'];
	}
}
?>
<section class="product-slider">
	<div class="main-container product-slider__shell">
		<div class="container product-slider__container">
			<div class="product-slider__filters" role="group" aria-label="<?php esc_attr_e( 'Presentación', 'wpboilerplate' ); ?>">
				<button class="product-slider__filter product-slider__filter--active" type="button" data-product-filter="tarro" aria-pressed="true">
					<?php esc_html_e( 'Productos en envase', 'wpboilerplate' ); ?>
				</button>
				<button class="product-slider__filter" type="button" data-product-filter="bolsa" aria-pressed="false">
					<?php esc_html_e( 'Paquetes de bolsas', 'wpboilerplate' ); ?>
				</button>
			</div>
			<?php foreach ( $groups as $group ) : ?>
				<?php
				$products = function_exists( 'wpb_wc_products_by_category' )
					? wpb_wc_products_by_category( $group['key'] )
					: array();
				?>
				<div class="product-slider__group product-slider__group--<?php echo esc_attr( $group['key'] ); ?>" id="<?php echo esc_attr( $group['key'] ); ?>">
					<div class="product-slider__inner">
						<header class="product-slider__header">
							<h2 class="product-slider__title"><?php echo esc_html( $group['title'] ); ?></h2>
							<p class="product-slider__subtitle"><?php echo esc_html( $group['subtitle'] ); ?></p>
						</header>

						<?php if ( empty( $products ) ) : ?>
							<p class="product-slider__empty"><?php esc_html_e( 'Pronto verás los productos de esta categoría.', 'wpboilerplate' ); ?></p>
						<?php else : ?>
							<div class="product-slider__slider">
								<button class="product-slider__nav product-slider__nav--prev" type="button" aria-label="<?php esc_attr_e( 'Anterior', 'wpboilerplate' ); ?>"></button>
								<button class="product-slider__nav product-slider__nav--next" type="button" aria-label="<?php esc_attr_e( 'Siguiente', 'wpboilerplate' ); ?>"></button>
								<div class="product-slider__swiper swiper">
									<div class="product-slider__wrapper swiper-wrapper">
										<?php foreach ( $products as $product ) : ?>
											<?php
											if ( ! $product instanceof WC_Product ) {
												continue;
											}
											$product_id    = $product->get_id();
											$permalink     = get_permalink( $product_id );
											$slug          = $product->get_slug();
											$base_slug     = preg_replace( '/-bolsa$/', '', $slug );
											$modifier      = isset( $modifier_map[ $base_slug ] ) ? $modifier_map[ $base_slug ] : sanitize_html_class( $base_slug );
											$presentacion  = function_exists( 'wpb_wc_product_presentacion' ) ? wpb_wc_product_presentacion( $product ) : 'tarro';
											$present_label = function_exists( 'wpb_wc_product_presentacion_label' ) ? wpb_wc_product_presentacion_label( $presentacion ) : '';
											$image_url  = wp_get_attachment_image_url( $product->get_image_id(), 'medium' );
											if ( ! $image_url ) {
												$image_url = wc_placeholder_img_src( 'medium' );
											}
											$blurb = $product->get_short_description();
											$blurb = $blurb ? wp_strip_all_tags( $blurb ) : '';
											if ( '' === $blurb ) {
												$blurb = wp_strip_all_tags( $product->get_description() );
											}
											// Prefer first sentence / first line for card text.
											$lines = preg_split( '/\R+/', $blurb );
											$blurb = isset( $lines[0] ) ? trim( $lines[0] ) : $blurb;
											if ( function_exists( 'wpb_wc_catalog_definition' ) ) {
												foreach ( wpb_wc_catalog_definition() as $row ) {
													if ( $row['slug'] === $base_slug ) {
														$blurb = $row['text'];
														break;
													}
												}
											}
											?>
											<article class="product-slider__slide swiper-slide<?php echo 'tarro' === $presentacion ? '' : ' product-slider__slide--hidden'; ?>" data-presentacion="<?php echo esc_attr( $presentacion ); ?>">
												<div class="product-slider__card product-slider__card--<?php echo esc_attr( $modifier ); ?>">
													<a class="product-slider__media" href="<?php echo esc_url( $permalink ); ?>">
														<img
															class="product-slider__image"
															src="<?php echo esc_url( $image_url ); ?>"
															alt="<?php echo esc_attr( $product->get_name() ); ?>"
															width="280"
															height="400"
															loading="lazy"
															decoding="async"
														>
													</a>
													<div class="product-slider__body">
														<?php if ( $present_label ) : ?>
															<p class="product-slider__presentacion"><?php echo esc_html( $present_label ); ?></p>
														<?php endif; ?>
														<h3 class="product-slider__name">
															<a class="product-slider__name-link" href="<?php echo esc_url( $permalink ); ?>"><?php echo esc_html( $product->get_name() ); ?></a>
														</h3>
														<?php if ( $blurb ) : ?>
															<p class="product-slider__text"><?php echo esc_html( $blurb ); ?></p>
														<?php endif; ?>
														<div class="product-slider__actions">
															<a class="product-slider__link" href="<?php echo esc_url( $permalink ); ?>"><?php esc_html_e( 'Conocer más →', 'wpboilerplate' ); ?></a>
															<?php if ( $product->is_purchasable() && $product->is_in_stock() ) : ?>
																<button
																	class="product-slider__add ajax_add_to_cart"
																	type="button"
																	data-product_id="<?php echo esc_attr( (string) $product_id ); ?>"
																	data-product-id="<?php echo esc_attr( (string) $product_id ); ?>"
																	data-quantity="1"
																	data-wpb-add-to-cart
																>
																	<?php esc_html_e( 'Agregar', 'wpboilerplate' ); ?>
																</button>
															<?php endif; ?>
														</div>
													</div>
												</div>
											</article>
										<?php endforeach; ?>
									</div>
									<div class="product-slider__pagination swiper-pagination"></div>
								</div>
							</div>
							<p class="product-slider__empty" data-product-filter-empty hidden><?php esc_html_e( 'No hay productos de esta presentación en la categoría.', 'wpboilerplate' ); ?></p>
						<?php endif; ?>
					</div>
				</div>
			<?php endforeach; ?>
		</div>
	</div>
</section>
