<?php
/**
 * Custom Payment Gateways for WooCommerce - Core Class
 *
 * @version 1.6.0
 * @since   1.0.0
 * @author  Tyche Softwares
 * @package cpgw
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'Alg_WC_shippingwoo_Core' ) ) :

	/**
	 * Custom Payment Gateway Core Class.
	 */
	class Alg_WC_shippingwoo_Core {

		/**
		 * Constructor.
		 *
		 * @version 1.6.0
		 * @since   1.0.0
		 * @todo    [dev] add "language" shortcode
		 * @todo    [dev] (maybe) currency conversion (#6974)
		 */
		public function __construct() {
            /*
			if ( 'yes' === get_option( 'alg_wc_shippingwoo_enabled', 'yes' ) ) {
				// Include custom payment gateways class.
				require_once 'class-wc-gateway-alg-custom.php';
				// Input fields.
				if ( 'yes' === get_option( 'alg_wc_cpg_input_fields_enabled', 'yes' ) ) {
					require_once 'class-alg-wc-custom-payment-gateways-input-fields.php';
				}
				// Fees.
				if ( 'yes' === get_option( 'alg_wc_cpg_fees_enabled', 'yes' ) ) {
					require_once 'class-alg-wc-custom-payment-gateways-fees.php';
				}
			}
            */

            //AGREGAMOS LOS CAMPOS ARCHIVOS QUE SE ENCARGAN DE HACER COSAS
            require_once 'class-wc-shippingwoo-tablas.php';

            //DEFINIMOS LAS ACCIONES MASIVAS
            require_once 'class-wc-shippingwoo-acciones-masivas.php';

			//AGREGAMOS LA SECCION PARA AGREGAR NUEVOS METODOS DE ENVIO
			require_once 'class-wc-shippingwoo-news.php';

			//AGREGAMOS EL MENU
			require_once 'class-wc-shippingwoo-menu.php';
		}

	}

endif;


return new Alg_WC_shippingwoo_Core();
