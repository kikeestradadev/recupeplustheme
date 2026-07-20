<?php
if ( !function_exists( 'wpb_setup' ) ):
	function wpb_setup () {
	//https://developer.wordpress.org/reference/functions/add_theme_support/
	add_theme_support( 'post-thumbnails' );
	//add_image_size( name, width, height, crop );
	add_theme_support('html5', array(
		'comment-list',
		'comment-form',
		'search-form',
		'gallery',
		'caption'
	));
	
	// Registrar ubicaciones de menús
	register_nav_menus( array(
		'main-menu' => esc_html__( 'Main Menu', 'wpboilerplate' ),
	) );
}
endif;
add_action( 'after_setup_theme', 'wpb_setup' );

// Eliminar metadatos de publicación y categorías
function wpb_remove_post_metadata() {
	// Remover fecha de publicación
	add_filter( 'the_date', '__return_empty_string' );
	add_filter( 'get_the_date', '__return_empty_string' );
	
	// Remover categorías
	add_filter( 'the_category', '__return_empty_string' );
	add_filter( 'get_the_category_list', '__return_empty_string' );
	
	// Remover entry-meta completo
	add_filter( 'twentytwentyone_post_meta_list', '__return_empty_string' );
}
add_action( 'init', 'wpb_remove_post_metadata' );
