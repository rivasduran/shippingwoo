<?php
/**
 * Plugin Name: ShippingWoo
 * Plugin URI: https://shippingwoo.com/
 * Description: Add-on created to manage shipments in Woocommerce
 * Version: 1.0.0
 * Author: Siores
 * Author URI: https://siores.com
 * Text Domain: shippingwoo
 * Domain Path: /langs
 * Copyright: © 2021 Siores.
 * WC tested up to: 4.6
 * License: GNU General Public License v3.0
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 *
 * @package cpgw
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'Alg_WC_ShippingWoo' ) ) :

	/**
	 * Main Alg_WC_ShippingWoo Class
	 *
	 * @class   Alg_WC_ShippingWoo
	 * @version 1.6.2
	 * @since   1.0.0
	 */
	final class Alg_WC_ShippingWoo {

		/**
		 * Plugin version.
		 *
		 * @var   string
		 * @since 1.0.0
		 */
		public $version = '1.0.0';

		public $envios = [];

		/**
		 * The single instance of the class.
		 *
		 * @var   Alg_WC_ShippingWoo The single instance of the class
		 * @since 1.0.0
		 */
		protected static $_instance = null;

		/**
		 * Main Alg_WC_ShippingWoo Instance
		 *
		 * Ensures only one instance of Alg_WC_ShippingWoo is loaded or can be loaded.
		 *
		 * @version 1.0.0
		 * @since   1.0.0
		 * @static
		 * @return  Alg_WC_ShippingWoo - Main instance
		 */
		public static function instance() {
			if ( is_null( self::$_instance ) ) {
				self::$_instance = new self();
			}
			return self::$_instance;
		}

		/**
		 * Alg_WC_ShippingWoo Constructor.
		 *
		 * @version 1.6.0
		 * @since   1.0.0
		 * @access  public
		 */
		public function __construct() {

			// Check for active plugins.
            /* COMENTADO POR LOS MOMENTOS PORQUE NO ME FUNCIONA
			if (
			! $this->is_plugin_active( 'woocommerce/woocommerce.php' ) ||
			( 'shippingwoo.php' === basename( __FILE__ ) )
			) {
				return;
			}
            */

			// Include required files.
			$this->includes();

			// Admin.
            /*
			if ( is_admin() ) {
				$this->admin();
			}
            */

			$this->todos();
		}

		/**
		 * Is plugin active.
		 *
		 * @param   string $plugin Plugin Name.
		 * @return  bool
		 * @version 1.6.0
		 * @since   1.6.0
		 */
		public function is_plugin_active( $plugin ) {
			return ( function_exists( 'is_plugin_active' ) ? is_plugin_active( $plugin ) :
			(
				in_array( $plugin, apply_filters( 'active_plugins', (array) get_option( 'active_plugins', array() ) ), true ) ||
				( is_multisite() && array_key_exists( $plugin, (array) get_site_option( 'active_sitewide_plugins', array() ) ) )
			)
			);
		}

		/**
		 * Include required core files used in admin and on the frontend.
		 *
		 * @version 1.2.0
		 * @since   1.0.0
		 */
		public function includes() {
			// Functions.
			//require_once 'includes/alg-wc-shippingwoo-functions.php';//ESTE NO ME INTERESA TANTO
			// Core.
			$this->core = require_once 'includes/class-alg-wc-shippingwoo-core.php';
		}


		public function todos(){
			$todos = new Alg_WC_ShippingWoo_News;

			$this->envios = $todos->todos;
		}

		/**
		 * Admin.
		 *
		 * @version 1.6.2
		 * @since   1.2.0
		 */
        /*
		public function admin() {
			// Action links.
			add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), array( $this, 'action_links' ) );
			// Settings.
			add_filter( 'woocommerce_get_settings_pages', array( $this, 'add_woocommerce_settings_tab' ) );
			// Version update.
			if ( get_option( 'alg_wc_shippingwoo_version', '' ) !== $this->version ) {
				add_action( 'admin_init', array( $this, 'version_updated' ) );
			}
		}
        */

		/**
		 * Show action links on the plugin screen.
		 *
		 * @version 1.2.1
		 * @since   1.0.0
		 * @param   mixed $links Links.
		 * @return  array
		 */
        /*
		public function action_links( $links ) {
			$custom_links   = array();
			$custom_links[] = '<a href="' . admin_url( 'admin.php?page=wc-settings&tab=alg_wc_shippingwoo' ) . '">' . __( 'Settings', 'woocommerce' ) . '</a>';
			if ( 'shippingwoo.php' === basename( __FILE__ ) ) {
				$custom_links[] = '<a target="_blank" href="https://wpfactory.com/item/shippingwoo-woocommerce/">' .
				__( 'Unlock All', 'shippingwoo-woocommerce' ) . '</a>';
			}
			return array_merge( $custom_links, $links );
		}
        */

		/**
		 * Add Custom Payment Gateways settings tab to WooCommerce settings.
		 *
		 * @param   array $settings WC Settings Array.
		 * @return  array
		 * @version 1.2.0
		 * @since   1.0.0
		 */
		public function add_woocommerce_settings_tab( $settings ) {
            /*
			$settings[] = require_once 'includes/settings/class-alg-wc-settings-shippingwoo.php';
			return $settings;
            */
		}

		/**
		 * Version updated.
		 *
		 * @version 1.2.0
		 * @since   1.2.0
		 */
		public function version_updated() {
			update_option( 'alg_wc_shippingwoo_version', $this->version );
		}

		/**
		 * Get the plugin url.
		 *
		 * @version 1.0.0
		 * @since   1.0.0
		 * @return  string
		 */
		public function plugin_url() {
			return untrailingslashit( plugin_dir_url( __FILE__ ) );
		}

		/**
		 * Get the plugin path.
		 *
		 * @version 1.0.0
		 * @since   1.0.0
		 * @return  string
		 */
		public function plugin_path() {
			return untrailingslashit( plugin_dir_path( __FILE__ ) );
		}

	}

