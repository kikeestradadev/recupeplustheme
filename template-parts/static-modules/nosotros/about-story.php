<?php
/**
 * Static module: about-story
 *
 * @package wpboilerplate
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$image = function_exists( 'wpb_acf_option_image' ) ? wpb_acf_option_image( 'nos_story_image' ) : null;
$title = function_exists( 'wpb_acf_option_text' ) ? wpb_acf_option_text( 'nos_story_title' ) : '';
$rows  = function_exists( 'wpb_acf_option_rows' ) ? wpb_acf_option_rows( 'nos_story_texts' ) : array();
$texts = array();

foreach ( $rows as $row ) {
	$text = isset( $row['text'] ) ? trim( (string) $row['text'] ) : '';
	if ( '' !== $text ) {
		$texts[] = $text;
	}
}

if ( ! $image && '' === $title && empty( $texts ) ) {
	return;
}
?>
<section class="about-story">
	<div class="main-container about-story__shell">
		<div class="container about-story__container about-story__inner">
			<div class="about-story__layout">
				<?php if ( $image ) : ?>
					<div class="about-story__media">
						<img
							class="about-story__image"
							src="<?php echo esc_url( $image['url'] ); ?>"
							alt="<?php echo esc_attr( $image['alt'] ? $image['alt'] : $title ); ?>"
							width="<?php echo esc_attr( (string) $image['width'] ); ?>"
							height="<?php echo esc_attr( (string) $image['height'] ); ?>"
							loading="lazy"
						>
					</div>
				<?php endif; ?>
				<?php if ( '' !== $title || ! empty( $texts ) ) : ?>
					<div class="about-story__content">
						<?php if ( '' !== $title ) : ?>
							<h2 class="about-story__title"><?php echo esc_html( $title ); ?></h2>
						<?php endif; ?>
						<?php foreach ( $texts as $text ) : ?>
							<p class="about-story__text"><?php echo esc_html( $text ); ?></p>
						<?php endforeach; ?>
					</div>
				<?php endif; ?>
			</div>
		</div>
	</div>
</section>
