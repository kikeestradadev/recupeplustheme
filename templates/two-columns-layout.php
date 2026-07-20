<?php
/**
 * Template Name: two columns
 *
 * @package wpboilerplate
 */

get_header(); ?>
<div class="content-info-column">
	<div class="content-info-column__item">
		<?php get_template_part( 'template-parts/module1' );?>
	</div>
	<div class="content-info-column__item">
		<?php the_content();?>
	</div>
</div>
<?php get_footer(); ?>
