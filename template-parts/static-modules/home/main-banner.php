<?php
/**
 * Static module: main-banner
 *
 * Image-only Swiper (desktop / tablet / mobile). No overlay copy.
 *
 * @package wpboilerplate
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$slides = function_exists( 'wpb_get_home_main_slider_slides' )
	? wpb_get_home_main_slider_slides()
	: array();

if ( empty( $slides ) ) {
	return;
}
?>
<section class="main-banner" aria-label="<?php echo esc_attr__( 'Recupé Plus', 'wpboilerplate' ); ?>" data-swiper-eager>
	<h1 class="visually-hidden"><?php echo esc_html__( 'Recupé Plus', 'wpboilerplate' ); ?></h1>
	<div class="main-banner__slider">
		<button class="main-banner__nav main-banner__nav--prev" type="button" aria-label="<?php echo esc_attr__( 'Anterior', 'wpboilerplate' ); ?>"></button>
		<button class="main-banner__nav main-banner__nav--next" type="button" aria-label="<?php echo esc_attr__( 'Siguiente', 'wpboilerplate' ); ?>"></button>
		<div class="main-banner__swiper swiper">
			<div class="main-banner__wrapper swiper-wrapper">
				<?php foreach ( $slides as $index => $slide ) : ?>
					<?php
					$is_first      = 0 === $index;
					$desktop_url   = $slide['desktop']['url'];
					$tablet_url    = $slide['tablet']['url'];
					$mobile_url    = $slide['mobile']['url'];
					$mobile_width  = ! empty( $slide['mobile']['width'] ) ? (int) $slide['mobile']['width'] : 500;
					$mobile_height = ! empty( $slide['mobile']['height'] ) ? (int) $slide['mobile']['height'] : 625;
					?>
					<article class="main-banner__slide swiper-slide">
						<picture>
							<source media="(min-width: 960px)" srcset="<?php echo esc_url( $desktop_url ); ?>">
							<source media="(min-width: 640px)" srcset="<?php echo esc_url( $tablet_url ); ?>">
							<img
								class="main-banner__image"
								src="<?php echo esc_url( $mobile_url ); ?>"
								alt="<?php echo esc_attr( $slide['alt'] ); ?>"
								width="<?php echo esc_attr( (string) $mobile_width ); ?>"
								height="<?php echo esc_attr( (string) $mobile_height ); ?>"
								<?php if ( $is_first ) : ?>
									fetchpriority="high"
								<?php else : ?>
									fetchpriority="auto"
								<?php endif; ?>
								decoding="async"
							>
						</picture>
					</article>
				<?php endforeach; ?>
			</div>
			<div class="main-banner__pagination swiper-pagination"></div>
		</div>
	</div>
</section>
