<?php
/**
 * Static module: faqs
 *
 * @package wpboilerplate
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$title = function_exists( 'wpb_acf_option_text' ) ? wpb_acf_option_text( 'home_faqs_title' ) : '';
$intro = function_exists( 'wpb_acf_option_text' ) ? wpb_acf_option_text( 'home_faqs_intro' ) : '';
$rows  = function_exists( 'wpb_acf_option_rows' ) ? wpb_acf_option_rows( 'home_faqs' ) : array();
$items = array();

foreach ( $rows as $row ) {
	$question = isset( $row['question'] ) ? $row['question'] : '';
	$answer   = isset( $row['answer'] ) ? $row['answer'] : '';

	if ( '' === $question && '' === $answer ) {
		continue;
	}

	$items[] = array(
		'question' => $question,
		'answer'   => $answer,
	);
}

if ( '' === $title && '' === $intro && empty( $items ) ) {
	return;
}
?>
<section class="faqs" id="faqs" aria-labelledby="faqs-title">
	<div class="main-container faqs__shell">
		<div class="container faqs__container">
			<?php if ( '' !== $title || '' !== $intro ) : ?>
				<div class="faqs__header">
					<?php if ( '' !== $title ) : ?>
						<h2 class="faqs__title" id="faqs-title"><?php echo esc_html( $title ); ?></h2>
					<?php endif; ?>
					<?php if ( '' !== $intro ) : ?>
						<p class="faqs__intro"><?php echo esc_html( $intro ); ?></p>
					<?php endif; ?>
				</div>
			<?php endif; ?>
			<?php if ( ! empty( $items ) ) : ?>
				<div class="faqs__list">
					<?php foreach ( $items as $item ) : ?>
						<details class="faqs__item">
							<summary class="faqs__question">
								<span class="faqs__question-text"><?php echo esc_html( $item['question'] ); ?></span>
								<span class="faqs__arrow" aria-hidden="true"></span>
							</summary>
							<?php if ( '' !== $item['answer'] ) : ?>
								<div class="faqs__answer">
									<p><?php echo esc_html( $item['answer'] ); ?></p>
								</div>
							<?php endif; ?>
						</details>
					<?php endforeach; ?>
				</div>
			<?php endif; ?>
		</div>
	</div>
</section>
