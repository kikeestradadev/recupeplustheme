<?php
/**
 * Static module: community-banner
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

if ( ! $placeholder ) {
	$placeholder = __( 'Tu correo electrónico', 'wpboilerplate' );
}

if ( ! $button ) {
	$button = __( 'Quiero sumarme', 'wpboilerplate' );
}

if ( '' === $title && '' === $text ) {
	return;
}
?>
<section class="community-banner">
	<div class="main-container community-banner__shell">
		<div class="container community-banner__container community-banner__inner">
			<div class="community-banner__card">
				<div class="community-banner__content">
					<div class="community-banner__copy">
						<?php if ( '' !== $title ) : ?>
							<h2 class="community-banner__title"><?php echo esc_html( $title ); ?></h2>
						<?php endif; ?>
						<?php if ( '' !== $text ) : ?>
							<p class="community-banner__text"><?php echo esc_html( $text ); ?></p>
						<?php endif; ?>
					</div>
					<form class="community-banner__form" action="#" method="post">
						<label class="community-banner__sr-only" for="communityEmail"><?php echo esc_html( $placeholder ); ?></label>
						<div class="community-banner__field">
							<input class="community-banner__input" id="communityEmail" type="email" name="email" placeholder="<?php echo esc_attr( $placeholder ); ?>" autocomplete="email" required>
							<button class="community-banner__submit" type="submit"><?php echo esc_html( $button ); ?></button>
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>
</section>
