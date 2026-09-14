<?php
/**
 * Static module: ways-slider
 *
 * @package wpboilerplate
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$title  = function_exists( 'wpb_acf_option_text' ) ? wpb_acf_option_text( 'buy_ways_title' ) : '';
$rows   = function_exists( 'wpb_acf_option_rows' ) ? wpb_acf_option_rows( 'buy_ways_slides' ) : array();
$slides = array();

foreach ( $rows as $row ) {
	$name = isset( $row['name'] ) ? $row['name'] : '';
	$text = isset( $row['text'] ) ? $row['text'] : '';
	$icon = isset( $row['icon'] ) ? $row['icon'] : '';
	$cta  = wpb_acf_link_data( isset( $row['cta'] ) ? $row['cta'] : null );
	$image = wpb_acf_image_data( isset( $row['image'] ) ? $row['image'] : null );

	if ( ! $image && '' === $name && '' === $text ) {
		continue;
	}

	$slides[] = array(
		'name'  => $name,
		'text'  => $text,
		'icon'  => $icon,
		'cta'   => $cta,
		'image' => $image,
	);
}

if ( '' === $title && empty( $slides ) ) {
	return;
}
?>
<section class="ways-slider">
	<div class="main-container ways-slider__shell">
		<div class="container ways-slider__container ways-slider__inner">
			<?php if ( '' !== $title ) : ?>
				<h2 class="ways-slider__title"><?php echo esc_html( $title ); ?></h2>
			<?php endif; ?>
			<?php if ( ! empty( $slides ) ) : ?>
				<div class="ways-slider__slider">
					<button class="ways-slider__nav ways-slider__nav--prev" type="button" aria-label="Anterior"></button>
					<button class="ways-slider__nav ways-slider__nav--next" type="button" aria-label="Siguiente"></button>
					<div class="ways-slider__swiper swiper">
						<div class="ways-slider__wrapper swiper-wrapper">
							<?php foreach ( $slides as $slide ) : ?>
								<?php $is_whatsapp = ( 'whatsapp' === $slide['icon'] ); ?>
								<article class="ways-slider__slide swiper-slide">
									<div class="ways-slider__card">
										<div class="ways-slider__content">
											<div class="ways-slider__heading">
												<?php if ( $slide['icon'] ) : ?>
													<span class="ways-slider__icon<?php echo $is_whatsapp ? ' ways-slider__icon--whatsapp' : ''; ?>" aria-hidden="true">
														<?php if ( $is_whatsapp ) : ?>
															<svg width="24" height="24" viewBox="0 0 32 32" fill="none"><path fill="currentColor" d="M16.03 3C8.84 3 3 8.78 3 15.91c0 2.5.72 4.93 2.08 7.01L3.7 28l5.23-1.36a13.08 13.08 0 0 0 7.1 2.04C23.22 28.68 29 22.9 29 15.91S23.22 3 16.03 3Z"></path></svg>
														<?php elseif ( 'pin' === $slide['icon'] ) : ?>
															<svg width="24" height="24" viewBox="0 0 24 24" fill="none"><path d="M12 20.55s6.35-4.7 6.35-9.95A6.35 6.35 0 1 0 5.65 10.6c0 5.25 6.35 9.95 6.35 9.95Z" stroke="currentColor" stroke-width="1.7" stroke-linejoin="round"></path><circle cx="12" cy="10.2" r="2.2" stroke="currentColor" stroke-width="1.7"></circle></svg>
														<?php else : ?>
															<svg width="24" height="24" viewBox="0 0 24 24" fill="none"><path d="M3.6 5h1.9l.65 2.2" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"></path><path d="M6.15 7.2h13.05l-1.4 7.2H7.7L6.15 7.2Z" stroke="currentColor" stroke-width="1.7"></path></svg>
														<?php endif; ?>
													</span>
												<?php endif; ?>
												<?php if ( '' !== $slide['name'] ) : ?>
													<h3 class="ways-slider__name"><?php echo esc_html( $slide['name'] ); ?></h3>
												<?php endif; ?>
											</div>
											<?php if ( '' !== $slide['text'] ) : ?>
												<p class="ways-slider__text"><?php echo esc_html( $slide['text'] ); ?></p>
											<?php endif; ?>
											<?php if ( ! empty( $slide['cta']['url'] ) ) : ?>
												<a
													class="ways-slider__cta<?php echo $is_whatsapp ? ' ways-slider__cta--whatsapp' : ''; ?>"
													href="<?php echo esc_url( $slide['cta']['url'] ); ?>"
													<?php if ( $slide['cta']['target'] ) : ?>
														target="<?php echo esc_attr( $slide['cta']['target'] ); ?>"
													<?php endif; ?>
												><?php echo esc_html( $slide['cta']['title'] ? $slide['cta']['title'] : __( 'Ver más', 'wpboilerplate' ) ); ?></a>
											<?php endif; ?>
										</div>
										<?php if ( $slide['image'] ) : ?>
											<div class="ways-slider__media">
												<img
													class="ways-slider__image"
													src="<?php echo esc_url( $slide['image']['url'] ); ?>"
													alt="<?php echo esc_attr( $slide['image']['alt'] ? $slide['image']['alt'] : $slide['name'] ); ?>"
													width="<?php echo esc_attr( (string) $slide['image']['width'] ); ?>"
													height="<?php echo esc_attr( (string) $slide['image']['height'] ); ?>"
													loading="lazy"
												>
											</div>
										<?php endif; ?>
									</div>
								</article>
							<?php endforeach; ?>
						</div>
						<div class="ways-slider__pagination swiper-pagination"></div>
					</div>
				</div>
			<?php endif; ?>
		</div>
	</div>
</section>
