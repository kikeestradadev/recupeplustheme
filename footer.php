<?php
  /**
   * The template for displaying the footer
   *
   * Contains the closing of the #content div and all content after.
   *
   * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
   *
   * @package WordPress
   * @subpackage Twenty_Twenty_One
   * @since Twenty Twenty-One 1.0
   */
?>
	</main>
	<?php get_template_part( 'template-parts/static-modules/shared/alt-footer' ); ?>
	<?php get_template_part( 'template-parts/static-modules/shared/whatsapp-button' ); ?>
	<?php get_template_part( 'template-parts/static-modules/shared/cart-drawer' ); ?>
	<?php wp_footer(); ?>
	</body>
</html>
