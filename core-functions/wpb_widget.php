<?php
function top_widget() {
    register_sidebar( array(
        'name' => __( 'Top Widget', 'my_theme' ),
        'id' => 'top-widget',
        'description' => __( 'Widgets in this area will be shown in the top widget area.', 'my_theme' ),
        'before_widget' => '<div id="%1$s" class="widget %2$s">',
        'after_widget' => '</div>',
        'before_title' => '<h2 class="widget-title">',
        'after_title' => '</h2>',
    ) );
}
add_action( 'widgets_init', 'top_widget' );
