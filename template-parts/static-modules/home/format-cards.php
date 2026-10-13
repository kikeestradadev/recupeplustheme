<?php
/**
 * Static module: format-cards
 *
 * @package wpboilerplate
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$title = function_exists( 'wpb_acf_option_text' ) ? wpb_acf_option_text( 'home_format_title' ) : '';
$rows  = function_exists( 'wpb_acf_option_rows' ) ? wpb_acf_option_rows( 'home_format_cards' ) : array();
$cards = array();

foreach ( $rows as $row ) {
	$image = wpb_acf_image_data( isset( $row['image'] ) ? $row['image'] : null );
	$name  = isset( $row['name'] ) ? $row['name'] : '';
	$text  = isset( $row['text'] ) ? $row['text'] : '';
	$list  = wpb_acf_lines( isset( $row['list'] ) ? $row['list'] : '' );
	$cta   = wpb_acf_link_data( isset( $row['cta'] ) ? $row['cta'] : null );
	$style = isset( $row['style'] ) ? $row['style'] : '';

	if ( ! $image && '' === $name && '' === $text && empty( $list ) ) {
		continue;
	}

	$cards[] = array(
		'image' => $image,
		'name'  => $name,
		'text'  => $text,
		'list'  => $list,
		'cta'   => $cta,
		'style' => $style,
	);
}

if ( '' === $title && empty( $cards ) ) {
	return;
}
?>
<section class="format-cards">
	<div class="main-container format-cards__shell">
		<div class="container format-cards__container format-cards__inner">
			<?php if ( '' !== $title ) : ?>
				<h2 class="format-cards__title"><?php echo esc_html( $title ); ?></h2>
			<?php endif; ?>
			<?php if ( ! empty( $cards ) ) : ?>
				<div class="format-cards__grid">
					<?php foreach ( $cards as $card ) : ?>
						<?php
						$modifier = '';
						if ( 'gomitas' === $card['style'] || 'capsulas' === $card['style'] ) {
							$modifier = ' format-cards__card--' . $card['style'];
						}
						?>
						<article class="format-cards__card<?php echo esc_attr( $modifier ); ?>">
							<div class="format-cards__content">
								<?php if ( '' !== $card['name'] ) : ?>
									<h3 class="format-cards__name"><?php echo esc_html( $card['name'] ); ?></h3>
								<?php endif; ?>
								<?php if ( '' !== $card['text'] ) : ?>
									<p class="format-cards__text"><?php echo esc_html( $card['text'] ); ?></p>
								<?php endif; ?>
								<?php if ( ! empty( $card['list'] ) ) : ?>
									<ul class="format-cards__list">
										<?php foreach ( $card['list'] as $item ) : ?>
											<li class="format-cards__item"><?php echo esc_html( $item ); ?></li>
										<?php endforeach; ?>
									</ul>
								<?php endif; ?>
								<?php if ( ! empty( $card['cta']['url'] ) ) : ?>
									<a
										class="format-cards__cta"
										href="<?php echo esc_url( $card['cta']['url'] ); ?>"
										<?php if ( $card['cta']['target'] ) : ?>
											target="<?php echo esc_attr( $card['cta']['target'] ); ?>"
										<?php endif; ?>
									><?php echo esc_html( $card['cta']['title'] ? $card['cta']['title'] : __( 'Ver más', 'wpboilerplate' ) ); ?></a>
								<?php endif; ?>
							</div>
							<?php if ( $card['image'] ) : ?>
								<div class="format-cards__media">
									<img
										class="format-cards__image"
										src="<?php echo esc_url( $card['image']['url'] ); ?>"
										alt="<?php echo esc_attr( $card['image']['alt'] ? $card['image']['alt'] : $card['name'] ); ?>"
										width="<?php echo esc_attr( (string) $card['image']['width'] ); ?>"
										height="<?php echo esc_attr( (string) $card['image']['height'] ); ?>"
										loading="lazy"
									>
								</div>
							<?php endif; ?>
						</article>
					<?php endforeach; ?>
				</div>
			<?php endif; ?>
		</div>
	</div>
</section>
