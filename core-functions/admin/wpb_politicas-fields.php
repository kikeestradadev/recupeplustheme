<?php
/**
 * ACF fields for the three policy pages.
 * The page templates read these options. The original text is stored once.
 *
 * @package wpboilerplate
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * WYSIWYG field for a policy page.
 *
 * @param string $key   Field key.
 * @param string $name  Field name.
 * @param string $label Field label.
 * @return array<string, mixed>
 */
function wpb_politicas_wysiwyg_field( $key, $name, $label ) {
	return array(
		'key'          => $key,
		'label'        => $label,
		'name'         => $name,
		'type'         => 'wysiwyg',
		'tabs'         => 'all',
		'toolbar'      => 'full',
		'media_upload' => 0,
	);
}

/**
 * Registers the Políticas content field group.
 */
function wpb_register_politicas_fields() {
	wpb_acf_home_field_group(
		'group_wpb_politicas',
		__( 'Contenido', 'wpboilerplate' ),
		'wpb-politicas-contenido',
		array(
			wpb_politicas_wysiwyg_field( 'field_wpb_politicas_devoluciones', 'politicas_devoluciones', __( 'Política de devoluciones', 'wpboilerplate' ) ),
			wpb_politicas_wysiwyg_field( 'field_wpb_politicas_terminos', 'politicas_terminos', __( 'Términos y condiciones', 'wpboilerplate' ) ),
			wpb_politicas_wysiwyg_field( 'field_wpb_politicas_privacidad', 'politicas_privacidad', __( 'Política de privacidad', 'wpboilerplate' ) ),
		)
	);
}
add_action( 'acf/init', 'wpb_register_politicas_fields' );

/**
 * Original policy HTML, copied from el sitio original.
 *
 * @return array<string, string>
 */
