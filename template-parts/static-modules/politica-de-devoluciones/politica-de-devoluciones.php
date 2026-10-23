<?php
/**
 * Política de devoluciones. The body comes from Mantenimientos → Políticas.
 *
 * @package wpboilerplate
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$content = function_exists( 'get_field' ) ? get_field( 'politicas_devoluciones', 'option' ) : '';
$content = is_string( $content ) ? $content : '';
?>
<section class="policy-content" aria-labelledby="policy-devoluciones-title">
	<div class="main-container">
		<div class="container policy-content__container">
			<h1 class="policy-content__title" id="policy-devoluciones-title">Política de devoluciones</h1>
			<?php if ( '' !== trim( wp_strip_all_tags( $content ) ) ) : ?>
				<div class="policy-content__body">
					<?php echo wp_kses_post( $content ); ?>
				</div>
			<?php endif; ?>
		</div>
	</div>
</section>
