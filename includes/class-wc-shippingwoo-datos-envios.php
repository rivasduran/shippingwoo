<?php
/**
 * Custom Payment Gateways for WooCommerce - DATOS DE ENVIO
 *
 * @version 1.6.1
 * @since   1.3.0
 * @author  Tyche Softwares
 * @package cpgw
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'Alg_WC_ShippingWoo_DatosEnvios' ) ) :

	/**
	 * Input Fields Class.
	 */
	class Alg_WC_ShippingWoo_DatosEnvios {

		/**
		 * Constructor.
		 *
		 * @version 1.6.1
		 * @since   1.3.0
		 * @todo    [dev] add option to pre-fill input fields on checkout with previous customer values (i.e. save it in customer meta)
		 */
		public function __construct() {
            //AGREGAMOS AL MENU PRINCIPAL DEL COMPLEMENTO
            //add_action( 'admin_menu', array($this, 'shmeh_menu') );

            add_action('add_meta_boxes', array($this, 'trusted_list_order_meta_boxes'));

            add_action('save_post', array($this, 'trusted_list_save_meta_box_data'));

            //AHORA MOSTREMOS ESTA INFORMACION EN LA PAGINA DE GRACIAS Y DEL PEDIDO
            //add_action( 'woocommerce_thankyou', 'misha_view_order_and_thankyou_page', 20 );
            add_action( 'woocommerce_view_order', array($this, 'misha_view_order_and_thankyou_page'), 20 );
		}

        public function trusted_list_order_meta_boxes()
        {
        
            add_meta_box(
                'woocommerce-order-verifyemail_transaction_id',
                __('Tracking'),
                array($this, 'trusted_list_order_meta_box_content'),
                'shop_order',
                'side',
                'default'
            );
        }

        public function trusted_list_order_meta_box_content($post)
        {
            $customeremail = get_post_meta($post->ID, '_billing_email', true);
            $button_text   = __('Enviar', 'woocommerce');
        
            $order = wc_get_order($post->ID);
        
            $tracking_number = $order->get_transaction_id();
        
            if ($tracking_number == "") {
                $tracking_number = $order->get_meta('tracking_number', true);
            }

            //TIPO DE ENVIO
            $tipo_envio = $order->get_meta('tipo_envio', true);

            //TRAEMOS TODOS LOS METODOS DE ENVIO
            $metodos_envios = new Alg_WC_ShippingWoo_News;
            $todos_envios = $metodos_envios->todos();

        
            $variables_envios = '<form method="post" action="CURRENT_FILE_URL">
                <input type="text" name="tracking_number" value="' . $tracking_number . '" style="margin-bottom: 10px;">
        
                <h3>Metodo de envio</h3>
        
                <select name="tipo_envio">
            ';

            $variables_envios .= '<option value="">Seleccionar</option>';

            foreach($todos_envios as $envio){
                $default = "";

                if($tipo_envio != "" && $tipo_envio == $envio->slug){
                    $default = 'selected';
                }

                $variables_envios .= '<option value="'.$envio->slug.'" '.$default.'>'.$envio->name.'</option>';
            }
                    
            
            $variables_envios .= '
                </select>
                <br />
                <br />
        
                <input type="submit" name="submit_trusted_list" value="' . $button_text . '" class="button button-primary"/>
                <input type="hidden" name="trusted_list_nonce" value="' . wp_create_nonce() . '">
            </form>';

            echo $variables_envios;
        }

        /**
         * PARA GUARDAR ESTA INFORMACION
         */
        public function trusted_list_save_meta_box_data($post_id)
        {
            $shippingwoo_emails = new Alg_WC_ShippingWoo_Emails;
        
            // Only for shop order
            if ('shop_order' != $_POST['post_type']) {
                return $post_id;
            }
        
            // Check if our nonce is set (and our cutom field)
            if (!isset($_POST['trusted_list_nonce']) && isset($_POST['submit_trusted_list'])) {
                return $post_id;
            }
        
            $nonce = $_POST['trusted_list_nonce'];
        
            // Verify that the nonce is valid.
            if (!wp_verify_nonce($nonce)) {
                return $post_id;
            }
        
            // Checking that is not an autosave
            if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
                return $post_id;
            }
        
            // Check the user’s permissions (for 'shop_manager' and 'administrator' user roles)
            if (!current_user_can('edit_shop_order', $post_id) && !current_user_can('edit_shop_orders', $post_id)) {
                return $post_id;
            }
        
            // Action to make or (saving data)
            if (isset($_POST['submit_trusted_list'])) {
                $order = wc_get_order($post_id);
        
                $tracking_number = $_POST['tracking_number'];
                $metodoenvios = $_POST['tipo_envio'];
        
                update_post_meta($order->id, 'tracking_number', esc_attr(htmlspecialchars($tracking_number)));
        
                // $customeremail = $order->get_billing_email();
                $order->add_order_note(sprintf("Se agrego la transacción ID: " . $tracking_number));

                //CAMBIAMOS EL METODO DE ENVIO
                
                update_post_meta($order->id, 'tipo_envio', esc_attr(htmlspecialchars($metodoenvios)));

                //ENVIAMOS EL EMAIL
                $email_enviado = $shippingwoo_emails->enviar_pedidos_orden($order->id);

            }
        }


        /**
         * MOSTRANDO LA INFORMACION EN LA SECCION DE ORDENES
         */
        public function misha_view_order_and_thankyou_page( $order_id ){  ?>
            <h2>Empresa de envio:</h2>
            <table class="woocommerce-table shop_table gift_info">
                <tbody>
                    <tr>
                        <th>Ya se envio mi pedido?</th>
                        <td><?php echo ( $tracking_number = get_post_meta( $order_id, 'tracking_number', true ) ) ? 'Yes' : 'No'; ?></td>
                    </tr>
                    <?php if( $tracking_number ) : ?>
                    <tr>
                        <th>Tipo de envio:</th>
                        <td><?php echo get_post_meta( $order_id, 'tipo_envio', true ); ?></td>
                    </tr>
                    <tr>
                        <th>Tracking: </th>
                        <td><?php echo get_post_meta( $order_id, 'tracking_number', true ); ?></td>
                    </tr>
                    <tr>
                        <th>Url de consulta:</th>

                        <?php

                            $slug = get_post_meta( $order_id, 'tipo_envio', true );

                            //echo "<h1> ==> {$slug}</h1>";

                            $tag = get_term_by('slug', $slug, 'metodoenvios');

                            //print_r($tag);

                            //RECUPERAMOS LA URL
                            $url_tracking = get_term_meta( $tag->term_id, 'shippingwoo-url-tracking', true );

                        ?>
                        <td><a href="<?php echo $url_tracking; ?>" target="_blank" ><?php echo $url_tracking; ?></a></td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        <?php }
    }

endif;


return new Alg_WC_ShippingWoo_DatosEnvios();
