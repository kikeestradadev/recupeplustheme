<?php
/**
 * Static module: main-slider
 *
 * @package wpboilerplate
 */
?>
<section class="main-slider" aria-label="Productos y beneficios destacados" data-main-slider>
        <div class="main-slider__swiper swiper">
          <div class="main-slider__wrapper swiper-wrapper">
            <article class="main-slider__slide main-slider__slide--performance swiper-slide"><img class="main-slider__image" src="<?php echo esc_url( wpb_lp_asset( 'slide1.jpg' ) ); ?>" alt="Mujer entrenando con cuerdas de batalla" width="1024" height="683" loading="eager" fetchpriority="high">
              <div class="main-slider__overlay" aria-hidden="true"></div>
              <div class="main-container main-slider__content">
                <div class="container main-slider__content-inner">
                  <div class="main-slider__copy">
                    <p class="main-slider__eyebrow">Energía para tu rutina</p>
                    <h1 class="main-slider__title"><span class="main-slider__title-line">Apoyá tu rendimiento</span><span class="main-slider__title-line main-slider__title-line--accent">físico y mental</span></h1>
                    <p class="main-slider__text">Fórmulas funcionales para acompañar tu energía, enfoque y recuperación cada día.</p>
                    <div class="main-slider__actions"><a class="main-slider__button main-slider__button--primary" href="#">Descubrir productos</a><a class="main-slider__button main-slider__button--secondary" href="#">Comprar en línea</a></div>
                  </div>
                </div>
              </div>
            </article>
            <article class="main-slider__slide main-slider__slide--gummies swiper-slide"><img class="main-slider__image" src="<?php echo esc_url( wpb_lp_asset( 'slide2.jpg' ) ); ?>" alt="Gomitas funcionales Recupé Plus de sabores naranja y frutos rojos" width="1024" height="683" loading="lazy" fetchpriority="auto">
              <div class="main-slider__overlay" aria-hidden="true"></div>
              <div class="main-container main-slider__content">
                <div class="container main-slider__content-inner">
                  <div class="main-slider__copy">
                    <p class="main-slider__eyebrow">Bienestar que disfrutás</p>
                    <h2 class="main-slider__title"><span class="main-slider__title-line">Gomitas</span><span class="main-slider__title-line main-slider__title-line--accent">funcionales</span></h2>
                    <p class="main-slider__text">Una forma práctica y deliciosa de sumar vitaminas, antioxidantes y bienestar a tu rutina.</p>
                    <div class="main-slider__actions"><a class="main-slider__button main-slider__button--primary" href="#">Conocer las gomitas</a><a class="main-slider__button main-slider__button--secondary" href="#">Comprar en línea</a></div>
                  </div>
                </div>
              </div>
            </article>
            <article class="main-slider__slide main-slider__slide--mushrooms swiper-slide"><img class="main-slider__image" src="<?php echo esc_url( wpb_lp_asset( 'slide3.jpg' ) ); ?>" alt="Selección de hongos medicinales sobre una superficie de madera" width="1024" height="683" loading="lazy" fetchpriority="auto">
              <div class="main-slider__overlay" aria-hidden="true"></div>
              <div class="main-container main-slider__content">
                <div class="container main-slider__content-inner">
                  <div class="main-slider__copy">
                    <p class="main-slider__eyebrow">Naturaleza con propósito</p>
                    <h2 class="main-slider__title"><span class="main-slider__title-line">Hongos</span><span class="main-slider__title-line main-slider__title-line--accent">medicinales</span></h2>
                    <p class="main-slider__text">Activos naturales seleccionados para acompañar tu enfoque, energía, descanso e inmunidad.</p>
                    <div class="main-slider__actions"><a class="main-slider__button main-slider__button--primary" href="#">Explorar sus beneficios</a><a class="main-slider__button main-slider__button--secondary" href="#">Ver productos</a></div>
                  </div>
                </div>
              </div>
            </article>
          </div>
          <button class="main-slider__nav main-slider__nav--prev" type="button" aria-label="Diapositiva anterior"><span class="main-slider__nav-icon" aria-hidden="true">←</span></button>
          <button class="main-slider__nav main-slider__nav--next" type="button" aria-label="Diapositiva siguiente"><span class="main-slider__nav-icon" aria-hidden="true">→</span></button>
          <div class="main-slider__footer">
            <div class="main-slider__pagination swiper-pagination"></div>
            <button class="main-slider__autoplay" type="button" aria-label="Pausar reproducción automática" aria-pressed="false" data-main-slider-autoplay><span class="main-slider__autoplay-icon" aria-hidden="true">Ⅱ</span><span class="main-slider__autoplay-label">Pausar</span></button>
          </div>
        </div>
      </section>
