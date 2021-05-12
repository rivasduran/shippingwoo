<?php
/**
 * Custom Payment Gateways for WooCommerce - Acciones Masivas
 *
 * @version 1.6.1
 * @since   1.3.0
 * @author  Tyche Softwares
 * @package cpgw
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'Alg_WC_ShippingWoo_AccionesMasivas' ) ) :

	/**
	 * Input Fields Class.
	 */
	class Alg_WC_ShippingWoo_AccionesMasivas {

		/**
		 * Constructor.
		 *
		 * @version 1.6.1
		 * @since   1.3.0
		 * @todo    [dev] add option to pre-fill input fields on checkout with previous customer values (i.e. save it in customer meta)
		 */
		public function __construct() {
            //
            add_filter('bulk_actions-edit-shop_order', array($this, 'definir_metodos_envios'));

            //$this->define_acciones();
            add_action( 'admin_action_sw_envio_cif', array($this, 'shippingwoo_aplicar_envio') );
            //add_action( 'admin_action_tienda', array($this, 'shippingwoo_aplicar_envio') );

            
            add_action('init', array($this, 'crear_Acciones_especiales'));
            


            //LA NOTICIA
            add_action('admin_notices', array($this, 'misha_custom_order_status_notices'));
		}


        public function crear_Acciones_especiales(){
            $taxonomy = "metodoenvios";
            $terms = get_terms([
                'taxonomy' => $taxonomy,
                'hide_empty' => false,
            ]);

            //if(count($terms) > 0){
                
            foreach ($terms as $key) {
                //echo "<h2>{$key->name}</h2>";
                $variable = "admin_action_{$key->slug}";
                //admin_action_sw_envio_cif
                //$variable = "admin_action_tienda";
                do_action( "admin_action_{$variable}" );

                add_action( $variable, array($this, 'shippingwoo_aplicar_envio') );

            }
        }


        public function define_acciones(){
            //$shipping_woo = new alg_wc_shippingwoo;
            //$shipping_woo = [];

            //ACCION APLICADA
            

            


            $taxonomy = "metodoenvios";
            $terms = get_terms([
                'taxonomy' => $taxonomy,
                'hide_empty' => false,
            ]);

            //if(count($terms) > 0){
                
                foreach ($terms as $key) {
                    //echo "<h2>{$key->name}</h2>";

                    add_action( "admin_action_".$key->slug, array($this, 'shippingwoo_aplicar_envio') );

                }
                
            //}
        }


        /**
         * DEFINIMOS LOS METODOS DE ENVIO
         */
        public function definir_metodos_envios($actions)
        {
            global $mis_envios_sw;
        
            //DEFINIMOS UN NUEVO ENVIO
            //$mis_envios_sw->metodos_envios_sw['nuevo_envio'] = 'Nuevo envio';
        
            //$actions['sw_envio_local'] = __('<strong>Tipo de envio:</strong>', 'woocommerce');
            //$actions['shipping_tipo_1'] = __(' - Tipo de 2', 'woocommerce');
            //$actions['shipping_tipo_2'] = __(' - Tipo de 2', 'woocommerce');
            /*
            $actions['tipo_envio'] = __('Tipos envios tesoro', 'woocommerce');
            
            $actions['tipo_envio'] = array(
                'sw_envio_local'                    => __('Envio Local', 'woocommerce-pip'),
                'sw_envio_cif'                      => __('CIF Express', 'woocommerce-pip'),
                'sw_envio_serv'                     => __('Servientrega', 'woocommerce-pip'),
                'wc_pip_send_email_packing_list'    => __('Email Packing List', 'woocommerce-pip'),
            );
            */
            

            //ESTOS DOS SON EL EJEMPLO
            //$mis_envios_sw->metodos_envios_sw['sw_envio_cif'] = __('CIF Express', 'woocommerce-pip');
            //$actions['tipo_envio']  = $mis_envios_sw->metodos_envios_sw;

            $shipping_woo = new alg_wc_shippingwoo;

            if(count($shipping_woo->envios) > 0){
                foreach ($shipping_woo->envios as $key) {
                    //echo "<h2>{$key->name}</h2>";

                    $mis_envios_sw->metodos_envios_sw[$key->slug] = __($key->name, 'woocommerce-pip');
                    $actions['tipo_envio']  = $mis_envios_sw->metodos_envios_sw;

                    

                }
            }
        
            return $actions;
        }

        /**
         * DEBEMOS DEFINIR NUESTRA ACCION
         * LUEGO DE APLICADA
         */
        public function shippingwoo_aplicar_envio()
        {

            global $current_user;
            wp_get_current_user();
            //return "";
 
            // if an array with order IDs is not presented, exit the function
            if( !isset( $_REQUEST['post'] ) && !is_array( $_REQUEST['post'] ) )
                return;
         
            foreach( $_REQUEST['post'] as $order_id ) {
         
                $order = new WC_Order( $order_id );
                /*
                $order_note = 'That\'s what happened by bulk edit:';
                $order->update_status( 'misha-shipment', $order_note, true ); // "misha-shipment" is the order status name (do not use wc-misha-shipment)
                */

                $tipo_envio =  $_REQUEST['action'];

                update_post_meta($order_id, 'tipo_envio', esc_attr(htmlspecialchars($tipo_envio)));
                $note = __("Agregamos el tipo de envío: " . $tipo_envio . " por " . $current_user->user_login);
                $order->add_order_note($note);

         
            }
         
            // of course using add_query_arg() is not required, you can build your URL inline
            $location = add_query_arg( array(
                    'post_type' => 'shop_order',
                'marked_awaiting_shipment' => 1, // markED_awaiting_shipment=1 is just the $_GET variable for notices
                'changed' => count( $_REQUEST['post'] ), // number of changed orders
                'ids' => join( $_REQUEST['post'], ',' ),
                'post_status' => 'all'
            ), 'edit.php' );
         
            wp_redirect( admin_url( $location ) );
            exit;
         
        }


        /**
         * AGREGAMOS LA NOTICIA
         */
        public function misha_custom_order_status_notices()
        {
 
            global $pagenow, $typenow;
         
            if( $typenow == 'shop_order' 
             && $pagenow == 'edit.php'
             && isset( $_REQUEST['marked_awaiting_shipment'] )
             && $_REQUEST['marked_awaiting_shipment'] == 1
             && isset( $_REQUEST['changed'] ) ) {
         
                $message = sprintf( _n( 'Order status changed.', '%s order statuses changed.', $_REQUEST['changed'] ), number_format_i18n( $_REQUEST['changed'] ) );
                echo "<div class=\"updated\"><p>{$message}</p></div>";
         
            }
         
        }
        
    }

endif;





return new Alg_WC_ShippingWoo_AccionesMasivas();
