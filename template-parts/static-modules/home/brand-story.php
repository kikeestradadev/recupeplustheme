<?php
/**
 * Static module: brand-story
 *
 * @package wpboilerplate
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$image = function_exists( 'wpb_acf_option_image' ) ? wpb_acf_option_image( 'home_brand_image' ) : null;
$title = function_exists( 'wpb_acf_option_text' ) ? wpb_acf_option_text( 'home_brand_title' ) : '';
$cta   = function_exists( 'wpb_acf_option_link' ) ? wpb_acf_option_link( 'home_brand_cta' ) : array(
	'url'    => '',
	'title'  => '',
	'target' => '',
);
$rows  = function_exists( 'wpb_acf_option_rows' ) ? wpb_acf_option_rows( 'home_brand_texts' ) : array();
$texts = array();

foreach ( $rows as $row ) {
	$text = isset( $row['text'] ) ? trim( (string) $row['text'] ) : '';
	if ( '' !== $text ) {
		$texts[] = $text;
	}
}

if ( ! $image && '' === $title && empty( $texts ) && empty( $cta['url'] ) ) {
	return;
}
?>
<section class="brand-story">
	<div class="main-container brand-story__shell">
		<div class="container brand-story__container brand-story__inner">
			<div class="brand-story__layout">
				<?php if ( $image ) : ?>
					<div class="brand-story__media">
						<img
							class="brand-story__image"
							src="<?php echo esc_url( $image['url'] ); ?>"
							alt="<?php echo esc_attr( $image['alt'] ? $image['alt'] : $title ); ?>"
							width="<?php echo esc_attr( (string) $image['width'] ); ?>"
							height="<?php echo esc_attr( (string) $image['height'] ); ?>"
							loading="lazy"
						>
					</div>
				<?php endif; ?>
				<div class="brand-story__content">
					<?php if ( '' !== $title ) : ?>
						<h2 class="brand-story__title"><?php echo esc_html( $title ); ?></h2>
					<?php endif; ?>
					<?php foreach ( $texts as $text ) : ?>
						<p class="brand-story__text"><?php echo esc_html( $text ); ?></p>
					<?php endforeach; ?>
					<?php if ( ! empty( $cta['url'] ) ) : ?>
						<a
							class="brand-story__cta"
							href="<?php echo esc_url( $cta['url'] ); ?>"
							<?php if ( $cta['target'] ) : ?>
								target="<?php echo esc_attr( $cta['target'] ); ?>"
							<?php endif; ?>
						><?php echo esc_html( $cta['title'] ? $cta['title'] : __( 'Conocé nuestra historia', 'wpboilerplate' ) ); ?></a>
					<?php endif; ?>
				</div>
			</div>
		</div>
	</div>
</section>
