<?php
/**
 * Module: alt-footer (ACF Options → Footer).
 *
 * @package wpboilerplate
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$logo_raw  = function_exists( 'get_field' ) ? get_field( 'footer_logo', 'option' ) : null;
$logo      = function_exists( 'wpb_acf_image_data' ) ? wpb_acf_image_data( $logo_raw ) : null;
$logo_alt  = function_exists( 'get_field' ) ? get_field( 'footer_logo_alt', 'option' ) : '';
$logo_alt  = function_exists( 'wpb_acf_image_alt' ) ? wpb_acf_image_alt( $logo_alt, $logo, __( 'Recupé Plus', 'wpboilerplate' ) ) : ( is_string( $logo_alt ) ? $logo_alt : '' );
$brand_text = function_exists( 'get_field' ) ? get_field( 'footer_brand_text', 'option' ) : '';
$brand_text = is_string( $brand_text ) ? $brand_text : '';

$social_rows = function_exists( 'get_field' ) ? get_field( 'footer_social', 'option' ) : array();
$social_rows = is_array( $social_rows ) ? $social_rows : array();
$social      = array();

$platform_labels = array(
	'facebook'  => 'Facebook',
	'instagram' => 'Instagram',
	'twitter'   => 'Twitter / X',
	'youtube'   => 'YouTube',
	'linkedin'  => 'LinkedIn',
	'tiktok'    => 'TikTok',
	'whatsapp'  => 'WhatsApp',
);

foreach ( $social_rows as $row ) {
	$platform = isset( $row['platform'] ) ? sanitize_title( $row['platform'] ) : '';
	$url      = isset( $row['url'] ) ? esc_url_raw( $row['url'] ) : '';
	if ( '' === $platform || '' === $url || ! isset( $platform_labels[ $platform ] ) ) {
		continue;
	}
	$social[] = array(
		'platform' => $platform,
		'url'      => $url,
		'label'    => $platform_labels[ $platform ],
	);
}

/**
 * Normalize footer link repeater rows (page ID select, legacy link array, or URL).
 *
 * @param mixed $rows Repeater value.
 * @return array<int, array{label:string,url:string,target:string}>
 */
$normalize_links = static function ( $rows ) {
	$out = array();
	if ( ! is_array( $rows ) ) {
		return $out;
	}
	foreach ( $rows as $row ) {
		$raw = isset( $row['link'] ) ? $row['link'] : null;
		$url    = '';
		$label  = '';
		$target = '';

		if ( is_array( $raw ) ) {
			$link   = function_exists( 'wpb_acf_link_data' ) ? wpb_acf_link_data( $raw ) : array(
				'url'    => '',
				'title'  => '',
				'target' => '',
			);
			$url    = $link['url'];
			$label  = $link['title'];
			$target = $link['target'];
		} elseif ( is_numeric( $raw ) ) {
			$page_id = (int) $raw;
			$permalink = get_permalink( $page_id );
			if ( $permalink ) {
				$url   = $permalink;
				$label = get_the_title( $page_id );
			}
		} elseif ( is_string( $raw ) && '' !== trim( $raw ) ) {
			$path = trim( $raw );
			// Path + anchor choices (e.g. /suplementos/#gomitas) — keep as site URL.
			if ( isset( $path[0] ) && '/' === $path[0] ) {
				$url = home_url( $path );
			} else {
				$url = esc_url_raw( $path );
			}
			if ( function_exists( 'wpb_footer_page_link_choices' ) ) {
				$choice_labels = wpb_footer_page_link_choices();
				if ( isset( $choice_labels[ $path ] ) ) {
					$label = $choice_labels[ $path ];
				}
			}
		}

		if ( '' === $url ) {
			continue;
		}
		if ( '' === $label ) {
			$label = $url;
		}
		$out[] = array(
			'label'  => $label,
			'url'    => $url,
			'target' => $target,
		);
	}
	return $out;
};

$supplements = $normalize_links( function_exists( 'get_field' ) ? get_field( 'footer_supplements_links', 'option' ) : array() );
$help        = $normalize_links( function_exists( 'get_field' ) ? get_field( 'footer_help_links', 'option' ) : array() );
$company     = $normalize_links( function_exists( 'get_field' ) ? get_field( 'footer_company_links', 'option' ) : array() );

