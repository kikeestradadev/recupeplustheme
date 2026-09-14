<?php
/**
 * Static module: allies-slider
 *
 * @package wpboilerplate
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$title  = function_exists( 'wpb_acf_option_text' ) ? wpb_acf_option_text( 'buy_allies_title' ) : '';
$rows   = function_exists( 'wpb_acf_option_rows' ) ? wpb_acf_option_rows( 'buy_allies_slides' ) : array();
$slides = array();

foreach ( $rows as $row ) {
	$logo = wpb_acf_image_data( isset( $row['logo'] ) ? $row['logo'] : null );
	if ( $logo ) {
		$slides[] = $logo;
	}
}

if ( '' === $title && empty( $slides ) ) {
	return;
}
?>
<section class="allies-slider">
	<div class="main-container allies-slider__shell">
		<div class="container allies-slider__container allies-slider__inner">
			<?php if ( '' !== $title ) : ?>
				<h2 class="allies-slider__title"><?php echo esc_html( $title ); ?></h2>
			<?php endif; ?>
			<?php if ( ! empty( $slides ) ) : ?>
				<div class="allies-slider__slider">
					<div class="allies-slider__swiper swiper">
						<div class="allies-slider__wrapper swiper-wrapper">
							<?php foreach ( $slides as $logo ) : ?>
								<article class="allies-slider__slide swiper-slide">
									<div class="allies-slider__card">
										<img
											class="allies-slider__logo"
											src="<?php echo esc_url( $logo['url'] ); ?>"
											alt="<?php echo esc_attr( $logo['alt'] ? $logo['alt'] : 'Aliado' ); ?>"
											width="<?php echo esc_attr( (string) $logo['width'] ); ?>"
											height="<?php echo esc_attr( (string) $logo['height'] ); ?>"
											loading="lazy"
										>
									</div>
								</article>
							<?php endforeach; ?>
						</div>
						<div class="allies-slider__pagination swiper-pagination"></div>
					</div>
				</div>
			<?php endif; ?>
		</div>
	</div>
</section>
