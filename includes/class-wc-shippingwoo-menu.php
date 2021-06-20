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

if ( ! class_exists( 'Alg_WC_ShippingWoo_Menu' ) ) :

	/**
	 * Input Fields Class.
	 */
	class Alg_WC_ShippingWoo_Menu {

		/**
		 * Constructor.
		 *
		 * @version 1.6.1
		 * @since   1.3.0
		 * @todo    [dev] add option to pre-fill input fields on checkout with previous customer values (i.e. save it in customer meta)
		 */
		public function __construct() {
            //AGREGAMOS LA OPCION DE MENU
            add_action('admin_menu', array($this, 'menu_shippingwoo'));
		}

        public function menu_shippingwoo(){

			$shippingwoo_emails = new Alg_WC_ShippingWoo_Emails;

            add_menu_page('SW', 'Shipping Woo', 'manage_options', 'sw', array($this, 'menu_principal_shipping'), 'dashicons-editor-paste-word', '35');

			add_submenu_page( 'sw', 'Editar email', 'Editar email', 'manage_options', 'ajustes-email', array($shippingwoo_emails, 'ajustes_emails'));
			//add_submenu_page('sw', 'Ajustes', 'Ajustes', 'manage_options', 'mis-ajustes-sw', 'ajustes_sw');
        }
        
        public function menu_principal_shipping(){
            echo "Menu!";
        }
    }

endif;


//return new Alg_WC_ShippingWoo_Menu();
$shippingwoo_menu = new Alg_WC_ShippingWoo_Menu();

return $shippingwoo_menu;