<?php
/**
 * Static module: moment-slider
 *
 * @package wpboilerplate
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$header = function_exists( 'wpb_get_home_moment_slider_header' )
	? wpb_get_home_moment_slider_header()
	: array(
		'title'    => '',
		'subtitle' => '',
	);
$slides = function_exists( 'wpb_get_home_moment_slider_slides' )
	? wpb_get_home_moment_slider_slides()
	: array();

if ( empty( $slides ) && empty( $header['title'] ) && empty( $header['subtitle'] ) ) {
	return;
}
?>
<section class="moment-slider">
	<div class="main-container moment-slider__shell">
		<div class="container moment-slider__container moment-slider__inner">
			<?php if ( ! empty( $header['title'] ) || ! empty( $header['subtitle'] ) ) : ?>
				<header class="moment-slider__header">
					<?php if ( ! empty( $header['title'] ) ) : ?>
						<h2 class="moment-slider__title"><?php echo esc_html( $header['title'] ); ?></h2>
					<?php endif; ?>
					<?php if ( ! empty( $header['subtitle'] ) ) : ?>
						<p class="moment-slider__subtitle"><?php echo esc_html( $header['subtitle'] ); ?></p>
					<?php endif; ?>
				</header>
			<?php endif; ?>
			<?php if ( ! empty( $slides ) ) : ?>
			<div class="moment-slider__slider">
				<button class="moment-slider__nav moment-slider__nav--prev" type="button" aria-label="Anterior"></button>
				<button class="moment-slider__nav moment-slider__nav--next" type="button" aria-label="Siguiente"></button>
				<div class="moment-slider__swiper swiper">
					<div class="moment-slider__wrapper swiper-wrapper">
						<?php foreach ( $slides as $slide ) : ?>
							<?php
							$has_link = ! empty( $slide['link']['url'] );
							$tag      = $has_link ? 'a' : 'div';
							$image    = $slide['image'];
							?>
							<article class="moment-slider__slide swiper-slide">
								<<?php echo tag_escape( $tag ); ?>
									class="moment-slider__card"
									<?php if ( $has_link ) : ?>
										href="<?php echo esc_url( $slide['link']['url'] ); ?>"
										<?php if ( ! empty( $slide['link']['target'] ) ) : ?>
											target="<?php echo esc_attr( $slide['link']['target'] ); ?>"
											<?php if ( '_blank' === $slide['link']['target'] ) : ?>
												rel="noopener noreferrer"
											<?php endif; ?>
										<?php endif; ?>
									<?php endif; ?>
								>
									<?php if ( $image ) : ?>
										<div class="moment-slider__media">
											<img
												class="moment-slider__image"
												src="<?php echo esc_url( $image['url'] ); ?>"
												alt="<?php echo esc_attr( $slide['alt'] ); ?>"
												width="<?php echo esc_attr( (string) $image['width'] ); ?>"
												height="<?php echo esc_attr( (string) $image['height'] ); ?>"
												loading="lazy"
											>
										</div>
									<?php endif; ?>
									<div class="moment-slider__body">
										<?php if ( ! empty( $slide['title'] ) ) : ?>
											<h3 class="moment-slider__card-title"><?php echo esc_html( $slide['title'] ); ?></h3>
										<?php endif; ?>
										<?php if ( ! empty( $slide['text'] ) ) : ?>
											<p class="moment-slider__card-text"><?php echo esc_html( $slide['text'] ); ?></p>
										<?php endif; ?>
									</div>
								</<?php echo tag_escape( $tag ); ?>>
							</article>
						<?php endforeach; ?>
					</div>
					<div class="moment-slider__pagination swiper-pagination"></div>
				</div>
			</div>
			<?php endif; ?>
		</div>
	</div>
</section>
