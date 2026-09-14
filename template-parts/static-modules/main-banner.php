<?php
/**
 * Static module: main-banner
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
<section class="main-banner" aria-label="Recupé Plus" data-swiper-eager>
	<div class="main-banner__slider">
		<button class="main-banner__nav main-banner__nav--prev" type="button" aria-label="Anterior"></button>
		<button class="main-banner__nav main-banner__nav--next" type="button" aria-label="Siguiente"></button>
		<div class="main-banner__swiper swiper">
			<div class="main-banner__wrapper swiper-wrapper">
				<?php foreach ( $slides as $index => $slide ) : ?>
					<?php
					$is_first     = 0 === $index;
					$heading_tag  = $is_first ? 'h1' : 'h2';
					$desktop_url  = $slide['desktop']['url'];
					$tablet_url   = $slide['tablet']['url'];
					$mobile_url   = $slide['mobile']['url'];
					$mobile_width = isset( $slide['mobile']['width'] ) ? (int) $slide['mobile']['width'] : 1200;
					$mobile_height = isset( $slide['mobile']['height'] ) ? (int) $slide['mobile']['height'] : 900;
					?>
					<article class="main-banner__slide swiper-slide">
						<figure class="main-banner__media">
							<picture class="main-banner__picture">
								<source media="(width >= 1280px)" srcset="<?php echo esc_url( $desktop_url ); ?>">
								<source media="(width >= 960px)" srcset="<?php echo esc_url( $tablet_url ); ?>">
								<img
									class="main-banner__image"
									src="<?php echo esc_url( $mobile_url ); ?>"
									alt="<?php echo esc_attr( $slide['alt'] ); ?>"
									width="<?php echo esc_attr( (string) $mobile_width ); ?>"
									height="<?php echo esc_attr( (string) $mobile_height ); ?>"
									<?php if ( $is_first ) : ?>
										loading="eager"
										fetchpriority="high"
									<?php else : ?>
										loading="lazy"
									<?php endif; ?>
								>
							</picture>
						</figure>
						<div class="main-banner__content">
							<div class="main-banner__copy">
								<?php if ( ! empty( $slide['title'] ) ) : ?>
									<<?php echo tag_escape( $heading_tag ); ?> class="main-banner__title"><?php echo esc_html( $slide['title'] ); ?></<?php echo tag_escape( $heading_tag ); ?>>
								<?php endif; ?>
								<?php if ( ! empty( $slide['text'] ) ) : ?>
									<p class="main-banner__text"><?php echo esc_html( $slide['text'] ); ?></p>
								<?php endif; ?>
							</div>
						</div>
					</article>
				<?php endforeach; ?>
			</div>
			<div class="main-banner__pagination swiper-pagination"></div>
		</div>
	</div>
</section>
