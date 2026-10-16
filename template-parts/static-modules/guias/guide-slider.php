<?php
/**
 * Static module: guide-slider
 *
 * @package wpboilerplate
 */
?>
<section class="guide-slider">
        <div class="main-container guide-slider__shell">
          <div class="container guide-slider__container guide-slider__inner">
            <header class="guide-slider__header">
              <h2 class="guide-slider__title">¿Qué momento del día querés mejorar?</h2>
              <p class="guide-slider__subtitle">Si no sabés por dónde empezar, elegí el momento de tu rutina donde querés sentirte mejor.</p>
            </header>
            <div class="guide-slider__slider">
              <button class="guide-slider__nav guide-slider__nav--prev" type="button" aria-label="Anterior"></button>
              <button class="guide-slider__nav guide-slider__nav--next" type="button" aria-label="Siguiente"></button>
              <div class="guide-slider__swiper swiper">
                <div class="guide-slider__wrapper swiper-wrapper">
                  <article class="guide-slider__slide swiper-slide">
                    <div class="guide-slider__card">
                      <div class="guide-slider__media"><img class="guide-slider__image" src="<?php echo esc_url( wpb_lp_asset( 'momento.jpeg' ) ); ?>" alt="" width="320" height="320" loading="lazy"></div>
                      <div class="guide-slider__body">
                        <h3 class="guide-slider__name">Al empezar el día</h3>
                        <p class="guide-slider__products">C+Maglife - Antiox Vitamin</p>
                      </div>
                    </div>
                  </article>
                  <article class="guide-slider__slide swiper-slide">
                    <div class="guide-slider__card">
                      <div class="guide-slider__media"><img class="guide-slider__image" src="<?php echo esc_url( wpb_lp_asset( 'momento.jpeg' ) ); ?>" alt="" width="320" height="320" loading="lazy"></div>
                      <div class="guide-slider__body">
                        <h3 class="guide-slider__name">Cuando trabajas o estudias</h3>
                        <p class="guide-slider__products">Memore - Antiox Vitamin</p>
                      </div>
                    </div>
                  </article>
                  <article class="guide-slider__slide swiper-slide">
                    <div class="guide-slider__card">
                      <div class="guide-slider__media"><img class="guide-slider__image" src="<?php echo esc_url( wpb_lp_asset( 'momento.jpeg' ) ); ?>" alt="" width="320" height="320" loading="lazy"></div>
                      <div class="guide-slider__body">
                        <h3 class="guide-slider__name">Antes de entrenar o moverte</h3>
                        <p class="guide-slider__products">Activity - Memore</p>
                      </div>
                    </div>
                  </article>
                  <article class="guide-slider__slide swiper-slide">
                    <div class="guide-slider__card">
                      <div class="guide-slider__media"><img class="guide-slider__image" src="<?php echo esc_url( wpb_lp_asset( 'momento.jpeg' ) ); ?>" alt="" width="320" height="320" loading="lazy"></div>
                      <div class="guide-slider__body">
                        <h3 class="guide-slider__name">Después de hacer ejercicio</h3>
                        <p class="guide-slider__products">Recovery - Antiox Vitamin</p>
                      </div>
                    </div>
                  </article>
                  <article class="guide-slider__slide swiper-slide">
                    <div class="guide-slider__card">
                      <div class="guide-slider__media"><img class="guide-slider__image" src="<?php echo esc_url( wpb_lp_asset( 'momento.jpeg' ) ); ?>" alt="" width="320" height="320" loading="lazy"></div>
                      <div class="guide-slider__body">
                        <h3 class="guide-slider__name">Todos los días</h3>
                        <p class="guide-slider__products">C+Maglife - Vitamin Antiox - Memore</p>
                      </div>
                    </div>
                  </article>
                  <article class="guide-slider__slide swiper-slide">
                    <div class="guide-slider__card">
                      <div class="guide-slider__media"><img class="guide-slider__image" src="<?php echo esc_url( wpb_lp_asset( 'momento.jpeg' ) ); ?>" alt="" width="320" height="320" loading="lazy"></div>
                      <div class="guide-slider__body">
                        <h3 class="guide-slider__name">Antes de dormir</h3>
                        <p class="guide-slider__products">Serenity - Memore</p>
                      </div>
                    </div>
                  </article>
                </div>
                <div class="guide-slider__pagination swiper-pagination"></div>
              </div>
            </div>
            <p class="guide-slider__note">Algunos productos pueden acompañarte en más de un momento.<br>Esta guía te ayuda a elegir por dónde empezar.</p>
          </div>
        </div>
      </section>
