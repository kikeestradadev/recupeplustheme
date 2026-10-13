<?php
/**
 * Static module: supplements-banner
 *
 * @package wpboilerplate
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$title    = function_exists( 'wpb_acf_option_text' ) ? wpb_acf_option_text( 'supl_banner_title' ) : '';
$text     = function_exists( 'wpb_acf_option_text' ) ? wpb_acf_option_text( 'supl_banner_text' ) : '';
$picture  = function_exists( 'wpb_acf_option_picture' ) ? wpb_acf_option_picture( 'supl_banner_desktop', 'supl_banner_tablet', 'supl_banner_mobile' ) : array(
	'desktop' => null,
	'tablet'  => null,
	'mobile'  => null,
);
$mobile   = $picture['mobile'];

if ( '' === $title && '' === $text && ! $mobile ) {
	return;
}
?>
<section class="supplements-banner" aria-label="Suplementos Recupé Plus">
	<figure class="supplements-banner__figure">
		<?php if ( $mobile ) : ?>
			<picture class="supplements-banner__picture">
				<?php if ( $picture['desktop'] ) : ?>
					<source media="(width >= 1280px)" srcset="<?php echo esc_url( $picture['desktop']['url'] ); ?>">
				<?php endif; ?>
				<?php if ( $picture['tablet'] ) : ?>
					<source media="(width >= 1024px)" srcset="<?php echo esc_url( $picture['tablet']['url'] ); ?>">
				<?php endif; ?>
				<img
					class="supplements-banner__image"
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
			<div class="supplements-banner__content">
				<div class="supplements-banner__copy">
					<?php if ( '' !== $title ) : ?>
						<h1 class="supplements-banner__title"><?php echo esc_html( $title ); ?></h1>
					<?php endif; ?>
					<?php if ( '' !== $text ) : ?>
						<p class="supplements-banner__text"><?php echo esc_html( $text ); ?></p>
					<?php endif; ?>
				</div>
			</div>
		<?php endif; ?>
	</figure>
</section>
