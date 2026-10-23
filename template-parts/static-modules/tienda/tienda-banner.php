<?php
/**
 * Static module: tienda-banner
 *
 * @package wpboilerplate
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$title   = function_exists( 'wpb_acf_option_text' ) ? wpb_acf_option_text( 'tienda_banner_title' ) : '';
$text    = function_exists( 'wpb_acf_option_text' ) ? wpb_acf_option_text( 'tienda_banner_text' ) : '';
$picture = function_exists( 'wpb_acf_option_picture' ) ? wpb_acf_option_picture( 'tienda_banner_desktop', 'tienda_banner_tablet', 'tienda_banner_mobile' ) : array(
	'desktop' => null,
	'tablet'  => null,
	'mobile'  => null,
);
$mobile  = $picture['mobile'];

if ( '' === $title && '' === $text && ! $mobile ) {
	return;
}
?>
<section class="tienda-banner" aria-label="Tienda Recupé Plus">
	<figure class="tienda-banner__figure">
		<?php if ( $mobile ) : ?>
			<picture class="tienda-banner__picture">
				<?php if ( $picture['desktop'] ) : ?>
					<source media="(width >= 1280px)" srcset="<?php echo esc_url( $picture['desktop']['url'] ); ?>">
				<?php endif; ?>
				<?php if ( $picture['tablet'] ) : ?>
					<source media="(width >= 1024px)" srcset="<?php echo esc_url( $picture['tablet']['url'] ); ?>">
				<?php endif; ?>
				<img
					class="tienda-banner__image"
					src="<?php echo esc_url( $mobile['url'] ); ?>"
					alt="<?php echo esc_attr( $mobile['alt'] ? $mobile['alt'] : $title ); ?>"
					width="<?php echo esc_attr( (string) $mobile['width'] ); ?>"
					height="<?php echo esc_attr( (string) $mobile['height'] ); ?>"
					loading="eager"
					fetchpriority="high"
				>
			</picture>
		<?php endif; ?>
		<?php if ( '' !== $title || '' !== $text ) : ?>
			<div class="tienda-banner__content">
				<div class="tienda-banner__copy">
					<?php if ( '' !== $title ) : ?>
						<h1 class="tienda-banner__title"><?php echo esc_html( $title ); ?></h1>
					<?php endif; ?>
					<?php if ( '' !== $text ) : ?>
						<p class="tienda-banner__text"><?php echo esc_html( $text ); ?></p>
					<?php endif; ?>
				</div>
			</div>
		<?php endif; ?>
	</figure>
</section>
