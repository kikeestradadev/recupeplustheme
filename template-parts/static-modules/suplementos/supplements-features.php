<?php
/**
 * Static module: supplements-features
 *
 * @package wpboilerplate
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$rows  = function_exists( 'wpb_acf_option_rows' ) ? wpb_acf_option_rows( 'supl_features' ) : array();
$items = array();

foreach ( $rows as $row ) {
	$label = isset( $row['label'] ) ? $row['label'] : '';
	$icon  = wpb_acf_image_data( isset( $row['icon'] ) ? $row['icon'] : null );

	if ( ! $icon && '' === $label ) {
		continue;
	}

	$items[] = array(
		'icon'  => $icon,
		'label' => $label,
	);
}

if ( empty( $items ) ) {
	return;
}
?>
<section class="supplements-features" aria-label="Beneficios de Recupé Plus">
	<div class="main-container supplements-features__shell">
		<div class="container supplements-features__container">
			<ul class="supplements-features__grid">
				<?php foreach ( $items as $item ) : ?>
					<li class="supplements-features__item">
						<?php if ( $item['icon'] ) : ?>
							<span class="supplements-features__icon">
								<img src="<?php echo esc_url( $item['icon']['url'] ); ?>" alt="" width="36" height="36">
							</span>
						<?php endif; ?>
						<?php if ( '' !== $item['label'] ) : ?>
							<span class="supplements-features__label"><?php echo esc_html( $item['label'] ); ?></span>
						<?php endif; ?>
					</li>
				<?php endforeach; ?>
			</ul>
		</div>
	</div>
</section>
