<?php
/**
 * Custom Payment Gateways for WooCommerce - Input Fields Class
 *
 * @version 1.6.1
 * @since   1.3.0
 * @author  Tyche Softwares
 * @package cpgw
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'Alg_WC_ShippingWoo_Tablas' ) ) :

	/**
	 * Input Fields Class.
	 */
	class Alg_WC_ShippingWoo_Tablas {

		/**
		 * Constructor.
		 *
		 * @version 1.6.1
		 * @since   1.3.0
		 * @todo    [dev] add option to pre-fill input fields on checkout with previous customer values (i.e. save it in customer meta)
		 */
		public function __construct() {
            /*
			add_action( 'woocommerce_after_checkout_validation', array( $this, 'check_required_input_fields' ), PHP_INT_MAX, 2 );
			add_action( 'woocommerce_checkout_update_order_meta', array( $this, 'add_input_fields_to_order_meta' ), PHP_INT_MAX, 2 );
			add_action( 'add_meta_boxes', array( $this, 'add_input_fields_meta_box' ) );
			add_action( 'woocommerce_order_details_after_order_table', array( $this, 'add_input_fields_to_order_details' ) );
			add_action( 'woocommerce_email_after_order_table', array( $this, 'add_input_fields_to_emails' ), 10, 4 );
			*/
            //if ( 'yes' === get_option( 'alg_wc_cpg_input_fields_woe_enabled', 'no' ) ) {
            if("yes" == "yes"){
				//add_filter( 'woe_get_order_value__alg_wc_cpg_input_fields', array( $this, 'sw_tablas_woocommerce' ), 10, 3 );
                
			}

            add_filter('manage_edit-shop_order_columns', array($this,'sw_define_tablas'));
            add_action('manage_shop_order_posts_custom_column', array($this, 'sw_tablas_woocommerce'));
		}


        /**
         * DEFINIMOS LAS TABLAS
         */
        public function sw_define_tablas($columns)
        {
            /*
            $columns['send_api_erp_saks'] = 'Tipo Envio';
            return $columns;
            */

            return array_slice($columns, 0, 3, true)
            + array('tipo_envio' => 'Tipo Envio')
            + array_slice($columns, 3, null, true);
        }

		/**
		 * AGREGAMOS LOS CAMPOS DE LAS TABLAS NUEVAS
		 *
		 * @param mixed    $value Value.
		 * @param WC_Order $order Order Object.
		 * @param string   $field Field.
		 * @return mixed
		 * @version 1.6.1
		 * @since   1.6.1
		 */
		//public function sw_tablas_woocommerce( $value, $order, $field ) {
        public function sw_tablas_woocommerce($column){
            global $post;

            if ('tipo_envio' === $column) {
                $order = wc_get_order($post->ID);

                ?>

                    <mark class="order-status metodo_envio tips">
                        <span><?php echo $order->get_meta('tipo_envio', true); ?></span>
                    </mark>
                    <p>Guia: <?php echo $order->get_meta('guia'); ?></p>
                <?php
            }
        }
        
    }

endif;


return new Alg_WC_ShippingWoo_Tablas();
