<?php
/**
 * WooCommerce product helpers for Recupé Plus theme modules.
 *
 * @package wpboilerplate
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Whether WooCommerce is available.
 *
 * @return bool
 */
function wpb_wc_ready() {
	return class_exists( 'WooCommerce' ) && function_exists( 'wc_get_products' );
}

/**
 * Product catalog definition (LP suplementos lines).
 *
 * @return array<int, array<string, mixed>>
 */
function wpb_wc_catalog_definition() {
	return array(
		array(
			'folder'   => 'VITAMIN',
			'name'     => 'Vitamin',
			'slug'     => 'vitamin',
			'category' => 'gomitas',
			'modifier' => 'vitamin',
			'price'    => '8900',
			'text'     => 'Mora + vitamina C para cuidar tus defensas todos los días.',
			'short'    => array(
				'Suplemento alimenticio en gomitas a base de mora y vitamina C.',
				'Apoya tus defensas todos los días con una fórmula práctica y deliciosa.',
			),
		),
		array(
			'folder'   => 'ANTIOX',
			'name'     => 'Antiox',
			'slug'     => 'antiox',
			'category' => 'gomitas',
			'modifier' => 'antiox',
			'price'    => '8900',
			'text'     => 'Para sumar antioxidantes a tu rutina.',
			'short'    => array(
				'Suplemento alimenticio en gomitas con activos antioxidantes.',
				'Para sumar antioxidantes a tu rutina diaria.',
			),
		),
		array(
			'folder'   => 'C-MAGNIFIC',
			'name'     => 'C-Magnific',
			'slug'     => 'c-magnific',
			'category' => 'gomitas',
			'modifier' => 'cmagnific',
			'price'    => '8900',
			'text'     => 'Magnesio + vitamina C para tu bienestar diario.',
			'short'    => array(
				'Suplemento alimenticio en gomitas con magnesio y vitamina C.',
				'Pensado para acompañar tu bienestar diario.',
			),
		),
		array(
			'folder'   => 'SERENITY',
			'name'     => 'Serenity',
			'slug'     => 'serenity',
			'category' => 'gomitas',
			'modifier' => 'serenity',
			'price'    => '8900',
			'text'     => 'Para bajar revoluciones y crear tu ritual de descanso.',
			'short'    => array(
				'Suplemento alimenticio en gomitas para tu ritual de descanso.',
				'Para bajar revoluciones al final del día.',
			),
		),
		array(
			'folder'   => 'ARE MORE',
			'name'     => 'Are More',
			'slug'     => 'are-more',
			'category' => 'gomitas',
			'modifier' => 'more',
			'price'    => '15000',
			'text'     => 'Hongos funcionales + vitamina C para tu bienestar diario.',
			'short'    => array(
				'Suplemento alimenticio en gomitas a base de hongos funcionales y vitamina C.',
				'Para reforzar tu bienestar diario.',
			),
		),
		array(
			'folder'   => 'MEMENTO',
			'name'     => 'Momento',
			'slug'     => 'momento',
			'category' => 'capsulas',
			'modifier' => 'momento',
			'price'    => '25000',
			'text'     => 'Para acompañar tus momentos de enfoque.',
			'short'    => array(
				'Suplemento alimenticio en cápsulas a base de hongos: LION’S MANE y CORDYCEPS.',
				'Para acompañar tus momentos de enfoque.',
			),
		),
		array(
			'folder'   => 'ACTIVITY',
			'name'     => 'Activity',
			'slug'     => 'activity',
			'category' => 'capsulas',
			'modifier' => 'activity',
			'price'    => '25000',
			'text'     => 'Para activar tu energía antes de moverte.',
			'short'    => array(
				'Suplemento alimenticio en cápsulas a base de hongos: CORDYCEPS y LION’S MANE.',
				'Para activar tu energía antes de moverte.',
			),
		),
		array(
			'folder'   => 'RECOVERY',
			'name'     => 'Recovery',
			'slug'     => 'recovery',
			'category' => 'capsulas',
			'modifier' => 'recovery',
			'price'    => '25000',
			'text'     => 'Para acompañar tu recuperación después de hacer ejercicio.',
			'short'    => array(
				'Suplemento alimenticio en cápsulas a base de hongos: REISHI, CORDYCEPS y LION’S MANE.',
				'Para acompañar tu recuperación después de hacer ejercicio.',
			),
		),
	);
}

/**
 * Get published products in a product_cat slug.
 *
 * @param string $category_slug Category slug (gomitas|capsulas).
 * @return WC_Product[]
 */
function wpb_wc_products_by_category( $category_slug ) {
	if ( ! wpb_wc_ready() ) {
		return array();
	}

	$products = wc_get_products(
		array(
			'status'   => 'publish',
			'limit'    => -1,
			'orderby'  => 'menu_order',
			'order'    => 'ASC',
			'category' => array( sanitize_title( $category_slug ) ),
		)
	);

	return is_array( $products ) ? $products : array();
}

/**
 * Resolve a WC product for the showcase (current single, ?sku=, or first Activity/catalog).
 *
 * @return WC_Product|null
 */
