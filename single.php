<?php
/**
 * The template for displaying single posts.
 *
 * @package wpboilerplate
 */

get_header();

while ( have_posts() ) :
	the_post();
	get_template_part( 'template-parts/content/content-single' );
endwhile;

get_footer();
