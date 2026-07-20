<?php
/**
 * Template part for displaying the main menu
 *
 * @package wpboilerplate
 */
?>
<header class="main-header">
	<nav class="main-menu" aria-label="Navegación principal" data-main-menu="">
		<div class="main-container main-menu__shell">
			<div class="container main-menu__container main-menu__inner">
				<a class="main-menu__logo" href="#">
					<img class="main-menu__logo-img" src="https://kikeestradadev.github.io/recupepluslp/assets/logonegro.png" alt="Recupé Plus" width="120" height="68">
				</a>
				<div class="main-menu__panel" data-main-menu-panel="" id="main-menu-panel-1" aria-hidden="false">
					<ul class="main-menu__list">
						<li class="main-menu__item"><a class="main-menu__link" href="#">Inicio</a></li>
						<li class="main-menu__item main-menu__item--has-children">
							<a class="main-menu__link" href="#" aria-expanded="false" data-main-menu-submenu-toggle="" aria-controls="main-menu-submenu-1-1">Suplementos</a>
							<ul class="main-menu__sub-menu" data-main-menu-submenu="" id="main-menu-submenu-1-1">
								<li class="main-menu__item"><a class="main-menu__link" href="#">lorem Ipsum</a></li>
								<li class="main-menu__item"><a class="main-menu__link" href="#">lorem Ipsum</a></li>
								<li class="main-menu__item"><a class="main-menu__link" href="#">lorem Ipsum</a></li>
							</ul>
						</li>
						<li class="main-menu__item"><a class="main-menu__link" href="#">Donde Comprar</a></li>
						<li class="main-menu__item"><a class="main-menu__link" href="#">Guias</a></li>
						<li class="main-menu__item"><a class="main-menu__link" href="#">Nosotros</a></li>
						<li class="main-menu__item"><a class="main-menu__link" href="#">Contacto</a></li>
					</ul>
				</div>
				<div class="main-menu__actions">
					<a class="main-menu__cta" href="#" aria-label="Comprar ahora">
						<span class="main-menu__cta-short">Comprar</span>
						<span class="main-menu__cta-full">Comprar ahora</span>
					</a>
					<a class="main-menu__cart" href="#" aria-label="Carrito de compras">
						<svg class="main-menu__cart-icon" width="24" height="24" viewBox="0 0 24 24" fill="none" aria-hidden="true">
							<path d="M6 8h12l-1 12H7L6 8z" stroke="currentColor" stroke-width="1.5" stroke-linejoin="round"></path>
							<path d="M9 8V7a3 3 0 0 1 6 0v1" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"></path>
						</svg>
					</a>
					<button class="main-menu__language" type="button" data-main-menu-language="" data-locale="es" data-es-href="#" data-en-href="#" data-es-label="Español" data-en-label="English" aria-label="Cambiar a English">
						<span class="main-menu__language-flag" data-flag="es" aria-hidden="true">
							<svg class="main-menu__language-flag-img" width="24" height="18" viewBox="0 0 24 18" aria-hidden="true">
								<rect width="24" height="18" fill="#c60b1e"></rect>
								<rect y="4.5" width="24" height="9" fill="#ffc400"></rect>
							</svg>
						</span>
						<span class="main-menu__language-flag" data-flag="en" hidden="" aria-hidden="true">
							<svg class="main-menu__language-flag-img" width="24" height="18" viewBox="0 0 24 18" aria-hidden="true">
								<rect width="24" height="18" fill="#b22234"></rect>
								<rect y="1.385" width="24" height="1.385" fill="#fff"></rect>
								<rect y="4.154" width="24" height="1.385" fill="#fff"></rect>
								<rect y="6.923" width="24" height="1.385" fill="#fff"></rect>
								<rect y="9.692" width="24" height="1.385" fill="#fff"></rect>
								<rect y="12.462" width="24" height="1.385" fill="#fff"></rect>
								<rect y="15.231" width="24" height="1.385" fill="#fff"></rect>
								<rect width="9.6" height="9.69" fill="#3c3b6e"></rect>
							</svg>
						</span>
					</button>
					<button class="main-menu__hamburger" type="button" aria-label="Abrir menú" aria-expanded="false" data-main-menu-toggle="" aria-controls="main-menu-panel-1">
						<span class="main-menu__hamburger-line"></span>
						<span class="main-menu__hamburger-line"></span>
						<span class="main-menu__hamburger-line"></span>
					</button>
				</div>
			</div>
		</div>
	</nav>
</header>
