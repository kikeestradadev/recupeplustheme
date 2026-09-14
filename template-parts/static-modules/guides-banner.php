<?php
/**
 * Static module: guides-banner
 *
 * @package wpboilerplate
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$desktop = function_exists( 'wpb_acf_option_image' ) ? wpb_acf_option_image( 'guias_banner_desktop' ) : null;
$mobile  = function_exists( 'wpb_acf_option_image' ) ? wpb_acf_option_image( 'guias_banner_mobile' ) : null;
$fallback = $mobile ? $mobile : $desktop;
$desktop  = $desktop ? $desktop : $fallback;
$mobile   = $mobile ? $mobile : $fallback;

if ( ! $mobile ) {
	return;
}
?>
<section class="guides-banner" aria-label="Guías Recupé Plus">
	<figure class="guides-banner__figure">
		<picture class="guides-banner__picture">
			<?php if ( $desktop ) : ?>
				<source media="(width >= 960px)" srcset="<?php echo esc_url( $desktop['url'] ); ?>">
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