function wpb_wc_resolve_showcase_product() {
	if ( ! wpb_wc_ready() ) {
		return null;
	}

	if ( is_singular( 'product' ) ) {
		$product = wc_get_product( get_the_ID() );
		return $product instanceof WC_Product ? $product : null;
	}

	$sku_or_slug = isset( $_GET['sku'] ) ? sanitize_title( wp_unslash( $_GET['sku'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
	if ( '' === $sku_or_slug && isset( $_GET['product'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		$sku_or_slug = sanitize_title( wp_unslash( $_GET['product'] ) ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
	}

	if ( '' !== $sku_or_slug ) {
		$by_sku = wc_get_product_id_by_sku( $sku_or_slug );
		if ( $by_sku ) {
			$product = wc_get_product( $by_sku );
			if ( $product instanceof WC_Product ) {
				return $product;
			}
		}
		$post = get_page_by_path( $sku_or_slug, OBJECT, 'product' );
		if ( $post ) {
			$product = wc_get_product( $post->ID );
			if ( $product instanceof WC_Product ) {
				return $product;
			}
		}
	}

	$activity = get_page_by_path( 'activity', OBJECT, 'product' );
	if ( $activity ) {
		$product = wc_get_product( $activity->ID );
		if ( $product instanceof WC_Product ) {
			return $product;
		}
	}

	$products = wc_get_products(
		array(
			'status' => 'publish',
			'limit'  => 1,
			'orderby'=> 'menu_order',
			'order'  => 'ASC',
		)
	);

	return ( ! empty( $products[0] ) && $products[0] instanceof WC_Product ) ? $products[0] : null;
}

/**
 * Gallery image payloads for the showcase (featured + gallery attachment IDs).
 *
 * @param WC_Product $product Product.
 * @return array<int, array{src:string,alt:string}>
 */
function wpb_wc_product_gallery_images( WC_Product $product ) {
	$ids = array();
	$featured = (int) $product->get_image_id();
	if ( $featured ) {
		$ids[] = $featured;
	}
	foreach ( $product->get_gallery_image_ids() as $gid ) {
		$gid = (int) $gid;
		if ( $gid && ! in_array( $gid, $ids, true ) ) {
			$ids[] = $gid;
		}
	}

	$images = array();
	foreach ( $ids as $attachment_id ) {
		$url = wp_get_attachment_image_url( $attachment_id, 'full' );
		if ( ! $url ) {
			continue;
		}
		$thumb = wp_get_attachment_image_url( $attachment_id, 'woocommerce_gallery_thumbnail' );
		$alt   = get_post_meta( $attachment_id, '_wp_attachment_image_alt', true );
		if ( '' === $alt ) {
			$alt = $product->get_name();
		}
		$images[] = array(
			'src'   => $url,
			'thumb' => $thumb ? $thumb : $url,
			'alt'   => $alt,
		);
	}

	return $images;
}

/**
 * Related products for showcase slider (same shop catalog, exclude current).
 *
 * @param WC_Product $product Current product.
 * @param int        $limit   Max items.
 * @return WC_Product[]
 */
function wpb_wc_related_products( WC_Product $product, $limit = 8 ) {
	if ( ! wpb_wc_ready() ) {
		return array();
	}

	$related_ids = wc_get_related_products( $product->get_id(), $limit );
	$items       = array();

	if ( ! empty( $related_ids ) ) {
		foreach ( $related_ids as $rid ) {
			$p = wc_get_product( $rid );
			if ( $p instanceof WC_Product && $p->is_visible() ) {
				$items[] = $p;
			}
		}
	}

	if ( count( $items ) >= 3 ) {
		return $items;
	}

	$fallback = wc_get_products(
		array(
			'status'  => 'publish',
			'limit'   => $limit + 1,
			'exclude' => array( $product->get_id() ),
			'orderby' => 'menu_order',
			'order'   => 'ASC',
		)
	);

	foreach ( (array) $fallback as $p ) {
		if ( $p instanceof WC_Product ) {
			$items[] = $p;
		}
		if ( count( $items ) >= $limit ) {
			break;
		}
	}

	return $items;
}

/**
 * Format display price for CRC placeholder catalog.
 *
 * @param WC_Product $product Product.
 * @return string
 */
function wpb_wc_format_display_price( WC_Product $product ) {
	$raw = (float) $product->get_price();
	if ( $raw <= 0 ) {
		return wp_strip_all_tags( wc_price( 0 ) );
	}
	return '₡' . number_format( $raw, 0, ',', ' ' );
}

/**
 * Primary product category label + link for meta row.
 *
 * @param WC_Product $product Product.
 * @return array{label:string,url:string}
 */
function wpb_wc_primary_category( WC_Product $product ) {
	$terms = get_the_terms( $product->get_id(), 'product_cat' );
	if ( empty( $terms ) || is_wp_error( $terms ) ) {
		return array(
			'label' => __( 'Suplementos', 'wpboilerplate' ),
			'url'   => home_url( '/suplementos/' ),
		);
	}

	$term = $terms[0];
	$anchor = ( 'capsulas' === $term->slug ) ? 'capsulas' : ( ( 'gomitas' === $term->slug ) ? 'gomitas' : '' );

	return array(
		'label' => $term->name,
		'url'   => home_url( '/suplementos/' . ( $anchor ? '#' . $anchor : '' ) ),
	);
}

/**
 * Presentation key for a catalog product: tarro or bolsa.
 *
 * @param WC_Product $product Product.
 * @return string
 */
function wpb_wc_product_presentacion( WC_Product $product ) {
	$value = $product->get_meta( '_wpb_presentacion', true );
	return 'bolsa' === $value ? 'bolsa' : 'tarro';
}

/**
 * Visible label for a presentation key.
 *
 * @param string $key tarro|bolsa.
 * @return string
 */
function wpb_wc_product_presentacion_label( $key ) {
	if ( 'bolsa' === $key ) {
		return __( 'Paquetes de bolsas', 'wpboilerplate' );
	}
	return __( 'Productos en tarros', 'wpboilerplate' );
}

/**
 * Technical sheet attachment. Only jar products keep one.
 *
 * @param WC_Product $product Product.
 * @return int
 */
function wpb_wc_product_ficha_id( WC_Product $product ) {
	if ( 'tarro' !== wpb_wc_product_presentacion( $product ) ) {
		return 0;
	}
	return (int) $product->get_meta( '_wpb_ficha_tecnica', true );
}
