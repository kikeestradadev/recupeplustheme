<?php
/**
 * Static module: newsletter-banner
 *
 * @package wpboilerplate
 */
?>
<section class="newsletter-banner">
        <div class="main-container newsletter-banner__shell">
          <div class="container newsletter-banner__container newsletter-banner__inner">
            <div class="newsletter-banner__card">
              <div class="newsletter-banner__intro"><span class="newsletter-banner__icon" aria-hidden="true">
                  <svg class="newsletter-banner__icon-svg" width="28" height="28" viewBox="0 0 24 24" fill="none">
                    <path d="M4 8l8 5 8-5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"></path>
                    <rect x="3" y="6" width="18" height="12" rx="2" stroke="currentColor" stroke-width="1.8"></rect>
                    <path d="M9 11h6" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"></path>
                  </svg></span>
                <p class="newsletter-banner__text">Recibí novedades, guías y consejos para sentirte mejor todos los días.</p>
              </div>
              <div class="newsletter-banner__form-wrap">
                <form class="newsletter-banner__form" action="#" method="post">
                  <label class="newsletter-banner__sr-only" for="newsletterEmail">Tu correo electrónico</label>
                  <div class="newsletter-banner__field">
                    <input class="newsletter-banner__input" id="newsletterEmail" type="email" name="email" placeholder="Tu correo electrónico" required>
                    <button class="newsletter-banner__submit" type="submit">Suscribirme</button>
                  </div>
                </form>
                <p class="newsletter-banner__note">Nada de spam. Solo información útil para tu bienestar.</p>
              </div>
            </div>
          </div>
        </div>
      </section>
