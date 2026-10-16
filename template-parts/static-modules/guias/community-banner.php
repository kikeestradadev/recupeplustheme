<?php
/**
 * Module: community-banner (Guías).
 *
 * Uses the same AJAX subscribe flow as home community-split /
 * newsletter-banner via communitySubscribeForm.js.
 *
 * @package wpboilerplate
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$title       = function_exists( 'wpb_acf_option_text' ) ? wpb_acf_option_text( 'guias_community_title' ) : '';
$text        = function_exists( 'wpb_acf_option_text' ) ? wpb_acf_option_text( 'guias_community_text' ) : '';
$placeholder = function_exists( 'wpb_acf_option_text' ) ? wpb_acf_option_text( 'guias_community_placeholder' ) : '';
$button      = function_exists( 'wpb_acf_option_text' ) ? wpb_acf_option_text( 'guias_community_button' ) : '';

// Fallbacks match LP community-banner.pug so the section still renders when ACF is empty.
if ( '' === $title ) {
	$title = __( 'Sumate a la comunidad Recupé Plus.', 'wpboilerplate' );
}

if ( '' === $text ) {
	$text = __( 'Recibí guías, tips, lanzamientos y recomendaciones para tu bienestar.', 'wpboilerplate' );
}

if ( ! $placeholder ) {
	$placeholder = __( 'Tu correo electrónico', 'wpboilerplate' );
}

if ( ! $button ) {
	$button = __( 'Quiero sumarme', 'wpboilerplate' );
}
?>
<section class="community-banner">
	<div class="main-container community-banner__shell">
		<div class="container community-banner__container community-banner__inner">
			<div class="community-banner__card">
				<div class="community-banner__content">
					<div class="community-banner__copy">
						<h2 class="community-banner__title"><?php echo esc_html( $title ); ?></h2>
						<p class="community-banner__text"><?php echo esc_html( $text ); ?></p>
					</div>
					<form class="community-banner__form" action="<?php echo esc_url( admin_url( 'admin-ajax.php' ) ); ?>" method="post" novalidate>
						<label class="community-banner__sr-only" for="communityBannerEmail"><?php echo esc_html( $placeholder ); ?></label>
						<div class="community-banner__field">
							<input
								class="community-banner__input"
								id="communityBannerEmail"
								type="email"
								name="email"
								placeholder="<?php echo esc_attr( $placeholder ); ?>"
								autocomplete="email"
								required
							>
							<button class="community-banner__submit" type="submit"><?php echo esc_html( $button ); ?></button>
						</div>
						<p class="community-banner__honeypot" aria-hidden="true">
							<label for="communityBannerWebsite"><?php esc_html_e( 'Sitio web', 'wpboilerplate' ); ?></label>
							<input id="communityBannerWebsite" type="text" name="website" value="" tabindex="-1" autocomplete="off">
						</p>
						<p class="community-banner__status" data-community-banner-status role="status" aria-live="polite" hidden></p>
					</form>
				</div>
			</div>
		</div>
	</div>
</section>
