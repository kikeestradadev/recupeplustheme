<?php
/**
 * Static module: ingredients-split
 *
 * @package wpboilerplate
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$eyebrow = function_exists( 'wpb_acf_option_text' ) ? wpb_acf_option_text( 'supl_ing_eyebrow' ) : '';
$title   = function_exists( 'wpb_acf_option_text' ) ? wpb_acf_option_text( 'supl_ing_title' ) : '';
$image   = function_exists( 'wpb_acf_option_image' ) ? wpb_acf_option_image( 'supl_ing_image' ) : null;
$rows    = function_exists( 'wpb_acf_option_rows' ) ? wpb_acf_option_rows( 'supl_ing_cards' ) : array();
$cards   = array();

foreach ( $rows as $row ) {
	$name = isset( $row['name'] ) ? $row['name'] : '';
	$text = isset( $row['text'] ) ? $row['text'] : '';
	$icon = wpb_acf_image_data( isset( $row['icon'] ) ? $row['icon'] : null );

	if ( ! $icon && '' === $name && '' === $text ) {
		continue;
	}

	$cards[] = array(
		'icon' => $icon,
		'name' => $name,
		'text' => $text,
	);
}

if ( '' === $eyebrow && '' === $title && ! $image && empty( $cards ) ) {
	return;
}
?>
<section class="ingredients-split" aria-labelledby="ingredients-split-title">
	<div class="main-container ingredients-split__shell">
		<div class="container ingredients-split__container ingredients-split__inner">
			<div class="ingredients-split__layout">
				<div class="ingredients-split__content">
					<?php if ( '' !== $eyebrow || '' !== $title ) : ?>
						<header class="ingredients-split__header">
							<?php if ( '' !== $eyebrow ) : ?>
								<p class="ingredients-split__eyebrow"><?php echo esc_html( $eyebrow ); ?></p>
							<?php endif; ?>
							<?php if ( '' !== $title ) : ?>
								<h2 class="ingredients-split__title" id="ingredients-split-title"><?php echo esc_html( $title ); ?></h2>
							<?php endif; ?>
						</header>
					<?php endif; ?>
					<?php if ( ! empty( $cards ) ) : ?>
						<div class="ingredients-split__grid">
							<?php foreach ( $cards as $card ) : ?>
								<article class="ingredients-split__card">
									<?php if ( $card['icon'] ) : ?>
										<span class="ingredients-split__icon">
											<img src="<?php echo esc_url( $card['icon']['url'] ); ?>" alt="" width="40" height="40">
										</span>
									<?php endif; ?>
									<div class="ingredients-split__body">
										<?php if ( '' !== $card['name'] ) : ?>
											<h3 class="ingredients-split__name"><?php echo esc_html( $card['name'] ); ?></h3>
										<?php endif; ?>
										<?php if ( '' !== $card['text'] ) : ?>
											<p class="ingredients-split__text"><?php echo esc_html( $card['text'] ); ?></p>
										<?php endif; ?>
									</div>
								</article>
							<?php endforeach; ?>
						</div>
					<?php endif; ?>
				</div>
				<?php if ( $image ) : ?>
					<div class="ingredients-split__media">
						<img
							class="ingredients-split__image"
							src="<?php echo esc_url( $image['url'] ); ?>"
							alt="<?php echo esc_attr( $image['alt'] ? $image['alt'] : $title ); ?>"
							width="<?php echo esc_attr( (string) $image['width'] ); ?>"
							height="<?php echo esc_attr( (string) $image['height'] ); ?>"
							loading="lazy"
						>
					</div>
				<?php endif; ?>
			</div>
		</div>
	</div>
</section>