endif;

if ( ! function_exists( 'alg_wc_shippingwoo' ) ) {
	/**
	 * Returns the main instance of alg_wc_shippingwoo to prevent the need to use globals.
	 *
	 * @version 1.0.0
	 * @since   1.0.0
	 * @return  alg_wc_shippingwoo
	 */
	function alg_wc_shippingwoo() {
		return Alg_WC_ShippingWoo::instance();
	}
}

alg_wc_shippingwoo();


/**
 * REGISTRAMOS ENVIOS PARA WOOCOMMERCE
 */
add_action('init', 'crear_Acciones_especiales');
function crear_Acciones_especiales(){
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

		//add_action( $variable, 'shippingwoo_aplicar_envio' );

		$mandalo_Acciones = new Alg_WC_ShippingWoo_AccionesMasivas();

		add_action( $variable, array($mandalo_Acciones, 'shippingwoo_aplicar_envio') );

	}

}

/**
 * Agregamos una tabla nueva a los tax
 */
//AGREGAMOS LOS METAS PERSONALIZADOS
$shippingwoo_news = new Alg_WC_ShippingWoo_News();
add_action( 'metodoenvios_add_form_fields', array($shippingwoo_news, 'metodoenvios_add_term_fields') );

//AGREGAMOS LA OPCION DE EDITAR
add_action( 'metodoenvios_edit_form_fields', array($shippingwoo_news, 'metodoenvios_edit_term_fields'), 10, 2 );


//GUARDAMOS EL TAG
add_action( 'created_metodoenvios', array($shippingwoo_news, 'metodoenvios_save_term_fields') );
add_action( 'edited_metodoenvios', array($shippingwoo_news, 'metodoenvios_save_term_fields') );
 
