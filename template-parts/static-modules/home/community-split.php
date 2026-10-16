<?php
/**
 * Static module: community-split
 *
 * @package wpboilerplate
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$image       = function_exists( 'wpb_acf_option_image' ) ? wpb_acf_option_image( 'home_community_image' ) : null;
$title       = function_exists( 'wpb_acf_option_text' ) ? wpb_acf_option_text( 'home_community_title' ) : '';
$text        = function_exists( 'wpb_acf_option_text' ) ? wpb_acf_option_text( 'home_community_text' ) : '';
$cta_title   = function_exists( 'wpb_acf_option_text' ) ? wpb_acf_option_text( 'home_community_cta_title' ) : '';
$placeholder = function_exists( 'wpb_acf_option_text' ) ? wpb_acf_option_text( 'home_community_placeholder' ) : '';
$button      = function_exists( 'wpb_acf_option_text' ) ? wpb_acf_option_text( 'home_community_button' ) : '';

if ( ! $placeholder ) {
	$placeholder = __( 'Tu correo electrónico', 'wpboilerplate' );
}

if ( ! $button ) {
	$button = __( 'Quiero sumarme', 'wpboilerplate' );
}

if ( ! $image && '' === $title && '' === $text && '' === $cta_title ) {
	return;
}
?>
<section class="community-split">
	<div class="main-container community-split__shell">
		<div class="container community-split__container community-split__inner">
			<div class="community-split__layout">
				<?php if ( $image ) : ?>
					<div class="community-split__media">
						<img
							class="community-split__image"
							src="<?php echo esc_url( $image['url'] ); ?>"
							alt="<?php echo esc_attr( $image['alt'] ? $image['alt'] : $title ); ?>"
							width="<?php echo esc_attr( (string) $image['width'] ); ?>"
							height="<?php echo esc_attr( (string) $image['height'] ); ?>"
							loading="lazy"
						>
					</div>
				<?php endif; ?>
				<div class="community-split__content">
					<?php if ( '' !== $title ) : ?>
						<h2 class="community-split__title"><?php echo esc_html( $title ); ?></h2>
					<?php endif; ?>
					<?php if ( '' !== $text ) : ?>
						<p class="community-split__text"><?php echo esc_html( $text ); ?></p>
					<?php endif; ?>
					<div class="community-split__cta">
						<?php if ( '' !== $cta_title ) : ?>
							<p class="community-split__cta-title"><?php echo esc_html( $cta_title ); ?></p>
						<?php endif; ?>
						<form class="community-split__form" action="<?php echo esc_url( admin_url( 'admin-ajax.php' ) ); ?>" method="post" novalidate>
							<label class="community-split__sr-only" for="communitySplitEmail"><?php echo esc_html( $placeholder ); ?></label>
							<div class="community-split__field">
								<input class="community-split__input" id="communitySplitEmail" type="email" name="email" placeholder="<?php echo esc_attr( $placeholder ); ?>" autocomplete="email" required>
								<button class="community-split__submit" type="submit"><?php echo esc_html( $button ); ?></button>
							</div>
							<p class="community-split__honeypot" aria-hidden="true">
								<label for="communitySplitWebsite"><?php esc_html_e( 'Sitio web', 'wpboilerplate' ); ?></label>
								<input id="communitySplitWebsite" type="text" name="website" value="" tabindex="-1" autocomplete="off">
							</p>
							<p class="community-split__status" data-community-split-status role="status" aria-live="polite" hidden></p>
						</form>
					</div>
				</div>
			</div>
		</div>
	</div>
</section>
