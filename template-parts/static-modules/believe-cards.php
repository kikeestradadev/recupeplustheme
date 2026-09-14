<?php
/**
 * Static module: believe-cards
 *
 * @package wpboilerplate
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$title    = function_exists( 'wpb_acf_option_text' ) ? wpb_acf_option_text( 'nos_believe_title' ) : '';
$subtitle = function_exists( 'wpb_acf_option_text' ) ? wpb_acf_option_text( 'nos_believe_subtitle' ) : '';
$rows     = function_exists( 'wpb_acf_option_rows' ) ? wpb_acf_option_rows( 'nos_believe_slides' ) : array();
$slides   = array();

foreach ( $rows as $row ) {
	$name  = isset( $row['name'] ) ? $row['name'] : '';
	$text  = isset( $row['text'] ) ? $row['text'] : '';
	$image = wpb_acf_image_data( isset( $row['image'] ) ? $row['image'] : null );

	if ( ! $image && '' === $name && '' === $text ) {
		continue;
	}

	$slides[] = array(
		'name'  => $name,
		'text'  => $text,
		'image' => $image,
	);
}

if ( '' === $title && '' === $subtitle && empty( $slides ) ) {
	return;
}
?>
<section class="believe-cards">
	<div class="main-container believe-cards__shell">
		<div class="container believe-cards__container believe-cards__inner">
			<?php if ( '' !== $title || '' !== $subtitle ) : ?>
				<header class="believe-cards__header">
					<?php if ( '' !== $title ) : ?>
						<h2 class="believe-cards__title"><?php echo esc_html( $title ); ?></h2>
					<?php endif; ?>
					<?php if ( '' !== $subtitle ) : ?>
						<p class="believe-cards__subtitle"><?php echo esc_html( $subtitle ); ?></p>
					<?php endif; ?>
				</header>
			<?php endif; ?>
			<?php if ( ! empty( $slides ) ) : ?>
				<div class="believe-cards__slider">
					<button class="believe-cards__nav believe-cards__nav--prev" type="button" aria-label="Anterior"></button>
					<button class="believe-cards__nav believe-cards__nav--next" type="button" aria-label="Siguiente"></button>
					<div class="believe-cards__swiper swiper">
						<div class="believe-cards__wrapper swiper-wrapper">
							<?php foreach ( $slides as $slide ) : ?>
								<article class="believe-cards__slide swiper-slide">
									<div class="believe-cards__card">
										<?php if ( '' !== $slide['name'] || '' !== $slide['text'] ) : ?>
											<div class="believe-cards__body">
												<?php if ( '' !== $slide['name'] ) : ?>
													<h3 class="believe-cards__name"><?php echo esc_html( $slide['name'] ); ?></h3>
												<?php endif; ?>
												<?php if ( '' !== $slide['text'] ) : ?>
													<p class="believe-cards__text"><?php echo esc_html( $slide['text'] ); ?></p>
												<?php endif; ?>
											</div>
										<?php endif; ?>
										<?php if ( $slide['image'] ) : ?>
											<div class="believe-cards__media">
												<img
													class="believe-cards__image"
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
						<div class="believe-cards__pagination swiper-pagination"></div>
					</div>
				</div>
			<?php endif; ?>
		</div>
	</div>
</section>
