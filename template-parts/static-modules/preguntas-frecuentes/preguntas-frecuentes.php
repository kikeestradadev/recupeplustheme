<?php
/**
 * Static module: preguntas-frecuentes
 *
 * @package wpboilerplate
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$page_id = get_queried_object_id();
$title   = function_exists( 'get_field' ) ? get_field( 'pf_title', $page_id ) : '';
$intro   = function_exists( 'get_field' ) ? get_field( 'pf_intro', $page_id ) : '';
$rows    = function_exists( 'get_field' ) ? get_field( 'pf_faqs', $page_id ) : array();
$title   = is_string( $title ) ? $title : '';
$intro   = is_string( $intro ) ? $intro : '';
$rows    = is_array( $rows ) ? $rows : array();
$items   = array();

foreach ( $rows as $row ) {
	$question = isset( $row['question'] ) ? trim( (string) $row['question'] ) : '';
	$answer   = isset( $row['answer'] ) ? trim( (string) $row['answer'] ) : '';

	if ( '' === $question && '' === $answer ) {
		continue;
	}

	$items[] = array(
		'question' => $question,
		'answer'   => $answer,
	);
}
?>
<section class="faqs" id="faqs" aria-labelledby="faqs-title">
	<div class="main-container faqs__shell">
		<div class="container faqs__container">
			<div class="faqs__header">
				<h1 class="faqs__title" id="faqs-title">
					<?php echo esc_html( '' !== $title ? $title : __( 'Preguntas frecuentes', 'wpboilerplate' ) ); ?>
				</h1>
				<?php if ( '' !== $intro ) : ?>
					<p class="faqs__intro"><?php echo esc_html( $intro ); ?></p>
				<?php endif; ?>
			</div>
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
