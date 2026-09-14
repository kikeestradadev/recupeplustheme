<?php
/**
 * Static module: reviews-slider
 *
 * @package wpboilerplate
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$title   = function_exists( 'wpb_acf_option_text' ) ? wpb_acf_option_text( 'home_reviews_title' ) : '';
$rows    = function_exists( 'wpb_acf_option_rows' ) ? wpb_acf_option_rows( 'home_reviews_slider' ) : array();
$reviews = array();

foreach ( $rows as $row ) {
	$image  = wpb_acf_image_data( isset( $row['image'] ) ? $row['image'] : null );
	$quote  = isset( $row['quote'] ) ? $row['quote'] : '';
	$author = isset( $row['author'] ) ? $row['author'] : '';
	$rating = isset( $row['rating'] ) ? (int) $row['rating'] : 5;

	if ( ! $image && '' === $quote && '' === $author ) {
		continue;
	}

	$reviews[] = array(
		'image'  => $image,
		'quote'  => $quote,
		'author' => $author,
		'rating' => $rating,
	);
}

if ( '' === $title && empty( $reviews ) ) {
	return;
}
?>
<section class="reviews-slider">
	<div class="main-container reviews-slider__shell">
		<div class="container reviews-slider__container reviews-slider__inner">
			<?php if ( '' !== $title ) : ?>
				<h2 class="reviews-slider__title"><?php echo esc_html( $title ); ?></h2>
			<?php endif; ?>
			<?php if ( ! empty( $reviews ) ) : ?>
				<div class="reviews-slider__slider">
					<button class="reviews-slider__nav reviews-slider__nav--prev" type="button" aria-label="Anterior"></button>
					<button class="reviews-slider__nav reviews-slider__nav--next" type="button" aria-label="Siguiente"></button>
					<div class="reviews-slider__swiper swiper">
						<div class="reviews-slider__wrapper swiper-wrapper">
							<?php foreach ( $reviews as $review ) : ?>
								<article class="reviews-slider__slide swiper-slide">
									<div class="reviews-slider__card">
										<?php if ( $review['image'] ) : ?>
											<div class="reviews-slider__media">
												<img
													class="reviews-slider__image"
													src="<?php echo esc_url( $review['image']['url'] ); ?>"
													alt="<?php echo esc_attr( $review['image']['alt'] ? $review['image']['alt'] : $review['author'] ); ?>"
													width="<?php echo esc_attr( (string) $review['image']['width'] ); ?>"
													height="<?php echo esc_attr( (string) $review['image']['height'] ); ?>"
													loading="lazy"
												>
											</div>
										<?php endif; ?>
										<div class="reviews-slider__body">
											<?php if ( '' !== $review['quote'] ) : ?>
												<blockquote class="reviews-slider__quote"><?php echo esc_html( $review['quote'] ); ?></blockquote>
											<?php endif; ?>
											<?php if ( '' !== $review['author'] ) : ?>
												<p class="reviews-slider__author"><?php echo esc_html( $review['author'] ); ?></p>
											<?php endif; ?>
											<?php echo wpb_reviews_stars_html( $review['rating'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
										</div>
									</div>
								</article>
							<?php endforeach; ?>
						</div>
						<div class="reviews-slider__pagination swiper-pagination"></div>
					</div>
				</div>
			<?php endif; ?>
		</div>
	</div>
</section>
