<?php
/**
 * Static module: product-showcase
 *
 * @package wpboilerplate
 */
?>
<section class="product-showcase" data-product-showcase>
        <div class="main-container product-showcase__shell">
          <div class="container product-showcase__container product-showcase__inner">
            <nav class="product-showcase__breadcrumbs" aria-label="Miga de pan">
              <ol class="product-showcase__crumbs">
                <li class="product-showcase__crumb"><a class="product-showcase__crumb-link" href="./">Inicio</a><span class="product-showcase__crumb-sep" aria-hidden="true">/</span>
                </li>
                <li class="product-showcase__crumb"><a class="product-showcase__crumb-link" href="suplementos.html#gomitas">Gomitas</a><span class="product-showcase__crumb-sep" aria-hidden="true">/</span>
                </li>
                <li class="product-showcase__crumb"><span class="product-showcase__crumb-current" aria-current="page">Vitamin</span>
                </li>
              </ol>
            </nav>
            <div class="product-showcase__layout">
              <div class="product-showcase__gallery">
                <div class="product-showcase__thumbs" role="list">
                  <button class="product-showcase__thumb product-showcase__thumb--active" type="button" role="listitem" data-product-thumb data-src="https://kikeestradadev.github.io/recupepluslp/assets/drive_24_julio/dos_formas/gomitas/individuales/vitamin.webp" aria-label="Ver Vitamin - imagen 1" aria-pressed="true"><img class="product-showcase__thumb-image" src="https://kikeestradadev.github.io/recupepluslp/assets/drive_24_julio/dos_formas/gomitas/individuales/vitamin.webp" alt="" width="80" height="80" loading="lazy"></button>
                  <button class="product-showcase__thumb" type="button" role="listitem" data-product-thumb data-src="https://kikeestradadev.github.io/recupepluslp/assets/drive_24_julio/dos_formas/gomitas/gomitas_1.webp" aria-label="Ver Vitamin - imagen 2" aria-pressed="false"><img class="product-showcase__thumb-image" src="https://kikeestradadev.github.io/recupepluslp/assets/drive_24_julio/dos_formas/gomitas/gomitas_1.webp" alt="" width="80" height="80" loading="lazy"></button>
                </div>
                <div class="product-showcase__stage" data-product-zoom><img class="product-showcase__stage-image" src="https://kikeestradadev.github.io/recupepluslp/assets/drive_24_julio/dos_formas/gomitas/individuales/vitamin.webp" alt="Vitamin - imagen 1" width="720" height="720" data-product-stage><span class="product-showcase__zoom-hint" aria-hidden="true">Ampliar</span></div>
              </div>
              <div class="product-showcase__summary">
                <h1 class="product-showcase__title">Vitamin</h1>
                <p class="product-showcase__price"><span class="product-showcase__price-value">₡8 900</span><span class="product-showcase__price-note">IVA incluido</span></p>
                <div class="product-showcase__excerpt">
                  <p>Suplemento alimenticio en gomitas a base de mora y vitamina C.</p>
                  <p>Apoya tus defensas todos los días con una fórmula práctica y deliciosa. Ideal para sumar bienestar a tu rutina sin complicarte.</p>
                </div>
                <form class="product-showcase__cart" action="#" method="post" data-product-cart>
                  <div class="product-showcase__qty" data-product-qty>
                    <button class="product-showcase__qty-btn" type="button" data-product-qty-dec aria-label="Disminuir cantidad">−</button>
                    <input class="product-showcase__qty-input" type="number" name="quantity" min="1" max="20" value="1" inputmode="numeric" aria-label="Cantidad" data-product-qty-input>
                    <button class="product-showcase__qty-btn" type="button" data-product-qty-inc aria-label="Aumentar cantidad">+</button>
                  </div>
                  <button class="product-showcase__add" type="submit">Añadir al carrito</button>
                </form>
                <button class="product-showcase__wishlist" type="button">Agregar a lista de deseos</button>
                <p class="product-showcase__meta"><span>Categoría:</span><a href="suplementos.html#gomitas">Gomitas Funcionales</a></p>
              </div>
            </div>
            <div class="product-showcase__tabs" data-product-tabs>
              <div class="product-showcase__tabs-bar">
                <button class="product-showcase__tabs-nav product-showcase__tabs-nav--prev" type="button" aria-label="Tabs anteriores"></button>
                <button class="product-showcase__tabs-nav product-showcase__tabs-nav--next" type="button" aria-label="Tabs siguientes"></button>
                <div class="product-showcase__tabs-fade product-showcase__tabs-fade--start" aria-hidden="true"></div>
                <div class="product-showcase__tabs-fade product-showcase__tabs-fade--end" aria-hidden="true"></div>
                <div class="product-showcase__tabs-swiper swiper" data-product-tabs-swiper>
                  <div class="product-showcase__tabs-wrapper swiper-wrapper" role="tablist" aria-label="Información del producto">
                    <div class="product-showcase__tabs-slide swiper-slide">
                      <button class="product-showcase__tab product-showcase__tab--active" type="button" role="tab" id="product-tab-descripcion" aria-controls="product-panel-descripcion" aria-selected="true" tabindex="0" data-product-tab="descripcion">Descripción</button>
                    </div>
                    <div class="product-showcase__tabs-slide swiper-slide">
                      <button class="product-showcase__tab" type="button" role="tab" id="product-tab-adicional" aria-controls="product-panel-adicional" aria-selected="false" tabindex="-1" data-product-tab="adicional">Información adicional</button>
                    </div>
                    <div class="product-showcase__tabs-slide swiper-slide">
                      <button class="product-showcase__tab" type="button" role="tab" id="product-tab-valoraciones" aria-controls="product-panel-valoraciones" aria-selected="false" tabindex="-1" data-product-tab="valoraciones">Valoraciones (0)</button>
                    </div>
                  </div>
                </div>
              </div>
              <div class="product-showcase__panel product-showcase__panel--active" role="tabpanel" id="product-panel-descripcion" aria-labelledby="product-tab-descripcion" data-product-panel="descripcion">
                <h2 class="product-showcase__panel-title">RECUPÉ PLUS VITAMIN</h2>
                <p class="product-showcase__panel-text">Nuestras gomitas Vitamin combinan el sabor de la mora con vitamina C para acompañar tu sistema inmune de forma simple y cotidiana.</p>
                <p class="product-showcase__panel-text">Pensadas para quienes buscan un hábito fácil: una gomita rica, práctica y con activos pensados para tu bienestar diario.</p>
                <p class="product-showcase__panel-text">Beneficios de Vitamin en tu rutina:</p>
                <ol class="product-showcase__benefits">
                  <li class="product-showcase__benefit">
                    <h3 class="product-showcase__benefit-title">1. Defensas:</h3>
                    <p class="product-showcase__benefit-text">La vitamina C contribuye al funcionamiento normal del sistema inmunológico y ayuda a proteger las células del estrés oxidativo.</p>
                  </li>
                  <li class="product-showcase__benefit">
                    <h3 class="product-showcase__benefit-title">2. Antioxidantes:</h3>
                    <p class="product-showcase__benefit-text">La mora aporta antocianinas y otros compuestos que acompañan una rutina enfocada en el bienestar celular.</p>
                  </li>
                  <li class="product-showcase__benefit">
                    <h3 class="product-showcase__benefit-title">3. Hábitos simples:</h3>
                    <p class="product-showcase__benefit-text">Formato gomita para sumar constancia sin fricción: ideal para la oficina, el gym o el día a día.</p>
                  </li>
                  <li class="product-showcase__benefit">
                    <h3 class="product-showcase__benefit-title">4. Bienestar diario:</h3>
                    <p class="product-showcase__benefit-text">Una opción práctica para complementar alimentación y estilo de vida activos.</p>
                  </li>
                </ol>
                <p class="product-showcase__panel-text">Al integrar Vitamin en tu rutina, sumás un apoyo concreto a tus defensas con un producto pensado para disfrutarse todos los días.</p>
              </div>
              <div class="product-showcase__panel" role="tabpanel" id="product-panel-adicional" aria-labelledby="product-tab-adicional" hidden data-product-panel="adicional">
                <table class="product-showcase__table">
                  <tbody>
                    <tr>
                      <th scope="row">Marca</th>
                      <td>Recupé Plus</td>
                    </tr>
                    <tr>
                      <th scope="row">Formato</th>
                      <td>Gomitas</td>
                    </tr>
                    <tr>
                      <th scope="row">Activos</th>
                      <td>Mora, Vitamina C</td>
                    </tr>
                  </tbody>
                </table>
              </div>
              <div class="product-showcase__panel" role="tabpanel" id="product-panel-valoraciones" aria-labelledby="product-tab-valoraciones" hidden data-product-panel="valoraciones">
                <h2 class="product-showcase__panel-title">Valoraciones</h2>
                <p class="product-showcase__panel-text">No hay valoraciones aún.</p>
                <p class="product-showcase__panel-cta">Sé el primero en valorar “Vitamin”</p>
              </div>
            </div>
            <section class="product-showcase__related">
              <h2 class="product-showcase__related-title">Productos relacionados</h2>
              <div class="product-showcase__related-grid">
                <article class="product-showcase__related-card"><a class="product-showcase__related-media" href="product.html"><img class="product-showcase__related-image" src="https://kikeestradadev.github.io/recupepluslp/assets/drive_24_julio/dos_formas/capsulas/individuales/activity.webp" alt="Activity" width="280" height="280" loading="lazy"></a>
                  <p class="product-showcase__related-category">Hongos Medicinales</p>
                  <h3 class="product-showcase__related-name"><a href="product.html">Activity</a></h3>
                  <p class="product-showcase__related-price"><span>₡25 000</span><span class="product-showcase__related-price-note">IVA incluido</span></p>
                  <p class="product-showcase__related-text">Para activar tu energía antes de moverte.</p><a class="product-showcase__related-link" href="product.html">Ver producto →</a>
                </article>
                <article class="product-showcase__related-card"><a class="product-showcase__related-media" href="product.html"><img class="product-showcase__related-image" src="https://kikeestradadev.github.io/recupepluslp/assets/drive_24_julio/dos_formas/capsulas/individuales/momento.webp" alt="Memento" width="280" height="280" loading="lazy"></a>
                  <p class="product-showcase__related-category">Hongos Medicinales</p>
                  <h3 class="product-showcase__related-name"><a href="product.html">Memento</a></h3>
                  <p class="product-showcase__related-price"><span>₡25 000</span><span class="product-showcase__related-price-note">IVA incluido</span></p>
                  <p class="product-showcase__related-text">Para acompañar tus momentos de enfoque.</p><a class="product-showcase__related-link" href="product.html">Ver producto →</a>
                </article>
                <article class="product-showcase__related-card"><a class="product-showcase__related-media" href="product.html"><img class="product-showcase__related-image" src="https://kikeestradadev.github.io/recupepluslp/assets/drive_24_julio/dos_formas/capsulas/individuales/recovery.webp" alt="Recovery" width="280" height="280" loading="lazy"></a>
                  <p class="product-showcase__related-category">Hongos Medicinales</p>
                  <h3 class="product-showcase__related-name"><a href="product.html">Recovery</a></h3>
                  <p class="product-showcase__related-price"><span>₡25 000</span><span class="product-showcase__related-price-note">IVA incluido</span></p>
                  <p class="product-showcase__related-text">Para acompañar tu recuperación después de hacer ejercicio.</p><a class="product-showcase__related-link" href="product.html">Ver producto →</a>
                </article>
              </div>
            </section>
          </div>
        </div>
      </section>
