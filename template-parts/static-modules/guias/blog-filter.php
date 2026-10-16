<?php
/**
 * Module: blog-filter (Guías grid).
 *
 * Cards are built from published posts via WP_Query.
 * Filter chips keep the existing markup; data-category uses category slugs
 * (bienestar-diario → bienestar to match the chip data-filter).
 *
 * @package wpboilerplate
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$guides_query = new WP_Query(
	array(
		'post_type'              => 'post',
		'post_status'            => 'publish',
		'posts_per_page'         => -1,
		'orderby'                => 'date',
		'order'                  => 'DESC',
		'ignore_sticky_posts'    => true,
		'no_found_rows'          => true,
		'update_post_meta_cache' => true,
		'update_post_term_cache' => true,
	)
);

$guide_cards = array();

if ( $guides_query->have_posts() ) {
	while ( $guides_query->have_posts() ) {
		$guides_query->the_post();

		$post_id   = get_the_ID();
		$permalink = get_permalink( $post_id );
		$title     = get_the_title( $post_id );
		$image_url = get_the_post_thumbnail_url( $post_id, 'medium_large' );
		if ( ! $image_url ) {
			$image_url = get_the_post_thumbnail_url( $post_id, 'full' );
		}

		$terms          = get_the_terms( $post_id, 'category' );
		$category_name  = '';
		$category_slugs = array();
		$default_cat    = (int) get_option( 'default_category' );

		if ( is_array( $terms ) ) {
			foreach ( $terms as $term ) {
				if ( ! $term instanceof WP_Term ) {
					continue;
				}
				if ( $default_cat === (int) $term->term_id ) {
					continue;
				}
				if ( '' === $category_name ) {
					$category_name = $term->name;
				}
				$slug = (string) $term->slug;
				// Match existing filter chip values in blogFilter.js.
				if ( 'bienestar-diario' === $slug ) {
					$slug = 'bienestar';
				}
				$category_slugs[] = $slug;
			}
			if ( '' === $category_name && isset( $terms[0] ) && $terms[0] instanceof WP_Term ) {
				$category_name = $terms[0]->name;
				$slug          = (string) $terms[0]->slug;
				if ( 'bienestar-diario' === $slug ) {
					$slug = 'bienestar';
				}
				$category_slugs[] = $slug;
			}
		}

		$category_slugs = array_values( array_unique( array_filter( $category_slugs ) ) );

		$guide_cards[] = array(
			'permalink'      => $permalink ? $permalink : '#',
			'title'          => $title,
			'image_url'      => $image_url ? $image_url : '',
			'category_name'  => $category_name,
			'category_slugs' => $category_slugs,
		);
	}
	wp_reset_postdata();
}
?>
<section class="blog-filter" data-blog-filter data-per-page="6">
	<div class="main-container blog-filter__shell">
		<div class="container blog-filter__container blog-filter__inner">
			<div class="blog-filter__header">
				<h2 class="blog-filter__title">Guías</h2>
				<a class="blog-filter__more" href="#"><span>Ver más</span><span class="blog-filter__more-arrow" aria-hidden="true">→</span></a>
			</div>
			<div class="blog-filter__filters">
				<button class="blog-filter__filters-nav blog-filter__filters-nav--prev" type="button" aria-label="Filtros anteriores"></button>
				<button class="blog-filter__filters-nav blog-filter__filters-nav--next" type="button" aria-label="Filtros siguientes"></button>
				<div class="blog-filter__filters-fade blog-filter__filters-fade--start" aria-hidden="true"></div>
				<div class="blog-filter__filters-fade blog-filter__filters-fade--end" aria-hidden="true"></div>
				<div class="blog-filter__filters-swiper swiper">
					<div class="blog-filter__filters-wrapper swiper-wrapper">
						<div class="blog-filter__filters-slide swiper-slide">
							<button class="blog-filter__chip blog-filter__chip--active" type="button" data-filter="all" aria-pressed="true"><span>Todas</span>
							</button>
						</div>
						<div class="blog-filter__filters-slide swiper-slide">
							<button class="blog-filter__chip" type="button" data-filter="rutina" aria-pressed="false">
								<svg class="blog-filter__chip-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" aria-hidden="true">
									<circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="1.6"></circle>
									<path d="M12 7v5l3 2" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"></path>
								</svg><span>Rutina</span>
							</button>
						</div>
						<div class="blog-filter__filters-slide swiper-slide">
							<button class="blog-filter__chip" type="button" data-filter="descanso" aria-pressed="false">
								<svg class="blog-filter__chip-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" aria-hidden="true">
									<path d="M18 14.5A7.5 7.5 0 0 1 9.5 6 6.5 6.5 0 1 0 18 14.5Z" stroke="currentColor" stroke-width="1.6" stroke-linejoin="round"></path>
								</svg><span>Descanso</span>
							</button>
						</div>
						<div class="blog-filter__filters-slide swiper-slide">
							<button class="blog-filter__chip" type="button" data-filter="ingredientes" aria-pressed="false">
								<svg class="blog-filter__chip-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" aria-hidden="true">
									<path d="M5 19c4-1 6-5 7-10 1 5 3 9 7 10-3 2-11 2-14 0Z" stroke="currentColor" stroke-width="1.6" stroke-linejoin="round"></path>
									<path d="M12 9V4" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"></path>
								</svg><span>Ingredientes</span>
							</button>
						</div>
						<div class="blog-filter__filters-slide swiper-slide">
							<button class="blog-filter__chip" type="button" data-filter="hongos" aria-pressed="false">
								<svg class="blog-filter__chip-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" aria-hidden="true">
									<path d="M4 12c0-4.4 3.6-8 8-8s8 3.6 8 8H4Z" stroke="currentColor" stroke-width="1.6" stroke-linejoin="round"></path>
									<path d="M10 12v6a2 2 0 0 0 4 0v-6" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"></path>
								</svg><span>Hongos</span>
							</button>
						</div>
						<div class="blog-filter__filters-slide swiper-slide">
							<button class="blog-filter__chip" type="button" data-filter="bienestar" aria-pressed="false">
								<svg class="blog-filter__chip-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" aria-hidden="true">
									<path d="M12 19s-7-4.4-7-9a3.8 3.8 0 0 1 7-2 3.8 3.8 0 0 1 7 2c0 4.6-7 9-7 9Z" stroke="currentColor" stroke-width="1.6" stroke-linejoin="round"></path>
								</svg><span>Bienestar diario</span>
							</button>
						</div>
					</div>
				</div>
			</div>
			<div class="blog-filter__grid" data-blog-grid>
				<?php foreach ( $guide_cards as $card ) : ?>
					<?php $cat_attr = implode( ' ', $card['category_slugs'] ); ?>
					<article class="blog-filter__card" data-category="<?php echo esc_attr( $cat_attr ); ?>">
						<a class="blog-filter__media" href="<?php echo esc_url( $card['permalink'] ); ?>">
							<?php if ( $card['image_url'] ) : ?>
								<img class="blog-filter__image" src="<?php echo esc_url( $card['image_url'] ); ?>" alt="" width="400" height="260" loading="lazy">
							<?php endif; ?>
						</a>
						<?php if ( $card['category_name'] ) : ?>
							<p class="blog-filter__category"><?php echo esc_html( $card['category_name'] ); ?></p>
						<?php endif; ?>
						<h3 class="blog-filter__name"><a href="<?php echo esc_url( $card['permalink'] ); ?>"><?php echo esc_html( $card['title'] ); ?></a></h3>
						<a class="blog-filter__link" href="<?php echo esc_url( $card['permalink'] ); ?>"><span>Leer guía</span><span class="blog-filter__link-arrow" aria-hidden="true">→</span></a>
					</article>
				<?php endforeach; ?>
			</div>
			<div class="blog-filter__mobile">
				<div class="blog-filter__swiper swiper" data-blog-swiper>
					<div class="blog-filter__wrapper swiper-wrapper" data-blog-mobile-track>
						<?php foreach ( $guide_cards as $card ) : ?>
							<?php $cat_attr = implode( ' ', $card['category_slugs'] ); ?>
							<article class="blog-filter__slide swiper-slide" data-category="<?php echo esc_attr( $cat_attr ); ?>">
								<div class="blog-filter__card">
									<a class="blog-filter__media" href="<?php echo esc_url( $card['permalink'] ); ?>">
										<?php if ( $card['image_url'] ) : ?>
											<img class="blog-filter__image" src="<?php echo esc_url( $card['image_url'] ); ?>" alt="" width="400" height="260" loading="lazy">
										<?php endif; ?>
									</a>
									<?php if ( $card['category_name'] ) : ?>
										<p class="blog-filter__category"><?php echo esc_html( $card['category_name'] ); ?></p>
									<?php endif; ?>
									<h3 class="blog-filter__name"><a href="<?php echo esc_url( $card['permalink'] ); ?>"><?php echo esc_html( $card['title'] ); ?></a></h3>
									<a class="blog-filter__link" href="<?php echo esc_url( $card['permalink'] ); ?>"><span>Leer guía</span><span class="blog-filter__link-arrow" aria-hidden="true">→</span></a>
								</div>
							</article>
						<?php endforeach; ?>
					</div>
				</div>
				<div class="blog-filter__bullets swiper-pagination"></div>
			</div>
			<nav class="blog-filter__pagination" data-blog-pagination aria-label="Paginación del blog"></nav>
		</div>
	</div>
</section>
