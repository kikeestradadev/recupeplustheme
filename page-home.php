<?php
/**
 * Template Name: Home
 * Template Post Type: page
 *
 * Dedicated template for the page with slug `home`.
 * WordPress loads this file automatically for /home/.
 *
 * @package wpboilerplate
 */

get_header();
get_template_part( 'template-parts/static-modules/home/home-modules' );
get_footer();
