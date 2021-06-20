<?php
/**
 * Custom Payment Gateways for WooCommerce - Configuraciones de Base de datos
 *
 * @version 1.6.1
 * @since   1.3.0
 * @author  Tyche Softwares
 * @package cpgw
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'Alg_WC_ShippingWoo_DB' ) ) :

	/**
	 * Input Fields Class.
	 */
	class Alg_WC_ShippingWoo_DB {

		/**
		 * Constructor.
		 *
		 * @version 1.6.1
		 * @since   1.3.0
		 * @todo    [dev] add option to pre-fill input fields on checkout with previous customer values (i.e. save it in customer meta)
		 */
		public function __construct() {
            //AGREGAMOS LA OPCION DE MENU
            register_activation_hook(__FILE__, array($this, 'instalar_tabla_shippingwoo'));
            register_activation_hook(__FILE__, array($this, 'insertar_tabla_shippingwoo'));
		}

        /**
         * Instalando la base de datos con informacion del email
         */
        public function instalar_tabla_shippingwoo()
        {
            ///echo "<h1>Pasa por el install</h1>";
            
            $version_tabla_sw = '1.0.0';

            global $wpdb;
            
            $emails_shippingwoo = $wpdb->prefix . 'emails_shippingwoo';

            //AQUI ESTAN LAS TABLAS DE LA DB
            //global $tabla_cron;

            $charset_collate = $wpdb->get_charset_collate();

            #AGREGAMOS EL REGISTRO DE INSERCIONES EN MI TABLA
            #NAME
            #Subject
            #MESSAGE
            $emails_shippingwoo = "CREATE TABLE $emails_shippingwoo (
                    id 		mediumint(9) NOT NULL AUTO_INCREMENT PRIMARY KEY,
                    nombre 		varchar(100),
                    sujeto     varchar(100),
                    mensaje     varchar(100),
                    estatus 	    varchar(100)
                ) $charset_collate;";
            require_once ABSPATH . 'wp-admin/includes/upgrade.php';
            dbDelta($emails_shippingwoo);

            add_option('version_tabla_sw', $version_tabla_sw);

            update_option("version_tabla_sw", $version_tabla_sw);
        }

        /**
         * INGRESAMOS INFORMACION DEMO
         */
        public function insertar_tabla_shippingwoo()
        {
            global $wpdb;
            $emails_shippingwoo = $wpdb->prefix . 'emails_shippingwoo';

            //CREAMOS LOS DATOS PARA CREAR Y BORRAR tabla_cron
            $activo = $wpdb->get_var("SELECT COUNT(*) FROM {$emails_shippingwoo} ");
            
            if ($activo <= 0) {
                $wpdb->insert(
                    $emails_shippingwoo,
                    array(
                        'nombre'      => 'default',
                        'sujeto'   => 'Tu pedido fue enviado',
                        'mensaje'   => 'Estamos enviando tu pedido',
                        'estatus'    => 1
                    )
                );
            }
            
        }

    }

endif;


//return new Alg_WC_ShippingWoo_DB();
$shippingwoo_db = new Alg_WC_ShippingWoo_DB();

//FORZAMOS MOMENTANIAMENTE LAS DOS FUNCIONES
//$shippingwoo_db->instalar_tabla_shippingwoo();
//$shippingwoo_db->insertar_tabla_shippingwoo();

return $shippingwoo_db;