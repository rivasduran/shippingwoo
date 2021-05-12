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

if ( ! class_exists( 'Alg_WC_ShippingWoo_News' ) ) :

	/**
	 * Input Fields Class.
	 */
	class Alg_WC_ShippingWoo_News {

		/**
		 * Constructor.
		 *
		 * @version 1.6.1
		 * @since   1.3.0
		 * @todo    [dev] add option to pre-fill input fields on checkout with previous customer values (i.e. save it in customer meta)
		 */
		public function __construct() {
            //AGREGAMOS AL MENU PRINCIPAL DEL COMPLEMENTO
            add_action( 'admin_menu', array($this, 'shmeh_menu') );
            add_action( 'parent_file', array($this, 'menu_highlight') );


            //ESTA ACCION ES CUANDO SE INSTALA EL COMPLEMENTO, PARA PODER CREAR LA NUEVA TAXONOMIA DE ENVIOS
            add_action( 'init', array($this, 'wporg_register_taxonomy_Envio') );
            

            $todos = $this->todos();
		}


        /**
         * MIS VARIABLES
         */

        public $todos = [];

        /**
         * AGREGAMOS AL MENU
         */
        public function shmeh_menu() {
            add_submenu_page( 'sw', 'Agregar Nuevos', 'Agregar Nuevos', 'manage_options', 'edit-tags.php?taxonomy=metodoenvios');
        }

        /**
         * DEFINIMOS
         */
        public function menu_highlight( $parent_file ) {
            global $current_screen;
    
            $taxonomy = $current_screen->taxonomy;
            if ( $taxonomy == 'metodoenvios' ) {
                $parent_file = 'sw';
            }
    
            return $parent_file;
        }

        /**
         * FUNCION PARA CREAR LA TAXONOMIA DE ENVIOS
         */
        public function wporg_register_taxonomy_Envio() {
            $labels = array(
                'name'              => _x( 'Envios', 'taxonomy general name' ),
                'singular_name'     => _x( 'Envio', 'taxonomy singular name' ),
                'search_items'      => __( 'Search Envios' ),
                'all_items'         => __( 'All Envios' ),
                'parent_item'       => __( 'Parent Envio' ),
                'parent_item_colon' => __( 'Parent Envio:' ),
                'edit_item'         => __( 'Edit Envio' ),
                'update_item'       => __( 'Update Envio' ),
                'add_new_item'      => __( 'Add New Envio' ),
                'new_item_name'     => __( 'New Envio Name' ),
                'menu_name'         => __( 'metodoenvios' ),
            );
            $args   = array(
                'hierarchical'      => false, // make it hierarchical (like categories)//JERARQUIAS
                'labels'            => $labels,
                'show_ui'           => true,
                'show_admin_column' => true,
                'query_var'         => true,
                'rewrite'           => [ 'slug' => 'Envio' ],
                'show_in_menu' => true
            );
            //register_taxonomy( 'Envio', [ 'post' ], $args );
            register_taxonomy( 'metodoenvios', [ 'woocommerce' ], $args );
       }


       /**
        * TODOS LOS METODOS DE ENVIO
        */
        public function todos(){
            $taxonomy = "metodoenvios";
            $terms = get_terms([
                'taxonomy' => $taxonomy,
                'hide_empty' => false,
            ]);
            
            $this->todos = $terms;

            return $terms;
        }
    }

endif;


return new Alg_WC_ShippingWoo_News();