function wpb_politicas_original_content() {
	return array(
		'devoluciones' => <<<'HTML'
<p>Aceptamos devoluciones dentro de los 7 días siguientes a la recepción del producto, siempre que esté sin abrir y en su empaque original. Los gastos de envío solo se cubren si el producto llegó dañado o incorrecto. Para iniciar una devolución, comuníquese a isolatecr@gmail.com con su número de pedido.</p>
HTML,
		'terminos' => <<<'HTML'
<p>Esta página contiene los Términos y Condiciones de IsolateCR operando bajo la marca Recupé Plus.</p>

<h2>1. General </h2>

<p>Este sitio web es operado por IsolateCR. A lo largo del sitio, los términos “nosotros”, “nos” y “nuestro” se refieren a IsolateCR y cualquiera de nuestras marcas que incluyen Sö Flow Co. y RECUPÉ Plus. Al visitar nuestro sitio o comprarnos algo, usted se compromete con nuestro “Servicio” y acepta los siguientes términos y condiciones.</p>

<h2>2. Cambios </h2>

<p>Nos reservamos el derecho de actualizar, modificar o reemplazar cualquier parte de estos Términos de Servicio publicando actualizaciones y/o cambios en nuestro sitio web. Es su responsabilidad revisar esta página periódicamente para verificar cambios. El uso continuo del sitio constituye su aceptación de los cambios.</p>

<h2>3. Uso de la Tienda </h2>

<p>Al aceptar estos Términos de Servicio, usted declara que es mayor de edad en su jurisdicción, y nos otorga consentimiento para que sus dependientes menores usen este sitio.</p>

<h2>4. Modificaciones del Servicio </h2>

<p>Nos reservamos el derecho de modificar o discontinuar el Servicio, o cualquier parte del contenido, en cualquier momento sin previo aviso. Además, nos reservamos el derecho de cambiar estos Términos y Condiciones en cualquier momento. Recomendamos a los usuarios revisar el sitio web con regularidad para asegurarse de estar al tanto de la versión más reciente.</p>

<h2>5. Productos y Servicios </h2>

<p>Ciertos productos o servicios pueden estar disponibles exclusivamente en línea. Pueden tener cantidades limitadas y están sujetos a devolución de acuerdo con nuestra política de devoluciones.</p>

<h2>6. Información Personal </h2>

<p>El envío de su información personal a través de la tienda se rige por nuestra Política de Privacidad.</p>

<h2>7. Renuncia de Garantías </h2>

<p>No garantizamos que el uso del servicio será ininterrumpido o libre de errores. Usted acepta que el uso del servicio es bajo su propio riesgo.</p>

<h2>8. Contacto </h2>

<p>Para dudas o reclamos, escríbanos a: isolatecr@gmail.com</p>

<p></p>
HTML,
		'privacidad' => <<<'HTML'
<ol>
<li><strong>Introducción</strong></li>
</ol>

<p>En IsolateCR, operando bajo las marcas Sö Flow Co. y RECUPÉ Plus, valoramos y respetamos su privacidad. Esta Política de Privacidad explica cómo recopilamos, usamos y protegemos su información personal cuando visita nuestro sitio web o realiza una compra a través de nuestra tienda en línea.</p>

<ol start="2">
<li><strong>Información que Recopilamos</strong></li>
</ol>

<p>Podemos recopilar la siguiente información:</p>

<p>Datos de contacto: nombre, correo electrónico, número de teléfono, dirección de envío y facturación.</p>

<p>Información de pago procesada de forma segura a través de nuestros proveedores de pago (ejemplo: Stripe, PayPal, tarjeta de crédito).</p>

<p>Información de cuenta, si crea un perfil en la tienda.</p>

<p>Datos técnicos como dirección IP, tipo de navegador, cookies y comportamiento de navegación en el sitio.</p>

<ol start="3">
<li><strong>Uso de la Información</strong></li>
</ol>

<p>Utilizamos su información para:</p>

<p>Procesar y enviar pedidos.</p>

<p>Gestionar pagos y facturación.</p>

<p>Enviar actualizaciones sobre pedidos, promociones y novedades (si usted acepta suscribirse).</p>

<p>Mejorar nuestros servicios y la experiencia de usuario.</p>

<p>Cumplir con obligaciones legales y fiscales.</p>

<ol start="4">
<li><strong>Compartir Información</strong></li>
</ol>

<p>No vendemos ni alquilamos su información personal. Solo podemos compartirla con:</p>

<p>Proveedores de servicios de pago y logística.</p>

<p>Plataformas de marketing (como email marketing), únicamente si usted ha dado su consentimiento.</p>

<p>Autoridades competentes, cuando la ley lo requiera.</p>

<ol start="5">
<li><strong>Seguridad</strong></li>
</ol>

<p>Adoptamos medidas razonables de seguridad para proteger sus datos personales. Sin embargo, debe tener en cuenta que ningún sistema de transmisión de datos por Internet es completamente seguro.</p>

<ol start="6">
<li><strong>Cookies</strong></li>
</ol>

<p>Nuestro sitio utiliza cookies y tecnologías similares para:</p>

<p>Mejorar su experiencia de navegación.</p>

<p>Analizar el tráfico en el sitio.</p>

<p>Recordar sus preferencias y carrito de compras.</p>

<p>Usted puede gestionar y deshabilitar las cookies desde la configuración de su navegador, aunque esto podría afectar el funcionamiento de algunas secciones de la tienda.</p>

<ol start="7">
<li><strong>Derechos del Usuario</strong></li>
</ol>

<p>Usted tiene derecho a:</p>

<p>Acceder, corregir o eliminar sus datos personales.</p>

<p>Retirar su consentimiento para recibir comunicaciones de marketing.</p>

<p>Solicitar la limitación del uso de sus datos.</p>

<p>Para ejercer estos derechos puede escribirnos al correo: isolatecr@gmail.com.</p>

<ol start="8">
<li><strong>Conservación de Datos</strong></li>
</ol>

<p>Conservamos sus datos mientras tenga una cuenta activa en nuestro sitio o mientras sea necesario para cumplir con nuestras obligaciones legales (por ejemplo, facturación y registros contables).</p>

<ol start="9">
<li><strong>Cambios a esta Política</strong></li>
</ol>

<p>Nos reservamos el derecho de actualizar esta Política de Privacidad en cualquier momento. Cualquier modificación será publicada en esta página con su respectiva fecha de actualización.</p>

<ol start="10">
<li><strong>Contacto</strong></li>
</ol>

<p>Si tiene preguntas o dudas sobre esta Política de Privacidad, puede comunicarse con nosotros en:<br>📧 isolatecr@gmail.com</p>
HTML,
	);
}

/**
 * Store the original text the first time each field is empty.
 */
function wpb_seed_politicas_content() {
	$fields = array(
		'politicas_devoluciones' => array( 'field_wpb_politicas_devoluciones', 'devoluciones' ),
		'politicas_terminos'     => array( 'field_wpb_politicas_terminos', 'terminos' ),
		'politicas_privacidad'   => array( 'field_wpb_politicas_privacidad', 'privacidad' ),
	);
	$content = wpb_politicas_original_content();

	foreach ( $fields as $name => $meta ) {
		$current = get_option( 'options_' . $name, false );
		if ( false !== $current && '' !== $current ) {
			continue;
		}
		update_option( 'options_' . $name, $content[ $meta[1] ] );
		update_option( '_options_' . $name, $meta[0] );
	}
}
add_action( 'acf/init', 'wpb_seed_politicas_content', 20 );

/**
 * Use the policy page templates, whose slugs do not match the template file names.
 */
function wpb_assign_policy_page_templates() {
	$map = array(
		'page-politica-de-devoluciones' => 'page-politica-de-devoluciones.php',
		'page-terminos-y-condiciones'   => 'page-terminos-y-condiciones.php',
		'page-politica-de-privacidad'   => 'page-politica-de-privacidad.php',
	);

	foreach ( $map as $slug => $template ) {
		$page = get_page_by_path( $slug );
		if ( ! $page instanceof WP_Post ) {
			continue;
		}
		if ( get_page_template_slug( $page->ID ) === $template ) {
			continue;
		}
		update_post_meta( $page->ID, '_wp_page_template', $template );
	}
}
add_action( 'init', 'wpb_assign_policy_page_templates' );
