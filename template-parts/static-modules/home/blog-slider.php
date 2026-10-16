<?php
/**
 * Module: blog-slider (Home Guías / Block Slider).
 *
 * Slides come from the 5 most recent published posts (same content source
 * as the Guías page grid). Section title stays editable via ACF.
 *
 * @package wpboilerplate
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$title = function_exists( 'wpb_acf_option_text' ) ? wpb_acf_option_text( 'home_blog_title' ) : '';
if ( '' === $title ) {
	$title = __( 'Guías para sentirte mejor', 'wpboilerplate' );
}

$guias_page = get_page_by_path( 'guias' );
$guias_url  = $guias_page instanceof WP_Post
	? get_permalink( $guias_page )
	: home_url( '/guias/' );

$guides_query = new WP_Query(
	array(
		'post_type'              => 'post',
		'post_status'            => 'publish',
		'posts_per_page'         => 5,
		'orderby'                => 'date',
		'order'                  => 'DESC',
		'ignore_sticky_posts'    => true,
		'no_found_rows'          => true,
		'update_post_meta_cache' => true,
		'update_post_term_cache' => false,
	)
);

$cards = array();

if ( $guides_query->have_posts() ) {
	while ( $guides_query->have_posts() ) {
		$guides_query->the_post();

		$post_id   = get_the_ID();
		$permalink = get_permalink( $post_id );
		$name      = get_the_title( $post_id );
		$image_url = get_the_post_thumbnail_url( $post_id, 'medium_large' );
		if ( ! $image_url ) {
			$image_url = get_the_post_thumbnail_url( $post_id, 'full' );
		}

		$width  = 0;
		$height = 0;
		$thumb_id = get_post_thumbnail_id( $post_id );
		if ( $thumb_id ) {
			$meta = wp_get_attachment_image_src( $thumb_id, 'medium_large' );
			if ( ! $meta ) {
				$meta = wp_get_attachment_image_src( $thumb_id, 'full' );
			}
			if ( is_array( $meta ) ) {
				$width  = isset( $meta[1] ) ? (int) $meta[1] : 0;
				$height = isset( $meta[2] ) ? (int) $meta[2] : 0;
			}
		}

		$cards[] = array(
			'image' => $image_url
				? array(
					'url'    => $image_url,
					'width'  => $width ? $width : 640,
					'height' => $height ? $height : 427,
				)
				: null,
			'name'  => $name ? $name : '',
			'alt'   => $name ? $name : '',
			'link'  => array(
				'url'    => $permalink ? $permalink : '',
				'target' => '',
			),
		);
	}
	wp_reset_postdata();
}

if ( empty( $cards ) ) {
	return;
}
?>
<section class="blog-slider">
	<div class="main-container blog-slider__shell">
		<div class="container blog-slider__container blog-slider__inner">
			<?php if ( '' !== $title ) : ?>
				<h2 class="blog-slider__title"><?php echo esc_html( $title ); ?></h2>
			<?php endif; ?>
			<div class="blog-slider__slider">
				<button class="blog-slider__nav blog-slider__nav--prev" type="button" aria-label="<?php esc_attr_e( 'Anterior', 'wpboilerplate' ); ?>"></button>
				<button class="blog-slider__nav blog-slider__nav--next" type="button" aria-label="<?php esc_attr_e( 'Siguiente', 'wpboilerplate' ); ?>"></button>
				<div class="blog-slider__swiper swiper">
					<div class="blog-slider__wrapper swiper-wrapper">
						<?php foreach ( $cards as $card ) : ?>
							<?php
							$has_link = ! empty( $card['link']['url'] );
							$tag      = $has_link ? 'a' : 'div';
							?>
							<article class="blog-slider__slide swiper-slide">
								<<?php echo tag_escape( $tag ); ?>
									class="blog-slider__card"
									<?php if ( $has_link ) : ?>
										href="<?php echo esc_url( $card['link']['url'] ); ?>"
									<?php endif; ?>
								>
									<?php if ( $card['image'] ) : ?>
										<div class="blog-slider__media">
											<img
												class="blog-slider__image"
												src="<?php echo esc_url( $card['image']['url'] ); ?>"
												alt="<?php echo esc_attr( $card['alt'] ); ?>"
												width="<?php echo esc_attr( (string) $card['image']['width'] ); ?>"
												height="<?php echo esc_attr( (string) $card['image']['height'] ); ?>"
												loading="lazy"
											>
										</div>
									<?php endif; ?>
									<?php if ( '' !== $card['name'] ) : ?>
										<h3 class="blog-slider__name"><?php echo esc_html( $card['name'] ); ?></h3>
									<?php endif; ?>
								</<?php echo tag_escape( $tag ); ?>>
							</article>
						<?php endforeach; ?>
					</div>
					<div class="blog-slider__pagination swiper-pagination"></div>
				</div>
			</div>
			<?php if ( $guias_url ) : ?>
				<div class="blog-slider__more">
					<a class="blog-slider__more-link" href="<?php echo esc_url( $guias_url ); ?>">
						<?php esc_html_e( 'Ver más', 'wpboilerplate' ); ?>
					</a>
				</div>
			<?php endif; ?>
		</div>
	</div>
</section>
