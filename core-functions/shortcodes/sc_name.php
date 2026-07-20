<?php
// Función que maneja el shortcode
function scName() {
    return '<h1>This is a ShortCode Example</h1>';
}
// Registrando el shortcode en WordPress
add_shortcode('sc_name', 'scName');
