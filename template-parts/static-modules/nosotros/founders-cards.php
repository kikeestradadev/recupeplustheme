<?php
/**
 * Static module: founders-cards
 *
 * @package wpboilerplate
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$title    = function_exists( 'wpb_acf_option_text' ) ? wpb_acf_option_text( 'nos_founders_title' ) : '';
$subtitle = function_exists( 'wpb_acf_option_text' ) ? wpb_acf_option_text( 'nos_founders_subtitle' ) : '';
$rows     = function_exists( 'wpb_acf_option_rows' ) ? wpb_acf_option_rows( 'nos_founders_cards' ) : array();
$cards    = array();

foreach ( $rows as $row ) {
	$name  = isset( $row['name'] ) ? $row['name'] : '';
	$role  = isset( $row['role'] ) ? $row['role'] : '';
	$text  = isset( $row['text'] ) ? $row['text'] : '';
	$image = wpb_acf_image_data( isset( $row['image'] ) ? $row['image'] : null );

	if ( ! $image && '' === $name && '' === $role && '' === $text ) {
		continue;
	}

	$cards[] = array(
		'image' => $image,
		'name'  => $name,
		'role'  => $role,
		'text'  => $text,
	);
}

if ( '' === $title && '' === $subtitle && empty( $cards ) ) {
	return;
}
?>
<section class="founders-cards">
	<div class="main-container founders-cards__shell">
		<div class="container founders-cards__container founders-cards__inner">
			<?php if ( '' !== $title || '' !== $subtitle ) : ?>
				<header class="founders-cards__header">
					<?php if ( '' !== $title ) : ?>
						<h2 class="founders-cards__title"><?php echo esc_html( $title ); ?></h2>
					<?php endif; ?>
					<?php if ( '' !== $subtitle ) : ?>
						<p class="founders-cards__subtitle"><?php echo esc_html( $subtitle ); ?></p>
					<?php endif; ?>
				</header>
			<?php endif; ?>
			<?php if ( ! empty( $cards ) ) : ?>
				<div class="founders-cards__grid">
					<?php foreach ( $cards as $card ) : ?>
						<article class="founders-cards__card">
							<?php if ( $card['image'] ) : ?>
								<div class="founders-cards__media">
									<img
										class="founders-cards__image"
										src="<?php echo esc_url( $card['image']['url'] ); ?>"
										alt="<?php echo esc_attr( $card['image']['alt'] ? $card['image']['alt'] : $card['name'] ); ?>"
										width="<?php echo esc_attr( (string) $card['image']['width'] ); ?>"
										height="<?php echo esc_attr( (string) $card['image']['height'] ); ?>"
										loading="lazy"
									>
								</div>
							<?php endif; ?>
							<?php if ( '' !== $card['name'] || '' !== $card['role'] || '' !== $card['text'] ) : ?>
								<div class="founders-cards__body">
									<?php if ( '' !== $card['name'] ) : ?>
										<h3 class="founders-cards__name"><?php echo esc_html( $card['name'] ); ?></h3>
									<?php endif; ?>
									<?php if ( '' !== $card['role'] ) : ?>
										<p class="founders-cards__role"><?php echo esc_html( $card['role'] ); ?></p>
									<?php endif; ?>
									<?php if ( '' !== $card['text'] ) : ?>
										<p class="founders-cards__text"><?php echo esc_html( $card['text'] ); ?></p>
									<?php endif; ?>
								</div>
							<?php endif; ?>
						</article>
					<?php endforeach; ?>
				</div>
			<?php endif; ?>
		</div>
	</div>
</section>
