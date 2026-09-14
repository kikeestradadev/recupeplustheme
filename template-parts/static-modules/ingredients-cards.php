<?php
/**
 * Static module: ingredients-cards
 *
 * @package wpboilerplate
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$title = function_exists( 'wpb_acf_option_text' ) ? wpb_acf_option_text( 'home_ingredients_title' ) : '';
$rows  = function_exists( 'wpb_acf_option_rows' ) ? wpb_acf_option_rows( 'home_ingredients_cards' ) : array();
$cards = array();

foreach ( $rows as $row ) {
	$image = wpb_acf_image_data( isset( $row['image'] ) ? $row['image'] : null );
	$name  = isset( $row['name'] ) ? $row['name'] : '';
	$list  = wpb_acf_lines( isset( $row['list'] ) ? $row['list'] : '' );

	if ( ! $image && '' === $name && empty( $list ) ) {
		continue;
	}

	$cards[] = array(
		'image' => $image,
		'name'  => $name,
		'list'  => $list,
	);
}

if ( '' === $title && empty( $cards ) ) {
	return;
}
?>
<section class="ingredients-cards">
	<div class="main-container ingredients-cards__shell">
		<div class="container ingredients-cards__container ingredients-cards__inner">
			<?php if ( '' !== $title ) : ?>
				<h2 class="ingredients-cards__title"><?php echo esc_html( $title ); ?></h2>
			<?php endif; ?>
			<?php if ( ! empty( $cards ) ) : ?>
				<div class="ingredients-cards__grid">
					<?php foreach ( $cards as $card ) : ?>
						<article class="ingredients-cards__card">
							<?php if ( $card['image'] ) : ?>
								<div class="ingredients-cards__media">
									<img
										class="ingredients-cards__image"
										src="<?php echo esc_url( $card['image']['url'] ); ?>"
										alt="<?php echo esc_attr( $card['image']['alt'] ? $card['image']['alt'] : $card['name'] ); ?>"
										width="<?php echo esc_attr( (string) $card['image']['width'] ); ?>"
										height="<?php echo esc_attr( (string) $card['image']['height'] ); ?>"
										loading="lazy"
									>
								</div>
							<?php endif; ?>
							<div class="ingredients-cards__body">
								<?php if ( '' !== $card['name'] ) : ?>
									<h3 class="ingredients-cards__name"><?php echo esc_html( $card['name'] ); ?></h3>
								<?php endif; ?>
								<?php if ( ! empty( $card['list'] ) ) : ?>
									<ul class="ingredients-cards__list">
										<?php foreach ( $card['list'] as $item ) : ?>
											<li class="ingredients-cards__bullet"><?php echo esc_html( $item ); ?></li>
										<?php endforeach; ?>
									</ul>
								<?php endif; ?>
							</div>
						</article>
					<?php endforeach; ?>
				</div>
			<?php endif; ?>
		</div>
	</div>
</section>
