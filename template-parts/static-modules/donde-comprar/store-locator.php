<?php
/**
 * Module: store-locator (Encontranos cerca de vos).
 *
 * Driven only by ACF repeater `buy_locator_tabs` (title + map_embed).
 *
 * @package wpboilerplate
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$rows = function_exists( 'wpb_acf_option_rows' ) ? wpb_acf_option_rows( 'buy_locator_tabs' ) : array();
$tabs = array();

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

foreach ( $rows as $row ) {
	$title = isset( $row['title'] ) ? trim( (string) $row['title'] ) : '';
	$embed = isset( $row['map_embed'] ) ? trim( (string) $row['map_embed'] ) : '';
	$embed = $embed ? wp_kses( $embed, $iframe_allowed ) : '';

	if ( '' === $title && '' === $embed ) {
		continue;
	}

	$tabs[] = array(
		'title' => $title,
		'embed' => $embed,
	);
}

if ( empty( $tabs ) ) {
	return;
}
?>
<section class="store-locator" data-store-locator>
	<div class="main-container store-locator__shell">
		<div class="container store-locator__container store-locator__inner">
			<header class="store-locator__header">
				<h2 class="store-locator__title"><?php esc_html_e( 'Encontranos cerca de vos', 'wpboilerplate' ); ?></h2>
			</header>
			<div class="store-locator__layout">
				<div class="store-locator__panel">
					<ul class="store-locator__list" role="tablist" aria-label="<?php esc_attr_e( 'Ubicaciones', 'wpboilerplate' ); ?>">
						<?php foreach ( $tabs as $index => $tab ) : ?>
							<?php
							$is_active = ( 0 === $index );
							$tab_id    = 'store-locator-tab-' . $index;
							$panel_id  = 'store-locator-panel-' . $index;
							?>
							<li
								class="store-locator__item<?php echo $is_active ? ' store-locator__item--active' : ''; ?>"
								data-store="<?php echo esc_attr( (string) $index ); ?>"
								role="presentation"
							>
								<button
									class="store-locator__item-btn"
									type="button"
									id="<?php echo esc_attr( $tab_id ); ?>"
									role="tab"
									aria-selected="<?php echo $is_active ? 'true' : 'false'; ?>"
									aria-controls="<?php echo esc_attr( $panel_id ); ?>"
									tabindex="<?php echo $is_active ? '0' : '-1'; ?>"
								>
									<span class="store-locator__item-icon" aria-hidden="true">
										<svg width="16" height="16" viewBox="0 0 24 24" fill="none">
											<path d="M12 21s7-5.2 7-11a7 7 0 1 0-14 0c0 5.8 7 11 7 11z" stroke="currentColor" stroke-width="1.6"></path>
											<circle cx="12" cy="10" r="2.4" stroke="currentColor" stroke-width="1.6"></circle>
										</svg>
									</span>
									<span class="store-locator__item-content">
										<span class="store-locator__item-name"><?php echo esc_html( $tab['title'] ? $tab['title'] : __( 'Ubicación', 'wpboilerplate' ) ); ?></span>
									</span>
									<span class="store-locator__item-arrow" aria-hidden="true">→</span>
								</button>
							</li>
						<?php endforeach; ?>
					</ul>
				</div>
				<div class="store-locator__map" aria-live="polite">
					<?php foreach ( $tabs as $index => $tab ) : ?>
						<?php
						$is_active = ( 0 === $index );
						$tab_id    = 'store-locator-tab-' . $index;
						$panel_id  = 'store-locator-panel-' . $index;
						?>
						<div
							class="store-locator__map-panel<?php echo $is_active ? ' store-locator__map-panel--active' : ''; ?>"
							id="<?php echo esc_attr( $panel_id ); ?>"
							data-store="<?php echo esc_attr( (string) $index ); ?>"
							role="tabpanel"
							aria-labelledby="<?php echo esc_attr( $tab_id ); ?>"
							aria-hidden="<?php echo $is_active ? 'false' : 'true'; ?>"
						>
							<?php if ( $tab['embed'] ) : ?>
								<?php
								// Force map iframe to fill the panel.
								$embed = preg_replace(
									'/<iframe\b/i',
									'<iframe class="store-locator__map-frame"',
									$tab['embed'],
									1
								);
								echo $embed; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- sanitized with wp_kses above.
								?>
							<?php endif; ?>
						</div>
					<?php endforeach; ?>
				</div>
			</div>
		</div>
	</div>
</section>
