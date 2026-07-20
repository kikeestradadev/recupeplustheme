<?php
// Función que maneja el shortcode
function scName2() {
	$html = '<h1>This is a ShortCode Example</h1>';
	return $html;
}
// Registrando el shortcode en WordPress
add_shortcode('sc_name2', 'scName2');
