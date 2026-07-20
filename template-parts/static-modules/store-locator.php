<?php
/**
 * Static module: store-locator
 *
 * @package wpboilerplate
 */
?>
<section class="store-locator" data-store-locator>
        <div class="main-container store-locator__shell">
          <div class="container store-locator__container store-locator__inner">
            <header class="store-locator__header">
              <h2 class="store-locator__title">Encontranos cerca de vos</h2>
              <p class="store-locator__subtitle">Buscá tu tienda más cercana.</p>
            </header>
            <div class="store-locator__layout">
              <div class="store-locator__panel">
                <form class="store-locator__search" action="#" method="get" data-store-locator-search>
                  <input class="store-locator__input" type="search" name="q" placeholder="Ingresá tu ubicación o código postal" aria-label="Buscar ubicación">
                  <button class="store-locator__search-btn" type="submit" aria-label="Buscar">
                    <svg class="store-locator__search-icon" width="18" height="18" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                      <circle cx="11" cy="11" r="7" stroke="currentColor" stroke-width="1.8"></circle>
                      <path d="M16.5 16.5L21 21" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"></path>
                    </svg>
                  </button>
                </form>
                <p class="visually-hidden" aria-live="polite" data-store-locator-status></p>
                <ul class="store-locator__list">
                  <li class="store-locator__item store-locator__item--active" data-store="0">
                    <button class="store-locator__item-btn" type="button" aria-pressed="true"><span class="store-locator__item-icon" aria-hidden="true">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none">
                          <path d="M12 21s7-5.2 7-11a7 7 0 1 0-14 0c0 5.8 7 11 7 11z" stroke="currentColor" stroke-width="1.6"></path>
                          <circle cx="12" cy="10" r="2.4" stroke="currentColor" stroke-width="1.6"></circle>
                        </svg></span><span class="store-locator__item-content"><span class="store-locator__item-name">Auto Mercado</span><span class="store-locator__item-place">Escazú, San José</span></span><span class="store-locator__item-meta"><span class="store-locator__item-distance">1.2 km</span><span class="store-locator__item-arrow" aria-hidden="true">→</span></span></button>
                  </li>
                  <li class="store-locator__item" data-store="1">
                    <button class="store-locator__item-btn" type="button" aria-pressed="false"><span class="store-locator__item-icon" aria-hidden="true">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none">
                          <path d="M12 21s7-5.2 7-11a7 7 0 1 0-14 0c0 5.8 7 11 7 11z" stroke="currentColor" stroke-width="1.6"></path>
                          <circle cx="12" cy="10" r="2.4" stroke="currentColor" stroke-width="1.6"></circle>
                        </svg></span><span class="store-locator__item-content"><span class="store-locator__item-name">GNC Multiplaza</span><span class="store-locator__item-place">Escazú, San José</span></span><span class="store-locator__item-meta"><span class="store-locator__item-distance">2.7 km</span><span class="store-locator__item-arrow" aria-hidden="true">→</span></span></button>
                  </li>
                  <li class="store-locator__item" data-store="2">
                    <button class="store-locator__item-btn" type="button" aria-pressed="false"><span class="store-locator__item-icon" aria-hidden="true">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none">
                          <path d="M12 21s7-5.2 7-11a7 7 0 1 0-14 0c0 5.8 7 11 7 11z" stroke="currentColor" stroke-width="1.6"></path>
                          <circle cx="12" cy="10" r="2.4" stroke="currentColor" stroke-width="1.6"></circle>
                        </svg></span><span class="store-locator__item-content"><span class="store-locator__item-name">Macrobióticas</span><span class="store-locator__item-place">Curridabat, San José</span></span><span class="store-locator__item-meta"><span class="store-locator__item-distance">3.1 km</span><span class="store-locator__item-arrow" aria-hidden="true">→</span></span></button>
                  </li>
                </ul><a class="store-locator__more" href="#">Ver más tiendas →</a>
              </div>
              <div class="store-locator__map" aria-label="Mapa de tiendas">
                <iframe class="store-locator__map-frame" title="Mapa de tiendas en San José" loading="lazy" referrerpolicy="no-referrer-when-downgrade" src="https://www.openstreetmap.org/export/embed.html?bbox=-84.170%2C9.900%2C-84.000%2C9.980&amp;layer=mapnik"></iframe>
                <div class="store-locator__pins" aria-hidden="true"><span class="store-locator__pin store-locator__pin--green store-locator__pin--active" data-store="0" style="top: 42%; left: 28%;"></span><span class="store-locator__pin store-locator__pin--orange" data-store="1" style="top: 48%; left: 46%;"></span><span class="store-locator__pin store-locator__pin--purple" data-store="2" style="top: 55%; left: 68%;"></span></div>
              </div>
            </div>
          </div>
        </div>
      </section>