$address  = function_exists( 'get_field' ) ? get_field( 'footer_contact_address', 'option' ) : '';
$address  = is_string( $address ) ? trim( $address ) : '';
$phone    = function_exists( 'get_field' ) ? get_field( 'footer_contact_phone', 'option' ) : '';
$phone    = is_string( $phone ) ? trim( $phone ) : '';
$email    = function_exists( 'get_field' ) ? get_field( 'footer_contact_email', 'option' ) : '';
$email    = is_string( $email ) ? trim( $email ) : '';
$whatsapp = function_exists( 'wpb_acf_global_whatsapp_url' ) ? wpb_acf_global_whatsapp_url() : '';
$extra    = function_exists( 'get_field' ) ? get_field( 'footer_contact_extra', 'option' ) : '';
$extra    = is_string( $extra ) ? $extra : '';
$extra_lines = function_exists( 'wpb_acf_lines' ) ? wpb_acf_lines( $extra ) : array_filter( array_map( 'trim', preg_split( '/\r\n|\r|\n/', $extra ) ?: array() ) );
$map_embed = function_exists( 'get_field' ) ? get_field( 'footer_contact_map_embed', 'option' ) : '';
$map_embed = is_string( $map_embed ) ? trim( $map_embed ) : '';
$copyright_text = function_exists( 'get_field' ) ? get_field( 'footer_copyright', 'option' ) : '';
$copyright_text = is_string( $copyright_text ) ? trim( $copyright_text ) : '';
if ( '' === $copyright_text ) {
	$copyright_text = 'Recupe Plus. ' . __( 'Todos los derechos reservados.', 'wpboilerplate' );
}
$copyright = '© ' . gmdate( 'Y' ) . ' ' . $copyright_text;

$iframe_allowed = array(
	'iframe' => array(
		'src'             => true,
		'width'           => true,
		'height'          => true,
		'style'           => true,
		'title'           => true,
		'class'           => true,
		'allowfullscreen' => true,
		'loading'         => true,
		'referrerpolicy'  => true,
		'frameborder'     => true,
	),
);
$map_embed = $map_embed ? wp_kses( $map_embed, $iframe_allowed ) : '';

$has_contact = ( '' !== $address || '' !== $phone || '' !== $email || '' !== $whatsapp || ! empty( $extra_lines ) || '' !== $map_embed );
$has_brand   = ( $logo || '' !== $brand_text || ! empty( $social ) );

$phone_href = '' !== $phone ? preg_replace( '/[^\d+]/', '', $phone ) : '';

/**
 * Social SVG path markup by platform.
 *
 * @param string $platform Platform slug.
 * @return string
 */
