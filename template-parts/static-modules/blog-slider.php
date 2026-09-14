<?php
/**
 * Static module: blog-slider
 *
 * @package wpboilerplate
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$title = function_exists( 'wpb_acf_option_text' ) ? wpb_acf_option_text( 'home_blog_title' ) : '';
$rows  = function_exists( 'wpb_acf_option_rows' ) ? wpb_acf_option_rows( 'home_blog_slider' ) : array();
$cards = array();

foreach ( $rows as $row ) {
	$image = wpb_acf_image_data( isset( $row['image'] ) ? $row['image'] : null );
	$name  = isset( $row['name'] ) ? $row['name'] : '';
	$link  = wpb_acf_link_data( isset( $row['link'] ) ? $row['link'] : null );

	if ( ! $image && '' === $name ) {
		continue;
	}

	$cards[] = array(
		'image' => $image,
		'name'  => $name,
		'link'  => $link,
	);
}

if ( '' === $title && empty( $cards ) ) {
	return;
}
?>
<section class="blog-slider">
	<div class="main-container blog-slider__shell">
		<div class="container blog-slider__container blog-slider__inner">
			<?php if ( '' !== $title ) : ?>
				<h2 class="blog-slider__title"><?php echo esc_html( $title ); ?></h2>
			<?php endif; ?>
			<?php if ( ! empty( $cards ) ) : ?>
				<div class="blog-slider__slider">
					<button class="blog-slider__nav blog-slider__nav--prev" type="button" aria-label="Anterior"></button>
					<button class="blog-slider__nav blog-slider__nav--next" type="button" aria-label="Siguiente"></button>
					<div class="blog-slider__swiper swiper">
						<div class="blog-slider__wrapper swiper-wrapper">
							<?php foreach ( $cards as $card ) : ?>
								<?php
								$has_link = ! empty( $card['link']['url'] );
								$tag      = $has_link ? 'a' : 'div';
								?>
								<article class="blog-slider__slide swiper-slide">
									<<?php echo tag_escape( $tag ); ?>
										class="blog-slider__card"
										<?php if ( $has_link ) : ?>
											href="<?php echo esc_url( $card['link']['url'] ); ?>"
											<?php if ( $card['link']['target'] ) : ?>
												target="<?php echo esc_attr( $card['link']['target'] ); ?>"
											<?php endif; ?>
										<?php endif; ?>
									>
										<?php if ( $card['image'] ) : ?>
											<div class="blog-slider__media">
												<img
													class="blog-slider__image"
													src="<?php echo esc_url( $card['image']['url'] ); ?>"
													alt="<?php echo esc_attr( $card['image']['alt'] ? $card['image']['alt'] : $card['name'] ); ?>"
													width="<?php echo esc_attr( (string) $card['image']['width'] ); ?>"
													height="<?php echo esc_attr( (string) $card['image']['height'] ); ?>"
													loading="lazy"
												>
											</div>
										<?php endif; ?>
										<?php if ( '' !== $card['name'] ) : ?>
											<h3 class="blog-slider__name"><?php echo esc_html( $card['name'] ); ?></h3>
										<?php endif; ?>
									</<?php echo tag_escape( $tag ); ?>>
								</article>
							<?php endforeach; ?>
						</div>
						<div class="blog-slider__pagination swiper-pagination"></div>
					</div>
				</div>
			<?php endif; ?>
		</div>
	</div>
</section>
