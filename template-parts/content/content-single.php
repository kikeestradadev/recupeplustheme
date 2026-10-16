<?php
/**
 * Template part for displaying single post content.
 *
 * Featured image uses the LP post-banner structure (section > figure > img).
 * Content uses main-container > container like the LP post-content module.
 *
 * @package wpboilerplate
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$banner_url = get_the_post_thumbnail_url( get_the_ID(), 'full' );
$banner_alt = trim( (string) get_post_meta( get_post_thumbnail_id(), '_wp_attachment_image_alt', true ) );
if ( '' === $banner_alt ) {
	$banner_alt = get_the_title();
}
?>
<?php if ( $banner_url ) : ?>
	<section class="post-banner" aria-label="<?php echo esc_attr( $banner_alt ); ?>">
		<figure class="post-banner__figure">
			<img
				class="post-banner__image"
				src="<?php echo esc_url( $banner_url ); ?>"
				alt="<?php echo esc_attr( $banner_alt ); ?>"
				width="1600"
				height="900"
				loading="eager"
				fetchpriority="high"
			>
		</figure>
	</section>
<?php endif; ?>
<article id="post-<?php the_ID(); ?>" <?php post_class( 'post-content' ); ?> aria-labelledby="post-content-title">
	<div class="main-container post-content__shell">
		<div class="container post-content__container">
			<header class="post-content__header">
				<h1 class="post-content__title" id="post-content-title"><?php the_title(); ?></h1>
			</header>
			<div class="post-content__body entry-content">
				<?php the_content(); ?>
			</div>
		</div>
	</div>
</article>
