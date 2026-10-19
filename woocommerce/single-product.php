<?php
/**
 * Single product — Recupé Plus showcase layout.
 *
 * @see     https://woocommerce.com/document/template-structure/
 * @package wpboilerplate
 * @version 1.6.4
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

while ( have_posts() ) {
	the_post();
	$product = wc_get_product( get_the_ID() );
	get_template_part(
		'template-parts/static-modules/product/product-showcase',
		null,
		array(
			'product' => $product,
		)
	);
}

get_footer();
