<?php
/**
 * Static module: help-guides
 *
 * @package wpboilerplate
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$image    = function_exists( 'wpb_acf_option_image' ) ? wpb_acf_option_image( 'guias_help_image' ) : null;
$title    = function_exists( 'wpb_acf_option_text' ) ? wpb_acf_option_text( 'guias_help_title' ) : '';
$text     = function_exists( 'wpb_acf_option_text' ) ? wpb_acf_option_text( 'guias_help_text' ) : '';
$whatsapp = function_exists( 'wpb_acf_option_link' ) ? wpb_acf_option_link( 'guias_help_whatsapp' ) : array(
	'url'    => '',
	'title'  => '',
	'target' => '',
);
$products = function_exists( 'wpb_acf_option_link' ) ? wpb_acf_option_link( 'guias_help_products' ) : array(
	'url'    => '',
	'title'  => '',
	'target' => '',
);

if ( ! $image && '' === $title && '' === $text && empty( $whatsapp['url'] ) && empty( $products['url'] ) ) {
	return;
}
?>
<section class="help-guides">
	<div class="main-container help-guides__shell">
		<div class="container help-guides__container help-guides__inner">
			<div class="help-guides__grid">
				<article class="help-guides__help">
					<?php if ( $image ) : ?>
						<div class="help-guides__help-media">
							<img
								class="help-guides__help-image"
								src="<?php echo esc_url( $image['url'] ); ?>"
								alt="<?php echo esc_attr( $image['alt'] ? $image['alt'] : $title ); ?>"
								width="<?php echo esc_attr( (string) $image['width'] ); ?>"
								height="<?php echo esc_attr( (string) $image['height'] ); ?>"
								loading="lazy"
							>
						</div>
					<?php endif; ?>
					<div class="help-guides__help-body">
						<?php if ( '' !== $title ) : ?>
							<h2 class="help-guides__help-title"><?php echo esc_html( $title ); ?></h2>
						<?php endif; ?>
						<?php if ( '' !== $text ) : ?>
							<p class="help-guides__help-text"><?php echo esc_html( $text ); ?></p>
						<?php endif; ?>
						<?php if ( ! empty( $whatsapp['url'] ) || ! empty( $products['url'] ) ) : ?>
							<div class="help-guides__help-actions">
								<?php if ( ! empty( $whatsapp['url'] ) ) : ?>
									<a
										class="help-guides__whatsapp"
										href="<?php echo esc_url( $whatsapp['url'] ); ?>"
										<?php if ( $whatsapp['target'] ) : ?>
											target="<?php echo esc_attr( $whatsapp['target'] ); ?>"
											rel="noopener noreferrer"
										<?php endif; ?>
									>
										<svg class="help-guides__whatsapp-icon" width="18" height="18" viewBox="0 0 24 24" fill="none" aria-hidden="true">
											<path d="M12 3.2a8.3 8.3 0 0 0-7.2 12.4L4 20.8l5.4-.8A8.3 8.3 0 1 0 12 3.2Z" stroke="currentColor" stroke-width="1.6"></path>
											<path d="M9.2 9.4c.2-.4.4-.4.7-.4h.5c.2 0 .4 0 .5.4l.7 1.7c.1.2 0 .4-.1.6l-.4.5c-.1.2-.1.3 0 .5.4.7 1.1 1.4 1.8 1.8.2.1.3.1.5 0l.5-.4c.2-.1.4-.2.6-.1l1.7.7c.3.1.4.3.4.5v.5c0 .3 0 .5-.4.7A5 5 0 0 1 12 16.5 5 5 0 0 1 9.2 9.4Z" fill="currentColor"></path>
										</svg>
										<span><?php echo esc_html( $whatsapp['title'] ? $whatsapp['title'] : __( 'Escribir por WhatsApp', 'wpboilerplate' ) ); ?></span>
									</a>
								<?php endif; ?>
								<?php if ( ! empty( $products['url'] ) ) : ?>
									<div class="help-guides__products">
										<a class="help-guides__products-link" href="<?php echo esc_url( $products['url'] ); ?>"><?php echo esc_html( $products['title'] ? $products['title'] : __( 'Ver productos', 'wpboilerplate' ) ); ?></a>
										<a class="help-guides__products-arrow" href="<?php echo esc_url( $products['url'] ); ?>" aria-label="<?php echo esc_attr( $products['title'] ? $products['title'] : __( 'Ver productos', 'wpboilerplate' ) ); ?>"><span aria-hidden="true">›</span></a>
									</div>
								<?php endif; ?>
							</div>
						<?php endif; ?>
					</div>
				</article>
				<aside class="help-guides__guides">
					<h2 class="help-guides__guides-title"><?php echo esc_html__( 'Explorá más guías', 'wpboilerplate' ); ?></h2>
					<div class="help-guides__guides-list"></div>
				</aside>
			</div>
		</div>
	</div>
</section>
