<?php
/**
 * Static module: guides-banner
 *
 * @package wpboilerplate
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$picture = function_exists( 'wpb_acf_option_picture' ) ? wpb_acf_option_picture( 'guias_banner_desktop', 'guias_banner_tablet', 'guias_banner_mobile' ) : array(
	'desktop' => null,
	'tablet'  => null,
	'mobile'  => null,
);
$mobile  = $picture['mobile'];

if ( ! $mobile ) {
	return;
}
?>
<section class="guides-banner" aria-label="Guías Recupé Plus">
	<figure class="guides-banner__figure">
		<picture class="guides-banner__picture">
			<?php if ( $picture['desktop'] ) : ?>
				<source media="(width >= 1280px)" srcset="<?php echo esc_url( $picture['desktop']['url'] ); ?>">
			<?php endif; ?>
			<?php if ( $picture['tablet'] ) : ?>
				<source media="(width >= 1024px)" srcset="<?php echo esc_url( $picture['tablet']['url'] ); ?>">
			<?php endif; ?>
			<img
				class="guides-banner__image"
				src="<?php echo esc_url( $mobile['url'] ); ?>"
				alt="<?php echo esc_attr( $mobile['alt'] ? $mobile['alt'] : 'Guías Recupé Plus' ); ?>"
				width="<?php echo esc_attr( (string) $mobile['width'] ); ?>"
				height="<?php echo esc_attr( (string) $mobile['height'] ); ?>"
				loading="eager"
				fetchpriority="high"
			>
		</picture>
	</figure>
</section>
