<?php
/**
 * Template Name: Producto
 * Template Post Type: page
 *
 * Product showcase page — data from WooCommerce (default: Activity, or ?sku= / ?product=slug).
 *
 * @package wpboilerplate
 */

get_header();

$product = function_exists( 'wpb_wc_resolve_showcase_product' ) ? wpb_wc_resolve_showcase_product() : null;

get_template_part(
	'template-parts/static-modules/product/product-showcase',
	null,
	array(
		'product' => $product,
	)
);

get_footer();
