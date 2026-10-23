<?php
/**
 * Template Name: Tienda
 * Template Post Type: page
 *
 * Dedicated template for the page with slug `tienda`.
 * Reuses the supplements gomitas and cápsulas product sliders.
 *
 * @package wpboilerplate
 */

get_header();
get_template_part( 'template-parts/static-modules/tienda/tienda-banner' );
get_template_part( 'template-parts/static-modules/suplementos/product-slider' );
get_footer();
