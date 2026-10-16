<?php
/**
 * Static module: newsletter-banner
 *
 * @package wpboilerplate
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<section class="newsletter-banner">
	<div class="main-container newsletter-banner__shell">
		<div class="container newsletter-banner__container newsletter-banner__inner">
			<div class="newsletter-banner__card">
				<div class="newsletter-banner__intro">
					<span class="newsletter-banner__icon" aria-hidden="true">
						<svg class="newsletter-banner__icon-svg" width="28" height="28" viewBox="0 0 24 24" fill="none">
							<path d="M4 8l8 5 8-5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"></path>
							<rect x="3" y="6" width="18" height="12" rx="2" stroke="currentColor" stroke-width="1.8"></rect>
							<path d="M9 11h6" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"></path>
						</svg>
					</span>
					<p class="newsletter-banner__text"><?php esc_html_e( 'Recibí novedades, guías y consejos para sentirte mejor todos los días.', 'wpboilerplate' ); ?></p>
				</div>
				<div class="newsletter-banner__form-wrap">
					<form class="newsletter-banner__form" action="<?php echo esc_url( admin_url( 'admin-ajax.php' ) ); ?>" method="post" novalidate>
						<label class="newsletter-banner__sr-only" for="newsletterEmail"><?php esc_html_e( 'Tu correo electrónico', 'wpboilerplate' ); ?></label>
						<div class="newsletter-banner__field">
							<input
								class="newsletter-banner__input"
								id="newsletterEmail"
								type="email"
								name="email"
								placeholder="<?php esc_attr_e( 'Tu correo electrónico', 'wpboilerplate' ); ?>"
								autocomplete="email"
								required
							>
							<button class="newsletter-banner__submit" type="submit"><?php esc_html_e( 'Suscribirme', 'wpboilerplate' ); ?></button>
						</div>
						<p class="newsletter-banner__honeypot" aria-hidden="true">
							<label for="newsletterWebsite"><?php esc_html_e( 'Sitio web', 'wpboilerplate' ); ?></label>
							<input id="newsletterWebsite" type="text" name="website" value="" tabindex="-1" autocomplete="off">
						</p>
						<p class="newsletter-banner__status" data-newsletter-banner-status role="status" aria-live="polite" hidden></p>
					</form>
					<p class="newsletter-banner__note"><?php esc_html_e( 'Nada de spam. Solo información útil para tu bienestar.', 'wpboilerplate' ); ?></p>
				</div>
			</div>
		</div>
	</div>
</section>
