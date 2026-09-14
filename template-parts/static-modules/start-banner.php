<?php
/**
 * Static module: start-banner
 *
 * @package wpboilerplate
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$title     = function_exists( 'wpb_acf_option_text' ) ? wpb_acf_option_text( 'home_start_title' ) : '';
$text      = function_exists( 'wpb_acf_option_text' ) ? wpb_acf_option_text( 'home_start_text' ) : '';
$whatsapp  = function_exists( 'wpb_acf_option_link' ) ? wpb_acf_option_link( 'home_start_whatsapp' ) : array(
	'url'    => '',
	'title'  => '',
	'target' => '',
);
$products  = function_exists( 'wpb_acf_option_link' ) ? wpb_acf_option_link( 'home_start_products' ) : array(
	'url'    => '',
	'title'  => '',
	'target' => '',
);
$desktop   = function_exists( 'wpb_acf_option_image' ) ? wpb_acf_option_image( 'home_start_image_desktop' ) : null;
$tablet    = function_exists( 'wpb_acf_option_image' ) ? wpb_acf_option_image( 'home_start_image_tablet' ) : null;
$mobile    = function_exists( 'wpb_acf_option_image' ) ? wpb_acf_option_image( 'home_start_image_mobile' ) : null;
$fallback  = $mobile ? $mobile : ( $tablet ? $tablet : $desktop );
$desktop   = $desktop ? $desktop : $fallback;
$tablet    = $tablet ? $tablet : $fallback;
$mobile    = $mobile ? $mobile : $fallback;

if ( '' === $title && '' === $text && empty( $whatsapp['url'] ) && empty( $products['url'] ) && ! $fallback ) {
	return;
}
?>
<section class="start-banner">
	<div class="main-container start-banner__shell">
		<div class="container start-banner__container start-banner__inner">
			<div class="start-banner__card">
				<div class="start-banner__content">
					<?php if ( '' !== $title ) : ?>
						<h2 class="start-banner__title"><?php echo esc_html( $title ); ?></h2>
					<?php endif; ?>
					<?php if ( '' !== $text ) : ?>
						<p class="start-banner__text"><?php echo esc_html( $text ); ?></p>
					<?php endif; ?>
					<?php if ( ! empty( $whatsapp['url'] ) || ! empty( $products['url'] ) ) : ?>
						<div class="start-banner__actions">
							<?php if ( ! empty( $whatsapp['url'] ) ) : ?>
								<a
									class="start-banner__cta start-banner__cta--whatsapp"
									href="<?php echo esc_url( $whatsapp['url'] ); ?>"
									<?php if ( $whatsapp['target'] ) : ?>
										target="<?php echo esc_attr( $whatsapp['target'] ); ?>"
									<?php endif; ?>
								>
									<svg class="start-banner__cta-icon" width="18" height="18" viewBox="0 0 24 24" fill="none" aria-hidden="true">
										<path d="M12 3a9 9 0 0 0-7.8 13.4L3.5 20.5l4.2-.7A9 9 0 1 0 12 3z" stroke="currentColor" stroke-width="1.6" stroke-linejoin="round"></path>
										<path d="M9.2 9.4c.2-.4.4-.4.6-.4h.5c.2 0 .4 0 .5.3l.7 1.7c.1.2 0 .4-.1.5l-.4.5c-.1.1-.1.3 0 .5.4.7 1.1 1.4 1.9 1.9.2.1.4.1.5 0l.5-.4c.2-.1.4-.2.5-.1l1.7.7c.3.1.3.3.3.5v.5c0 .2 0 .4-.4.6A5.4 5.4 0 0 1 12 16.8 5.5 5.5 0 0 1 9.2 9.4z" fill="currentColor"></path>
									</svg>
									<span><?php echo esc_html( $whatsapp['title'] ? $whatsapp['title'] : __( 'Escribir por WhatsApp', 'wpboilerplate' ) ); ?></span>
								</a>
							<?php endif; ?>
							<?php if ( ! empty( $products['url'] ) ) : ?>
								<a
									class="start-banner__cta start-banner__cta--primary"
									href="<?php echo esc_url( $products['url'] ); ?>"
									<?php if ( $products['target'] ) : ?>
										target="<?php echo esc_attr( $products['target'] ); ?>"
									<?php endif; ?>
								><?php echo esc_html( $products['title'] ? $products['title'] : __( 'Ver productos', 'wpboilerplate' ) ); ?></a>
							<?php endif; ?>
						</div>
					<?php endif; ?>
				</div>
				<?php if ( $mobile ) : ?>
					<picture class="start-banner__picture">
						<?php if ( $desktop ) : ?>
							<source media="(width >= 1280px)" srcset="<?php echo esc_url( $desktop['url'] ); ?>">
						<?php endif; ?>
						<?php if ( $tablet ) : ?>
							<source media="(width >= 960px)" srcset="<?php echo esc_url( $tablet['url'] ); ?>">
						<?php endif; ?>
						<img
							class="start-banner__image"
							src="<?php echo esc_url( $mobile['url'] ); ?>"
							alt="<?php echo esc_attr( $mobile['alt'] ? $mobile['alt'] : $title ); ?>"
							width="<?php echo esc_attr( (string) $mobile['width'] ); ?>"
							height="<?php echo esc_attr( (string) $mobile['height'] ); ?>"
							loading="lazy"
						>
					</picture>
				<?php endif; ?>
			</div>
		</div>
	</div>
</section>
