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

$icon_keys = array( 'citrus', 'magnesium', 'ginger', 'chamomile' );

foreach ( $rows as $row ) {
	$name = isset( $row['name'] ) ? $row['name'] : '';
	$text = isset( $row['text'] ) ? $row['text'] : '';
	$icon = isset( $row['icon'] ) ? sanitize_key( $row['icon'] ) : '';

	if ( ! in_array( $icon, $icon_keys, true ) ) {
		$icon = '';
	}

	if ( '' === $icon && '' === $name && '' === $text ) {
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
									<?php if ( '' !== $card['icon'] ) : ?>
										<span class="ingredients-split__icon" aria-hidden="true">
											<?php if ( 'citrus' === $card['icon'] ) : ?>
												<svg width="40" height="40" viewBox="0 0 40 40" fill="none"><circle cx="20" cy="20" r="14.5" stroke="currentColor" stroke-width="1.5"></circle><circle cx="20" cy="20" r="3" stroke="currentColor" stroke-width="1.5"></circle><path d="M20 6.5v10.2M20 23.3V33.5M6.5 20h10.2M23.3 20H33.5M10.5 10.5l7.2 7.2M22.3 22.3l7.2 7.2M10.5 29.5l7.2-7.2M22.3 17.7l7.2-7.2" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"></path></svg>
											<?php elseif ( 'magnesium' === $card['icon'] ) : ?>
												<svg width="40" height="40" viewBox="0 0 40 40" fill="none"><circle cx="20" cy="20" r="4" stroke="currentColor" stroke-width="1.5"></circle><circle cx="20" cy="8.5" r="2.4" stroke="currentColor" stroke-width="1.5"></circle><circle cx="29.9" cy="14.2" r="2.4" stroke="currentColor" stroke-width="1.5"></circle><circle cx="29.9" cy="25.8" r="2.4" stroke="currentColor" stroke-width="1.5"></circle><circle cx="20" cy="31.5" r="2.4" stroke="currentColor" stroke-width="1.5"></circle><circle cx="10.1" cy="25.8" r="2.4" stroke="currentColor" stroke-width="1.5"></circle><circle cx="10.1" cy="14.2" r="2.4" stroke="currentColor" stroke-width="1.5"></circle><path d="M20 16v-5.1M23.5 18.1l4.4-2.5M23.5 21.9l4.4 2.5M20 24v5.1M16.5 21.9l-4.4 2.5M16.5 18.1l-4.4-2.5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"></path></svg>
											<?php elseif ( 'ginger' === $card['icon'] ) : ?>
												<svg width="40" height="40" viewBox="0 0 40 40" fill="none"><path d="M11 27.5c-2.2-1.4-3.4-3.8-2.6-6.2.7-2.1 2.6-3.3 4.7-3.1 1.1.1 2.1-.2 2.8-1 1.2-1.4 3.2-1.8 4.9-.9 1.4.7 3.2.4 4.3-.7 1.4-1.4 3.7-1.6 5.4-.4 2 1.5 2.6 4.3 1.4 6.6-.7 1.3-.6 2.9.3 4.1 1.4 1.8 1.2 4.4-.4 6-1.7 1.7-4.5 1.8-6.4.3-1.1-.9-2.6-1-3.8-.2-1.9 1.3-4.5 1.1-6.1-.5-1.5-1.5-1.8-3.8-.8-5.6.6-1.1.5-2.5-.3-3.5-.6-.8-1.5-1.3-2.4-1.5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path><path d="M16.5 18.2c.6-2.4 1.2-5.6-.2-7.7-1.3-2-4-2.4-5.8-1" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"></path><path d="M24.8 16.8c.4-2.6 1.8-5 .8-7.3-.9-2-3.4-2.8-5.3-1.7" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"></path></svg>
											<?php elseif ( 'chamomile' === $card['icon'] ) : ?>
												<svg width="40" height="40" viewBox="0 0 40 40" fill="none"><circle cx="20" cy="18" r="4.2" stroke="currentColor" stroke-width="1.5"></circle><path d="M20 6.5c1.4 2.2 1.8 4.6 1.2 6.8h-2.4C18.2 11.1 18.6 8.7 20 6.5ZM20 29.5c-1.4-2.2-1.8-4.6-1.2-6.8h2.4c-.6 2.2-1 4.6-1.2 6.8ZM8.5 18c2.2-1.4 4.6-1.8 6.8-1.2v2.4C11.1 19.8 8.7 19.4 8.5 18ZM31.5 18c-2.2 1.4-4.6 1.8-6.8 1.2v-2.4c2.2.6 4.6 1 6.8 1.2Z" stroke="currentColor" stroke-width="1.5" stroke-linejoin="round"></path><path d="M11.6 9.6c2.5.8 4.5 2.4 5.6 4.4l-1.8 1.8c-1.2-1.9-3.1-3.3-5.4-4.1ZM28.4 26.4c-2.5-.8-4.5-2.4-5.6-4.4l1.8-1.8c1.2 1.9 3.1 3.3 5.4 4.1ZM11.6 26.4c.8-2.5 2.4-4.5 4.4-5.6l1.8 1.8c-1.9 1.2-3.3 3.1-4.1 5.4ZM28.4 9.6c-.8 2.5-2.4 4.5-4.4 5.6l-1.8-1.8c1.9-1.2 3.3-3.1 4.1-5.4Z" stroke="currentColor" stroke-width="1.5" stroke-linejoin="round"></path><path d="M20 22.5v8.5M17.5 33.5h5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"></path></svg>
											<?php endif; ?>
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
