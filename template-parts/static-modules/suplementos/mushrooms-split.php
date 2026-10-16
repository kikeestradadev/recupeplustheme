<?php
/**
 * Static module: mushrooms-split
 *
 * @package wpboilerplate
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$eyebrow = function_exists( 'wpb_acf_option_text' ) ? wpb_acf_option_text( 'supl_mush_eyebrow' ) : '';
$title   = function_exists( 'wpb_acf_option_text' ) ? wpb_acf_option_text( 'supl_mush_title' ) : '';
$badge   = function_exists( 'wpb_acf_option_text' ) ? wpb_acf_option_text( 'supl_mush_badge' ) : '';
$text    = function_exists( 'wpb_acf_option_text' ) ? wpb_acf_option_text( 'supl_mush_text' ) : '';
$rows    = function_exists( 'wpb_acf_option_rows' ) ? wpb_acf_option_rows( 'supl_mush_items' ) : array();
$items   = array();

foreach ( $rows as $row ) {
	$description = isset( $row['description'] ) ? $row['description'] : '';
	$image       = wpb_acf_image_data( isset( $row['image'] ) ? $row['image'] : null );
	$alt         = wpb_acf_image_alt(
		isset( $row['image_alt'] ) ? $row['image_alt'] : '',
		$image
	);

	if ( ! $image && '' === $description ) {
		continue;
	}

	$items[] = array(
		'image'       => $image,
		'alt'         => $alt,
		'description' => $description,
	);
}

if ( '' === $eyebrow && '' === $title && '' === $badge && '' === $text && empty( $items ) ) {
	return;
}
?>
<section class="mushrooms-split" aria-labelledby="mushrooms-split-title">
	<div class="main-container mushrooms-split__shell">
		<div class="container mushrooms-split__container mushrooms-split__inner">
			<div class="mushrooms-split__layout">
				<div class="mushrooms-split__content">
					<?php if ( '' !== $eyebrow ) : ?>
						<p class="mushrooms-split__eyebrow"><?php echo esc_html( $eyebrow ); ?></p>
					<?php endif; ?>
					<?php if ( '' !== $title ) : ?>
						<h2 class="mushrooms-split__title" id="mushrooms-split-title"><?php echo esc_html( $title ); ?></h2>
					<?php endif; ?>
					<?php if ( '' !== $badge ) : ?>
						<span class="mushrooms-split__badge">
							<span class="mushrooms-split__badge-icon" aria-hidden="true">
								<svg width="18" height="18" viewBox="0 0 18 18" fill="none">
									<circle cx="9" cy="9" r="7.25" stroke="currentColor" stroke-width="1.4"></circle>
									<circle cx="6.4" cy="7.6" r="0.9" fill="currentColor"></circle>
									<circle cx="11.6" cy="7.6" r="0.9" fill="currentColor"></circle>
									<path d="M6.2 11c1 1.3 2.2 1.9 2.8 1.9S10.8 12.3 11.8 11" stroke="currentColor" stroke-width="1.4" stroke-linecap="round"></path>
								</svg>
							</span>
							<span class="mushrooms-split__badge-text"><?php echo esc_html( $badge ); ?></span>
						</span>
					<?php endif; ?>
					<?php if ( '' !== $text ) : ?>
						<p class="mushrooms-split__text"><?php echo esc_html( $text ); ?></p>
					<?php endif; ?>
				</div>
				<?php if ( ! empty( $items ) ) : ?>
					<div class="mushrooms-split__slider">
						<div class="mushrooms-split__swiper swiper">
							<div class="mushrooms-split__wrapper swiper-wrapper">
								<?php foreach ( $items as $item ) : ?>
									<article class="mushrooms-split__item mushrooms-split__slide swiper-slide">
										<?php if ( $item['image'] ) : ?>
											<div class="mushrooms-split__icon">
												<img
													class="mushrooms-split__image"
													src="<?php echo esc_url( $item['image']['url'] ); ?>"
													alt="<?php echo esc_attr( $item['alt'] ); ?>"
													width="<?php echo esc_attr( (string) ( $item['image']['width'] ? $item['image']['width'] : 1260 ) ); ?>"
													height="<?php echo esc_attr( (string) ( $item['image']['height'] ? $item['image']['height'] : 1260 ) ); ?>"
													loading="lazy"
												>
											</div>
										<?php endif; ?>
										<?php if ( '' !== $item['description'] ) : ?>
											<p class="mushrooms-split__item-text"><?php echo esc_html( $item['description'] ); ?></p>
										<?php endif; ?>
									</article>
								<?php endforeach; ?>
							</div>
							<div class="mushrooms-split__pagination swiper-pagination"></div>
						</div>
					</div>
				<?php endif; ?>
			</div>
		</div>
	</div>
</section>
