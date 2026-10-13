<?php
/**
 * Front page template.
 *
 * Uses the same fixed modules as page-home.php when this site
 * shows a static front page or the blog home at /.
 *
 * @package wpboilerplate
 */

get_header();
get_template_part( 'template-parts/static-modules/home/home-modules' );
get_footer();
