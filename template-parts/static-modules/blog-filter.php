<?php
/**
 * Static module: blog-filter
 *
 * @package wpboilerplate
 */
?>
<section class="blog-filter" data-blog-filter data-per-page="6">
        <div class="main-container blog-filter__shell">
          <div class="container blog-filter__container blog-filter__inner">
            <div class="blog-filter__header">
              <h2 class="blog-filter__title">Guías</h2><a class="blog-filter__more" href="#"><span>Ver más</span><span class="blog-filter__more-arrow" aria-hidden="true">→</span></a>
            </div>
            <div class="blog-filter__filters">
              <button class="blog-filter__filters-nav blog-filter__filters-nav--prev" type="button" aria-label="Filtros anteriores"></button>
              <button class="blog-filter__filters-nav blog-filter__filters-nav--next" type="button" aria-label="Filtros siguientes"></button>
              <div class="blog-filter__filters-fade blog-filter__filters-fade--start" aria-hidden="true"></div>
              <div class="blog-filter__filters-fade blog-filter__filters-fade--end" aria-hidden="true"></div>
              <div class="blog-filter__filters-swiper swiper">
                <div class="blog-filter__filters-wrapper swiper-wrapper">
                  <div class="blog-filter__filters-slide swiper-slide">
                    <button class="blog-filter__chip blog-filter__chip--active" type="button" data-filter="all" aria-pressed="true"><span>Todas</span>
                    </button>
                  </div>
                  <div class="blog-filter__filters-slide swiper-slide">
                    <button class="blog-filter__chip" type="button" data-filter="rutina" aria-pressed="false">
                      <svg class="blog-filter__chip-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                        <circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="1.6"></circle>
                        <path d="M12 7v5l3 2" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"></path>
                      </svg><span>Rutina</span>
                    </button>
                  </div>
                  <div class="blog-filter__filters-slide swiper-slide">
                    <button class="blog-filter__chip" type="button" data-filter="descanso" aria-pressed="false">
                      <svg class="blog-filter__chip-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                        <path d="M18 14.5A7.5 7.5 0 0 1 9.5 6 6.5 6.5 0 1 0 18 14.5Z" stroke="currentColor" stroke-width="1.6" stroke-linejoin="round"></path>
                      </svg><span>Descanso</span>
                    </button>
                  </div>
                  <div class="blog-filter__filters-slide swiper-slide">
                    <button class="blog-filter__chip" type="button" data-filter="ingredientes" aria-pressed="false">
                      <svg class="blog-filter__chip-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                        <path d="M5 19c4-1 6-5 7-10 1 5 3 9 7 10-3 2-11 2-14 0Z" stroke="currentColor" stroke-width="1.6" stroke-linejoin="round"></path>
                        <path d="M12 9V4" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"></path>
                      </svg><span>Ingredientes</span>
                    </button>
                  </div>
                  <div class="blog-filter__filters-slide swiper-slide">
                    <button class="blog-filter__chip" type="button" data-filter="hongos" aria-pressed="false">
                      <svg class="blog-filter__chip-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                        <path d="M4 12c0-4.4 3.6-8 8-8s8 3.6 8 8H4Z" stroke="currentColor" stroke-width="1.6" stroke-linejoin="round"></path>
                        <path d="M10 12v6a2 2 0 0 0 4 0v-6" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"></path>
                      </svg><span>Hongos</span>
                    </button>
                  </div>
                  <div class="blog-filter__filters-slide swiper-slide">
                    <button class="blog-filter__chip" type="button" data-filter="bienestar" aria-pressed="false">
                      <svg class="blog-filter__chip-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                        <path d="M12 19s-7-4.4-7-9a3.8 3.8 0 0 1 7-2 3.8 3.8 0 0 1 7 2c0 4.6-7 9-7 9Z" stroke="currentColor" stroke-width="1.6" stroke-linejoin="round"></path>
                      </svg><span>Bienestar diario</span>
                    </button>
                  </div>
                </div>
              </div>
            </div>
            <div class="blog-filter__grid" data-blog-grid>
              <article class="blog-filter__card" data-category="rutina"><a class="blog-filter__media" href="#"><img class="blog-filter__image" src="https://kikeestradadev.github.io/recupepluslp/assets/item_filtro.png" alt="" width="400" height="260" loading="lazy"></a>
                <p class="blog-filter__category">Rutina</p>
                <h3 class="blog-filter__name"><a href="#">¿Cómo elegir tu Recupé Plus según tu rutina?</a></h3><a class="blog-filter__link" href="#"><span>Leer guía</span><span class="blog-filter__link-arrow" aria-hidden="true">→</span></a>
              </article>
              <article class="blog-filter__card" data-category="hongos"><a class="blog-filter__media" href="#"><img class="blog-filter__image" src="https://kikeestradadev.github.io/recupepluslp/assets/item_filtro.png" alt="" width="400" height="260" loading="lazy"></a>
                <p class="blog-filter__category">Hongos</p>
                <h3 class="blog-filter__name"><a href="#">Hongos medicinales: qué son y cómo sumarlos a tu día.</a></h3><a class="blog-filter__link" href="#"><span>Leer guía</span><span class="blog-filter__link-arrow" aria-hidden="true">→</span></a>
              </article>
              <article class="blog-filter__card" data-category="descanso"><a class="blog-filter__media" href="#"><img class="blog-filter__image" src="https://kikeestradadev.github.io/recupepluslp/assets/item_filtro.png" alt="" width="400" height="260" loading="lazy"></a>
                <p class="blog-filter__category">Descanso</p>
                <h3 class="blog-filter__name"><a href="#">Cómo crear un ritual simple antes de dormir.</a></h3><a class="blog-filter__link" href="#"><span>Leer guía</span><span class="blog-filter__link-arrow" aria-hidden="true">→</span></a>
              </article>
              <article class="blog-filter__card" data-category="ingredientes"><a class="blog-filter__media" href="#"><img class="blog-filter__image" src="https://kikeestradadev.github.io/recupepluslp/assets/item_filtro.png" alt="" width="400" height="260" loading="lazy"></a>
                <p class="blog-filter__category">Ingredientes</p>
                <h3 class="blog-filter__name"><a href="#">Vitamina C, magnesio y antioxidantes: cuándo elegir cada uno.</a></h3><a class="blog-filter__link" href="#"><span>Leer guía</span><span class="blog-filter__link-arrow" aria-hidden="true">→</span></a>
              </article>
              <article class="blog-filter__card" data-category="rutina"><a class="blog-filter__media" href="#"><img class="blog-filter__image" src="https://kikeestradadev.github.io/recupepluslp/assets/item_filtro.png" alt="" width="400" height="260" loading="lazy"></a>
                <p class="blog-filter__category">Rutina</p>
                <h3 class="blog-filter__name"><a href="#">Rutinas cortas para días intensos.</a></h3><a class="blog-filter__link" href="#"><span>Leer guía</span><span class="blog-filter__link-arrow" aria-hidden="true">→</span></a>
              </article>
              <article class="blog-filter__card" data-category="hongos"><a class="blog-filter__media" href="#"><img class="blog-filter__image" src="https://kikeestradadev.github.io/recupepluslp/assets/item_filtro.png" alt="" width="400" height="260" loading="lazy"></a>
                <p class="blog-filter__category">Hongos</p>
                <h3 class="blog-filter__name"><a href="#">Qué hongos funcionan mejor según tu objetivo.</a></h3><a class="blog-filter__link" href="#"><span>Leer guía</span><span class="blog-filter__link-arrow" aria-hidden="true">→</span></a>
              </article>
              <article class="blog-filter__card" data-category="descanso"><a class="blog-filter__media" href="#"><img class="blog-filter__image" src="https://kikeestradadev.github.io/recupepluslp/assets/item_filtro.png" alt="" width="400" height="260" loading="lazy"></a>
                <p class="blog-filter__category">Descanso</p>
                <h3 class="blog-filter__name"><a href="#">Rituales de descanso que sí puedes mantener.</a></h3><a class="blog-filter__link" href="#"><span>Leer guía</span><span class="blog-filter__link-arrow" aria-hidden="true">→</span></a>
              </article>
              <article class="blog-filter__card" data-category="ingredientes"><a class="blog-filter__media" href="#"><img class="blog-filter__image" src="https://kikeestradadev.github.io/recupepluslp/assets/item_filtro.png" alt="" width="400" height="260" loading="lazy"></a>
                <p class="blog-filter__category">Ingredientes</p>
                <h3 class="blog-filter__name"><a href="#">Cómo leer una etiqueta de suplementos sin volverte loco.</a></h3><a class="blog-filter__link" href="#"><span>Leer guía</span><span class="blog-filter__link-arrow" aria-hidden="true">→</span></a>
              </article>
              <article class="blog-filter__card" data-category="bienestar"><a class="blog-filter__media" href="#"><img class="blog-filter__image" src="https://kikeestradadev.github.io/recupepluslp/assets/item_filtro.png" alt="" width="400" height="260" loading="lazy"></a>
                <p class="blog-filter__category">Bienestar diario</p>
                <h3 class="blog-filter__name"><a href="#">Hábitos simples para recuperar energía cada mañana.</a></h3><a class="blog-filter__link" href="#"><span>Leer guía</span><span class="blog-filter__link-arrow" aria-hidden="true">→</span></a>
              </article>
              <article class="blog-filter__card" data-category="rutina"><a class="blog-filter__media" href="#"><img class="blog-filter__image" src="https://kikeestradadev.github.io/recupepluslp/assets/item_filtro.png" alt="" width="400" height="260" loading="lazy"></a>
                <p class="blog-filter__category">Rutina</p>
                <h3 class="blog-filter__name"><a href="#">Cómo armar tu kit de recuperación para la semana.</a></h3><a class="blog-filter__link" href="#"><span>Leer guía</span><span class="blog-filter__link-arrow" aria-hidden="true">→</span></a>
              </article>
              <article class="blog-filter__card" data-category="bienestar"><a class="blog-filter__media" href="#"><img class="blog-filter__image" src="https://kikeestradadev.github.io/recupepluslp/assets/item_filtro.png" alt="" width="400" height="260" loading="lazy"></a>
                <p class="blog-filter__category">Bienestar diario</p>
                <h3 class="blog-filter__name"><a href="#">Pequeños cambios que mejoran tu bienestar diario.</a></h3><a class="blog-filter__link" href="#"><span>Leer guía</span><span class="blog-filter__link-arrow" aria-hidden="true">→</span></a>
              </article>
              <article class="blog-filter__card" data-category="hongos"><a class="blog-filter__media" href="#"><img class="blog-filter__image" src="https://kikeestradadev.github.io/recupepluslp/assets/item_filtro.png" alt="" width="400" height="260" loading="lazy"></a>
                <p class="blog-filter__category">Hongos</p>
                <h3 class="blog-filter__name"><a href="#">Guía rápida de hongos funcionales para principiantes.</a></h3><a class="blog-filter__link" href="#"><span>Leer guía</span><span class="blog-filter__link-arrow" aria-hidden="true">→</span></a>
              </article>
            </div>
            <div class="blog-filter__mobile">
              <div class="blog-filter__swiper swiper" data-blog-swiper>
                <div class="blog-filter__wrapper swiper-wrapper" data-blog-mobile-track>
                  <article class="blog-filter__slide swiper-slide" data-category="rutina">
                    <div class="blog-filter__card"><a class="blog-filter__media" href="#"><img class="blog-filter__image" src="https://kikeestradadev.github.io/recupepluslp/assets/item_filtro.png" alt="" width="400" height="260" loading="lazy"></a>
                      <p class="blog-filter__category">Rutina</p>
                      <h3 class="blog-filter__name"><a href="#">¿Cómo elegir tu Recupé Plus según tu rutina?</a></h3><a class="blog-filter__link" href="#"><span>Leer guía</span><span class="blog-filter__link-arrow" aria-hidden="true">→</span></a>
                    </div>
                  </article>
                  <article class="blog-filter__slide swiper-slide" data-category="hongos">
                    <div class="blog-filter__card"><a class="blog-filter__media" href="#"><img class="blog-filter__image" src="https://kikeestradadev.github.io/recupepluslp/assets/item_filtro.png" alt="" width="400" height="260" loading="lazy"></a>
                      <p class="blog-filter__category">Hongos</p>
                      <h3 class="blog-filter__name"><a href="#">Hongos medicinales: qué son y cómo sumarlos a tu día.</a></h3><a class="blog-filter__link" href="#"><span>Leer guía</span><span class="blog-filter__link-arrow" aria-hidden="true">→</span></a>
                    </div>
                  </article>
                  <article class="blog-filter__slide swiper-slide" data-category="descanso">
                    <div class="blog-filter__card"><a class="blog-filter__media" href="#"><img class="blog-filter__image" src="https://kikeestradadev.github.io/recupepluslp/assets/item_filtro.png" alt="" width="400" height="260" loading="lazy"></a>
                      <p class="blog-filter__category">Descanso</p>
                      <h3 class="blog-filter__name"><a href="#">Cómo crear un ritual simple antes de dormir.</a></h3><a class="blog-filter__link" href="#"><span>Leer guía</span><span class="blog-filter__link-arrow" aria-hidden="true">→</span></a>
                    </div>
                  </article>
                  <article class="blog-filter__slide swiper-slide" data-category="ingredientes">
                    <div class="blog-filter__card"><a class="blog-filter__media" href="#"><img class="blog-filter__image" src="https://kikeestradadev.github.io/recupepluslp/assets/item_filtro.png" alt="" width="400" height="260" loading="lazy"></a>
                      <p class="blog-filter__category">Ingredientes</p>
                      <h3 class="blog-filter__name"><a href="#">Vitamina C, magnesio y antioxidantes: cuándo elegir cada uno.</a></h3><a class="blog-filter__link" href="#"><span>Leer guía</span><span class="blog-filter__link-arrow" aria-hidden="true">→</span></a>
                    </div>
                  </article>
                  <article class="blog-filter__slide swiper-slide" data-category="rutina">
                    <div class="blog-filter__card"><a class="blog-filter__media" href="#"><img class="blog-filter__image" src="https://kikeestradadev.github.io/recupepluslp/assets/item_filtro.png" alt="" width="400" height="260" loading="lazy"></a>
                      <p class="blog-filter__category">Rutina</p>
                      <h3 class="blog-filter__name"><a href="#">Rutinas cortas para días intensos.</a></h3><a class="blog-filter__link" href="#"><span>Leer guía</span><span class="blog-filter__link-arrow" aria-hidden="true">→</span></a>
                    </div>
                  </article>
                  <article class="blog-filter__slide swiper-slide" data-category="hongos">
                    <div class="blog-filter__card"><a class="blog-filter__media" href="#"><img class="blog-filter__image" src="https://kikeestradadev.github.io/recupepluslp/assets/item_filtro.png" alt="" width="400" height="260" loading="lazy"></a>
                      <p class="blog-filter__category">Hongos</p>
                      <h3 class="blog-filter__name"><a href="#">Qué hongos funcionan mejor según tu objetivo.</a></h3><a class="blog-filter__link" href="#"><span>Leer guía</span><span class="blog-filter__link-arrow" aria-hidden="true">→</span></a>
                    </div>
                  </article>
                  <article class="blog-filter__slide swiper-slide" data-category="descanso">
                    <div class="blog-filter__card"><a class="blog-filter__media" href="#"><img class="blog-filter__image" src="https://kikeestradadev.github.io/recupepluslp/assets/item_filtro.png" alt="" width="400" height="260" loading="lazy"></a>
                      <p class="blog-filter__category">Descanso</p>
                      <h3 class="blog-filter__name"><a href="#">Rituales de descanso que sí puedes mantener.</a></h3><a class="blog-filter__link" href="#"><span>Leer guía</span><span class="blog-filter__link-arrow" aria-hidden="true">→</span></a>
                    </div>
                  </article>
                  <article class="blog-filter__slide swiper-slide" data-category="ingredientes">
                    <div class="blog-filter__card"><a class="blog-filter__media" href="#"><img class="blog-filter__image" src="https://kikeestradadev.github.io/recupepluslp/assets/item_filtro.png" alt="" width="400" height="260" loading="lazy"></a>
                      <p class="blog-filter__category">Ingredientes</p>
                      <h3 class="blog-filter__name"><a href="#">Cómo leer una etiqueta de suplementos sin volverte loco.</a></h3><a class="blog-filter__link" href="#"><span>Leer guía</span><span class="blog-filter__link-arrow" aria-hidden="true">→</span></a>
                    </div>
                  </article>
                  <article class="blog-filter__slide swiper-slide" data-category="bienestar">
                    <div class="blog-filter__card"><a class="blog-filter__media" href="#"><img class="blog-filter__image" src="https://kikeestradadev.github.io/recupepluslp/assets/item_filtro.png" alt="" width="400" height="260" loading="lazy"></a>
                      <p class="blog-filter__category">Bienestar diario</p>
                      <h3 class="blog-filter__name"><a href="#">Hábitos simples para recuperar energía cada mañana.</a></h3><a class="blog-filter__link" href="#"><span>Leer guía</span><span class="blog-filter__link-arrow" aria-hidden="true">→</span></a>
                    </div>
                  </article>
                  <article class="blog-filter__slide swiper-slide" data-category="rutina">
                    <div class="blog-filter__card"><a class="blog-filter__media" href="#"><img class="blog-filter__image" src="https://kikeestradadev.github.io/recupepluslp/assets/item_filtro.png" alt="" width="400" height="260" loading="lazy"></a>
                      <p class="blog-filter__category">Rutina</p>
                      <h3 class="blog-filter__name"><a href="#">Cómo armar tu kit de recuperación para la semana.</a></h3><a class="blog-filter__link" href="#"><span>Leer guía</span><span class="blog-filter__link-arrow" aria-hidden="true">→</span></a>
                    </div>
                  </article>
                  <article class="blog-filter__slide swiper-slide" data-category="bienestar">
                    <div class="blog-filter__card"><a class="blog-filter__media" href="#"><img class="blog-filter__image" src="https://kikeestradadev.github.io/recupepluslp/assets/item_filtro.png" alt="" width="400" height="260" loading="lazy"></a>
                      <p class="blog-filter__category">Bienestar diario</p>
                      <h3 class="blog-filter__name"><a href="#">Pequeños cambios que mejoran tu bienestar diario.</a></h3><a class="blog-filter__link" href="#"><span>Leer guía</span><span class="blog-filter__link-arrow" aria-hidden="true">→</span></a>
                    </div>
                  </article>
                  <article class="blog-filter__slide swiper-slide" data-category="hongos">
                    <div class="blog-filter__card"><a class="blog-filter__media" href="#"><img class="blog-filter__image" src="https://kikeestradadev.github.io/recupepluslp/assets/item_filtro.png" alt="" width="400" height="260" loading="lazy"></a>
                      <p class="blog-filter__category">Hongos</p>
                      <h3 class="blog-filter__name"><a href="#">Guía rápida de hongos funcionales para principiantes.</a></h3><a class="blog-filter__link" href="#"><span>Leer guía</span><span class="blog-filter__link-arrow" aria-hidden="true">→</span></a>
                    </div>
                  </article>
                </div>
              </div>
              <div class="blog-filter__bullets swiper-pagination"></div>
            </div>
            <nav class="blog-filter__pagination" data-blog-pagination aria-label="Paginación del blog"></nav>
          </div>
        </div>
      </section>
