<?php
/**
 * Admin new order email
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/emails/admin-new-order.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates\Emails\HTML
 * @version 3.7.0
 */

defined('ABSPATH') || exit;

/*
 * @hooked WC_Emails::email_header() Output the email header
 */
do_action('woocommerce_email_header', $email_heading, $email);?>

<?php /* translators: %s: Customer billing full name */
global $wpdb;

/*
?>

<p>
<?php printf(esc_html__('You’ve received the following order from %s:', 'woocommerce'), $order->get_formatted_billing_full_name()); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>

</p>
 */

$tracking   = $order->get_meta('tracking_number', true);
$tipo_envio = $order->get_meta('tipo_envio', true);
$url_envio  = "";

$taxonomy = "";
$nombre_proveedor = "";

if($tracking != ""){
    $taxonomy = "metodoenvios";
    $terms = get_terms([
        'taxonomy' => $taxonomy,
        'hide_empty' => false,
        'slug'      => $tipo_envio
        ]);

    if(count($terms)){
        $term = $terms[0];

        $url_envio = get_term_meta( $term->term_id, 'shippingwoo-url-tracking', true );
        $nombre_proveedor = $term->name;

    }
}

$nombre     = "";
$estatus    = "";
$subject    = "";
$message    = "";

//DEBEMOS HACER LA CONSULTA DE LO QUE DICE EL EMAIL
$emails = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}emails_shippingwoo");
foreach($emails as $email){
    $nombre     = $email->nombre;
    $estatus    = $email->estatus;
    $subject    = $email->sujeto;//shippingwoo_subject
    $message    = $email->mensaje;//shippingwoo_message
}


$message = str_replace("{name}", $nombre_proveedor, $message);
$message = str_replace("{tracking}", $tracking, $message);
$message = str_replace("{url}", $url_envio, $message);


/* ESTE ERA EL MENSAJE ANTERIOR QUE NO ESTAMOS UTILIZANDO
?>
<p><?php printf(esc_html__('Este es tu numero de Tracking %s', 'woocommerce'), $order->get_meta('tracking', true)); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></p>
<?php
*/

?>
<!--
<p>Caramba  2.0.2!</p>
-->
<p><?php echo esc_html__($message); ?></p>

<?php

/*
 * @hooked WC_Emails::order_details() Shows the order details table.
 * @hooked WC_Structured_Data::generate_order_data() Generates structured data.
 * @hooked WC_Structured_Data::output_structured_data() Outputs structured data.
 * @since 2.5.0
 */
//do_action('woocommerce_email_order_details', $order, $sent_to_admin, $plain_text, $email);//DETALLES DEL PEDIDO

/*
 * @hooked WC_Emails::order_meta() Shows order meta data.
 */
//do_action('woocommerce_email_order_meta', $order, $sent_to_admin, $plain_text, $email);

/*
 * @hooked WC_Emails::customer_details() Shows customer details
 * @hooked WC_Emails::email_address() Shows email address
 */
//do_action('woocommerce_email_customer_details', $order, $sent_to_admin, $plain_text, $email);

/**
 * Show user-defined additional content - this is set in each email's settings.
 */
if ($additional_content) {
    echo wp_kses_post(wpautop(wptexturize($additional_content)));
}

/*
 * @hooked WC_Emails::email_footer() Output the email footer
 */
do_action('woocommerce_email_footer', $email);
