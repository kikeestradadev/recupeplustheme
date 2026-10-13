<?php
/**
 * Static module: buy-banner
 *
 * @package wpboilerplate
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$picture = function_exists( 'wpb_acf_option_picture' ) ? wpb_acf_option_picture( 'buy_banner_desktop', 'buy_banner_tablet', 'buy_banner_mobile' ) : array(
	'desktop' => null,
	'tablet'  => null,
	'mobile'  => null,
);
$mobile  = $picture['mobile'];

if ( ! $mobile ) {
	return;
}
?>
<section class="buy-banner" aria-label="¿Dónde querés comprar Recupé Plus?">
	<figure class="buy-banner__figure">
		<picture class="buy-banner__picture">
			<?php if ( $picture['desktop'] ) : ?>
				<source media="(width >= 1280px)" srcset="<?php echo esc_url( $picture['desktop']['url'] ); ?>">
			<?php endif; ?>
			<?php if ( $picture['tablet'] ) : ?>
				<source media="(width >= 765px)" srcset="<?php echo esc_url( $picture['tablet']['url'] ); ?>">
			<?php endif; ?>
			<img
				class="buy-banner__image"
				src="<?php echo esc_url( $mobile['url'] ); ?>"
				alt="<?php echo esc_attr( $mobile['alt'] ? $mobile['alt'] : '¿Dónde querés comprar Recupé Plus?' ); ?>"
				width="<?php echo esc_attr( (string) $mobile['width'] ); ?>"
				height="<?php echo esc_attr( (string) $mobile['height'] ); ?>"
				loading="eager"
				fetchpriority="high"
			>
		</picture>
	</figure>
</section>