$social_icon = static function ( $platform ) {
	switch ( $platform ) {
		case 'instagram':
			return '<rect x="3" y="3" width="18" height="18" rx="5" stroke="currentColor" stroke-width="1.5"></rect><circle cx="12" cy="12" r="4" stroke="currentColor" stroke-width="1.5"></circle><circle cx="17.5" cy="6.5" r="1" fill="currentColor"></circle>';
		case 'facebook':
			return '<path d="M14 8h2V5h-2c-2.2 0-4 1.8-4 4v2H8v3h2v7h3v-7h2.2l.8-3H13V9c0-.6.4-1 1-1z" fill="currentColor"></path>';
		case 'youtube':
			return '<rect x="2" y="6" width="20" height="12" rx="3" stroke="currentColor" stroke-width="1.5"></rect><path d="M10 9.5v5l5-2.5-5-2.5z" fill="currentColor"></path>';
		case 'twitter':
			return '<path d="M4 4l6.5 8.2L4.3 20H6l5.2-6.2L15.8 20H20l-6.7-8.5L19.5 4H17.8l-4.8 5.7L9 4H4z" fill="currentColor"></path>';
		case 'linkedin':
			return '<rect x="3" y="3" width="18" height="18" rx="2" stroke="currentColor" stroke-width="1.5"></rect><path d="M8 10.5V17M8 7.5v.1M11.5 17v-3.8c0-1.5.8-2.2 2-2.2s2 .8 2 2.2V17" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"></path>';
		case 'tiktok':
			return '<path d="M14 5c.4 2.2 1.8 3.6 4 4v2.2c-1.4.1-2.7-.3-4-1.1V15a5 5 0 1 1-5-5c.3 0 .7 0 1 .1V12a2.8 2.8 0 1 0 2 2.7V5h2z" fill="currentColor"></path>';
		case 'whatsapp':
			return '<path d="M12 3.2a8.3 8.3 0 0 0-7.2 12.4L4 20.8l5.4-.8A8.3 8.3 0 1 0 12 3.2Z" stroke="currentColor" stroke-width="1.5"></path><path d="M9.2 9.4c.2-.4.4-.4.7-.4h.5c.2 0 .4 0 .5.4l.7 1.7c.1.2 0 .4-.1.6l-.4.5c-.1.2-.1.3 0 .5.4.7 1.1 1.4 1.8 1.8.2.1.3.1.5 0l.5-.4c.2-.1.4-.2.6-.1l1.7.7c.3.1.4.3.4.5v.5c0 .3 0 .5-.4.7A5 5 0 0 1 12 16.5 5 5 0 0 1 9.2 9.4Z" fill="currentColor"></path>';
		default:
			return '';
	}
};
?>
<footer class="alt-footer">
	<div class="main-container alt-footer__shell">
		<div class="container alt-footer__container alt-footer__inner">
			<div class="alt-footer__grid">
				<?php if ( $has_brand ) : ?>
					<div class="alt-footer__brand">
						<?php if ( $logo ) : ?>
							<a class="alt-footer__logo" href="<?php echo esc_url( home_url( '/' ) ); ?>">
								<img
									class="alt-footer__logo-img"
									src="<?php echo esc_url( $logo['url'] ); ?>"
									alt="<?php echo esc_attr( $logo_alt ); ?>"
									width="105"
									height="63"
									decoding="async"
								>
							</a>
						<?php endif; ?>
						<?php if ( '' !== $brand_text ) : ?>
							<p class="alt-footer__text"><?php echo esc_html( $brand_text ); ?></p>
						<?php endif; ?>
						<?php if ( ! empty( $social ) ) : ?>
							<ul class="alt-footer__social">
								<?php foreach ( $social as $item ) : ?>
									<?php $icon = $social_icon( $item['platform'] ); ?>
									<?php if ( '' === $icon ) : ?>
										<?php continue; ?>
									<?php endif; ?>
									<li class="alt-footer__social-item">
										<a
											class="alt-footer__social-link"
											href="<?php echo esc_url( $item['url'] ); ?>"
											target="_blank"
											rel="noopener noreferrer"
											aria-label="<?php echo esc_attr( $item['label'] ); ?>"
										>
											<svg class="alt-footer__icon" width="18" height="18" viewBox="0 0 24 24" fill="none" aria-hidden="true">
												<?php echo $icon; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static SVG paths. ?>
											</svg>
										</a>
									</li>
								<?php endforeach; ?>
							</ul>
						<?php endif; ?>
					</div>
				<?php endif; ?>

				<?php if ( ! empty( $supplements ) ) : ?>
					<nav class="alt-footer__col" aria-label="<?php echo esc_attr__( 'Suplementos', 'wpboilerplate' ); ?>">
						<h2 class="alt-footer__title"><?php esc_html_e( 'Suplementos', 'wpboilerplate' ); ?></h2>
						<ul class="alt-footer__list">
							<?php foreach ( $supplements as $link ) : ?>
								<li class="alt-footer__item">
									<a
										class="alt-footer__link"
										href="<?php echo esc_url( $link['url'] ); ?>"
										<?php if ( ! empty( $link['target'] ) ) : ?>
											target="<?php echo esc_attr( $link['target'] ); ?>"
											<?php if ( '_blank' === $link['target'] ) : ?>
												rel="noopener noreferrer"
											<?php endif; ?>
										<?php endif; ?>
									><?php echo esc_html( $link['label'] ); ?></a>
								</li>
							<?php endforeach; ?>
						</ul>
					</nav>
				<?php endif; ?>

				<?php if ( ! empty( $help ) ) : ?>
					<nav class="alt-footer__col" aria-label="<?php echo esc_attr__( 'Ayuda', 'wpboilerplate' ); ?>">
						<h2 class="alt-footer__title"><?php esc_html_e( 'Ayuda', 'wpboilerplate' ); ?></h2>
						<ul class="alt-footer__list">
							<?php foreach ( $help as $link ) : ?>
								<li class="alt-footer__item">
									<a
										class="alt-footer__link"
										href="<?php echo esc_url( $link['url'] ); ?>"
										<?php if ( ! empty( $link['target'] ) ) : ?>
											target="<?php echo esc_attr( $link['target'] ); ?>"
											<?php if ( '_blank' === $link['target'] ) : ?>
												rel="noopener noreferrer"
											<?php endif; ?>
										<?php endif; ?>
									><?php echo esc_html( $link['label'] ); ?></a>
								</li>
							<?php endforeach; ?>
						</ul>
					</nav>
				<?php endif; ?>

				<?php if ( ! empty( $company ) ) : ?>
					<nav class="alt-footer__col" aria-label="<?php echo esc_attr__( 'Empresa', 'wpboilerplate' ); ?>">
						<h2 class="alt-footer__title"><?php esc_html_e( 'Empresa', 'wpboilerplate' ); ?></h2>
						<ul class="alt-footer__list">
							<?php foreach ( $company as $link ) : ?>
								<li class="alt-footer__item">
									<a
										class="alt-footer__link"
										href="<?php echo esc_url( $link['url'] ); ?>"
										<?php if ( ! empty( $link['target'] ) ) : ?>
											target="<?php echo esc_attr( $link['target'] ); ?>"
											<?php if ( '_blank' === $link['target'] ) : ?>
												rel="noopener noreferrer"
											<?php endif; ?>
										<?php endif; ?>
									><?php echo esc_html( $link['label'] ); ?></a>
								</li>
							<?php endforeach; ?>
						</ul>
					</nav>
				<?php endif; ?>

				<?php if ( $has_contact ) : ?>
					<div class="alt-footer__col alt-footer__col--contact">
						<h2 class="alt-footer__title"><?php esc_html_e( 'Contacto', 'wpboilerplate' ); ?></h2>
						<ul class="alt-footer__contact">
							<?php if ( '' !== $whatsapp ) : ?>
								<li class="alt-footer__contact-item">
									<a class="alt-footer__contact-link alt-footer__contact-link--whatsapp" href="<?php echo esc_url( $whatsapp ); ?>" target="_blank" rel="noopener noreferrer">
										<svg class="alt-footer__contact-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" aria-hidden="true">
											<path d="M12 3.2a8.3 8.3 0 0 0-7.2 12.4L4 20.8l5.4-.8A8.3 8.3 0 1 0 12 3.2Z" stroke="currentColor" stroke-width="1.5"></path>
											<path d="M9.2 9.4c.2-.4.4-.4.7-.4h.5c.2 0 .4 0 .5.4l.7 1.7c.1.2 0 .4-.1.6l-.4.5c-.1.2-.1.3 0 .5.4.7 1.1 1.4 1.8 1.8.2.1.3.1.5 0l.5-.4c.2-.1.4-.2.6-.1l1.7.7c.3.1.4.3.4.5v.5c0 .3 0 .5-.4.7A5 5 0 0 1 12 16.5 5 5 0 0 1 9.2 9.4Z" fill="currentColor"></path>
										</svg>
										<span><?php esc_html_e( 'Escribinos por WhatsApp', 'wpboilerplate' ); ?></span>
									</a>
								</li>
							<?php endif; ?>
							<?php if ( '' !== $email ) : ?>
								<li class="alt-footer__contact-item">
									<a class="alt-footer__contact-link" href="<?php echo esc_url( 'mailto:' . $email ); ?>">
										<svg class="alt-footer__contact-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" aria-hidden="true">
											<rect x="3" y="5" width="18" height="14" rx="2" stroke="currentColor" stroke-width="1.5"></rect>
											<path d="M3 7l9 6 9-6" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"></path>
										</svg>
										<span><?php echo esc_html( $email ); ?></span>
									</a>
								</li>
							<?php endif; ?>
							<?php if ( '' !== $phone ) : ?>
								<li class="alt-footer__contact-item">
									<a class="alt-footer__contact-link" href="<?php echo esc_url( 'tel:' . $phone_href ); ?>">
										<svg class="alt-footer__contact-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" aria-hidden="true">
											<path d="M6 4h3l1 4-2 1a10 10 0 0 0 5 5l1-2 4 1v3a2 2 0 0 1-2 2A14 14 0 0 1 4 6a2 2 0 0 1 2-2z" stroke="currentColor" stroke-width="1.5" stroke-linejoin="round"></path>
										</svg>
										<span><?php echo esc_html( $phone ); ?></span>
									</a>
								</li>
							<?php endif; ?>
							<?php if ( '' !== $address ) : ?>
								<li class="alt-footer__contact-item">
									<span class="alt-footer__contact-link"><?php echo nl2br( esc_html( $address ) ); ?></span>
								</li>
							<?php endif; ?>
							<?php foreach ( $extra_lines as $line ) : ?>
								<li class="alt-footer__contact-item">
									<span class="alt-footer__contact-link"><?php echo esc_html( $line ); ?></span>
								</li>
							<?php endforeach; ?>
						</ul>
						<?php if ( '' !== $map_embed ) : ?>
							<div class="alt-footer__map">
								<?php echo $map_embed; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- sanitized with wp_kses. ?>
							</div>
						<?php endif; ?>
					</div>
				<?php endif; ?>
			</div>
			<div class="alt-footer__bottom">
				<p class="alt-footer__copy"><?php echo esc_html( $copyright ); ?></p>
			</div>
		</div>
	</div>
</footer>
