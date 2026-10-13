<?php
/**
 * Static module: contact-form
 *
 * @package wpboilerplate
 */
?>
<section class="contact-form" aria-label="Contacto">
        <div class="main-container contact-form__shell">
          <div class="container contact-form__container">
            <div class="contact-form__grid">
              <div class="contact-form__column contact-form__column--form">
                <h1 class="contact-form__title">Contáctenos</h1>
                <form class="contact-form__form" action="#" method="post">
                  <div class="contact-form__field">
                    <label class="contact-form__label visually-hidden" for="contact-name">Nombre</label>
                    <input class="contact-form__control" id="contact-name" type="text" name="name" placeholder="Nombre" required>
                  </div>
                  <div class="contact-form__field">
                    <label class="contact-form__label visually-hidden" for="contact-email">Email</label>
                    <input class="contact-form__control" id="contact-email" type="email" name="email" placeholder="Email" required>
                  </div>
                  <div class="contact-form__field">
                    <label class="contact-form__label visually-hidden" for="contact-phone">Teléfono</label>
                    <input class="contact-form__control" id="contact-phone" type="tel" name="phone" placeholder="Teléfono">
                  </div>
                  <div class="contact-form__field">
                    <label class="contact-form__label visually-hidden" for="contact-message">Comentario</label>
                    <textarea class="contact-form__control contact-form__control--textarea" id="contact-message" name="message" placeholder="Comentario" rows="5" required></textarea>
                  </div>
                  <button class="contact-form__submit" type="submit">Enviar</button>
                </form>
              </div>
              <div class="contact-form__column contact-form__column--info">
                <h2 class="contact-form__title">Información de contacto</h2>
                <ul class="contact-form__info">
                  <li class="contact-form__info-item contact-form__info-item--address"><span class="contact-form__info-icon" aria-hidden="true">
                      <svg width="18" height="18" viewBox="0 0 24 24" fill="none">
                        <path d="M12 21s7-5.2 7-11a7 7 0 1 0-14 0c0 5.8 7 11 7 11z" stroke="currentColor" stroke-width="1.5"></path>
                        <circle cx="12" cy="10" r="2.5" stroke="currentColor" stroke-width="1.5"></circle>
                      </svg></span>
                    <div class="contact-form__info-copy">
                      <p class="contact-form__info-text"><strong>Dirección: </strong><span>Santa Ana, 100 mts sur del Centro Comercial Santa Ana 2000, Bodegas, Local 1</span></p>
                    </div>
                  </li>
                  <li class="contact-form__info-item contact-form__info-item--whatsapp"><span class="contact-form__info-icon" aria-hidden="true">
                      <svg width="18" height="18" viewBox="0 0 24 24" fill="none">
                        <path d="M12 3.2a8.3 8.3 0 0 0-7.2 12.4L4 20.8l5.4-.8A8.3 8.3 0 1 0 12 3.2Z" stroke="currentColor" stroke-width="1.5"></path>
                        <path d="M9.2 9.4c.2-.4.4-.4.7-.4h.5c.2 0 .4 0 .5.4l.7 1.7c.1.2 0 .4-.1.6l-.4.5c-.1.2-.1.3 0 .5.4.7 1.1 1.4 1.8 1.8.2.1.3.1.5 0l.5-.4c.2-.1.4-.2.6-.1l1.7.7c.3.1.4.3.4.5v.5c0 .3 0 .5-.4.7A5 5 0 0 1 12 16.5 5 5 0 0 1 9.2 9.4Z" fill="currentColor"></path>
                      </svg></span>
                    <div class="contact-form__info-copy"><a class="contact-form__info-link" href="https://wa.me/50685128000" target="_blank" rel="noopener noreferrer"><strong>WhatsApp: </strong><span>8512-8000</span></a>
                    </div>
                  </li>
                  <li class="contact-form__info-item contact-form__info-item--email"><span class="contact-form__info-icon" aria-hidden="true">
                      <svg width="18" height="18" viewBox="0 0 24 24" fill="none">
                        <rect x="3" y="5" width="18" height="14" rx="2" stroke="currentColor" stroke-width="1.5"></rect>
                        <path d="M3 7l9 6 9-6" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"></path>
                      </svg></span>
                    <div class="contact-form__info-copy"><a class="contact-form__info-link" href="mailto:hola@recupeplus.com"><strong>Email: </strong><span>hola@recupeplus.com</span></a>
                    </div>
                  </li>
                  <li class="contact-form__info-item contact-form__info-item--schedule"><span class="contact-form__info-icon" aria-hidden="true">
                      <svg width="18" height="18" viewBox="0 0 24 24" fill="none">
                        <rect x="3" y="5" width="18" height="16" rx="2" stroke="currentColor" stroke-width="1.5"></rect>
                        <path d="M8 3v4M16 3v4M3 10h18" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"></path>
                      </svg></span>
                    <div class="contact-form__info-copy">
                      <p class="contact-form__info-text"><strong>Horario de atención telefónica: </strong><span>lunes a viernes, 8:00 a.m. a 4:00 p.m.</span></p>
                    </div>
                  </li>
                  <li class="contact-form__info-item contact-form__info-item--schedule"><span class="contact-form__info-icon" aria-hidden="true">
                      <svg width="18" height="18" viewBox="0 0 24 24" fill="none">
                        <rect x="3" y="5" width="18" height="16" rx="2" stroke="currentColor" stroke-width="1.5"></rect>
                        <path d="M8 3v4M16 3v4M3 10h18" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"></path>
                      </svg></span>
                    <div class="contact-form__info-copy">
                      <p class="contact-form__info-text"><strong>Horario de contacto directo: </strong><span>lunes a viernes, 7:30 a.m. a 4:30 p.m.</span></p>
                    </div>
                  </li>
                </ul>
              </div>
            </div>
          </div>
        </div>
      </section>
