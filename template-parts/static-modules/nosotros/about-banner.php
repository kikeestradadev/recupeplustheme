<?php
/**
 * Static module: about-banner
 *
 * @package wpboilerplate
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$image    = function_exists( 'wpb_acf_option_image' ) ? wpb_acf_option_image( 'nos_banner_image' ) : null;
$title    = function_exists( 'wpb_acf_option_text' ) ? wpb_acf_option_text( 'nos_banner_title' ) : '';
$lines    = function_exists( 'wpb_acf_lines' ) ? wpb_acf_lines( $title ) : array();
$text     = function_exists( 'wpb_acf_option_text' ) ? wpb_acf_option_text( 'nos_banner_text' ) : '';
$products = function_exists( 'wpb_acf_option_link' ) ? wpb_acf_option_link( 'nos_banner_products' ) : array(
	'url'    => '',
	'title'  => '',
	'target' => '',
);
$whatsapp = function_exists( 'wpb_acf_option_link' ) ? wpb_acf_option_link( 'nos_banner_whatsapp' ) : array(
	'url'    => '',
	'title'  => '',
	'target' => '',
);
$last     = count( $lines ) - 1;

if ( ! $image && empty( $lines ) && '' === $text && empty( $products['url'] ) && empty( $whatsapp['url'] ) ) {
	return;
}
?>
<section class="about-banner" aria-label="<?php echo esc_attr( $lines ? implode( ' ', $lines ) : __( 'Nosotros', 'wpboilerplate' ) ); ?>">
	<div class="about-banner__layout">
		<?php if ( $image ) : ?>
			<div class="about-banner__media">
				<img
					class="about-banner__image"
					src="<?php echo esc_url( $image['url'] ); ?>"
					alt="<?php echo esc_attr( $image['alt'] ? $image['alt'] : ( $lines ? implode( ' ', $lines ) : __( 'Nosotros', 'wpboilerplate' ) ) ); ?>"
					width="<?php echo esc_attr( (string) $image['width'] ); ?>"
					height="<?php echo esc_attr( (string) $image['height'] ); ?>"
					loading="eager"
					fetchpriority="high"
				>
			</div>
		<?php endif; ?>
		<?php if ( ! empty( $lines ) || '' !== $text || ! empty( $products['url'] ) || ! empty( $whatsapp['url'] ) ) : ?>
			<div class="about-banner__content">
				<div class="about-banner__copy">
					<?php if ( ! empty( $lines ) ) : ?>
						<h1 class="about-banner__title">
							<?php foreach ( $lines as $index => $line ) : ?>
								<span class="about-banner__title-line<?php echo $index === $last ? ' about-banner__title-line--accent' : ''; ?>"><?php echo esc_html( $line ); ?></span>
							<?php endforeach; ?>
						</h1>
					<?php endif; ?>
					<?php if ( '' !== $text ) : ?>
						<p class="about-banner__text"><?php echo esc_html( $text ); ?></p>
					<?php endif; ?>
					<?php if ( ! empty( $products['url'] ) || ! empty( $whatsapp['url'] ) ) : ?>
						<div class="about-banner__actions">
							<?php if ( ! empty( $products['url'] ) ) : ?>
								<a
									class="about-banner__button about-banner__button--primary"
									href="<?php echo esc_url( $products['url'] ); ?>"
									<?php if ( $products['target'] ) : ?>
										target="<?php echo esc_attr( $products['target'] ); ?>"
									<?php endif; ?>
								><?php echo esc_html( $products['title'] ? $products['title'] : __( 'Ver productos', 'wpboilerplate' ) ); ?></a>
							<?php endif; ?>
							<?php if ( ! empty( $whatsapp['url'] ) ) : ?>
								<a
									class="about-banner__button about-banner__button--whatsapp"
									href="<?php echo esc_url( $whatsapp['url'] ); ?>"
									<?php if ( $whatsapp['target'] ) : ?>
										target="<?php echo esc_attr( $whatsapp['target'] ); ?>"
										rel="noopener noreferrer"
									<?php endif; ?>
								>
									<svg class="about-banner__button-icon" width="18" height="18" viewBox="0 0 24 24" fill="none" aria-hidden="true">
										<path d="M12 3a9 9 0 0 0-7.8 13.4L3.5 20.5l4.2-.7A9 9 0 1 0 12 3z" stroke="currentColor" stroke-width="1.6" stroke-linejoin="round"></path>
										<path d="M9.2 9.4c.2-.4.4-.4.6-.4h.5c.2 0 .4 0 .5.3l.7 1.7c.1.2 0 .4-.1.5l-.4.5c-.1.1-.1.3 0 .5.4.7 1.1 1.4 1.9 1.9.2.1.4.1.5 0l.5-.4c.2-.1.4-.2.5-.1l1.7.7c.3.1.3.3.3.5v.5c0 .2 0 .4-.4.6A5.4 5.4 0 0 1 12 16.8 5.5 5.4 0 0 1 9.2 9.4z" fill="currentColor"></path>
									</svg>
									<span><?php echo esc_html( $whatsapp['title'] ? $whatsapp['title'] : __( 'Escribir por WhatsApp', 'wpboilerplate' ) ); ?></span>
								</a>
							<?php endif; ?>
						</div>
					<?php endif; ?>
				</div>
			</div>
		<?php endif; ?>
	</div>
</section>
