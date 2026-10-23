<?php
/**
 * Términos y condiciones. The body comes from Mantenimientos → Políticas.
 *
 * @package wpboilerplate
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$content = function_exists( 'get_field' ) ? get_field( 'politicas_terminos', 'option' ) : '';
$content = is_string( $content ) ? $content : '';
?>
<section class="policy-content" aria-labelledby="policy-terminos-title">
	<div class="main-container">
		<div class="container policy-content__container">
			<h1 class="policy-content__title" id="policy-terminos-title">Términos y condiciones</h1>
			<?php if ( '' !== trim( wp_strip_all_tags( $content ) ) ) : ?>
				<div class="policy-content__body">
					<?php echo wp_kses_post( $content ); ?>
				</div>
			<?php endif; ?>
		</div>
	</div>
</section>
