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

if ( ! class_exists( 'Alg_WC_ShippingWoo_Emails' ) ) :

	/**
	 * Input Fields Class.
	 */
	class Alg_WC_ShippingWoo_Emails {

		/**
		 * Constructor.
		 *
		 * @version 1.6.1
		 * @since   1.3.0
		 * @todo    [dev] add option to pre-fill input fields on checkout with previous customer values (i.e. save it in customer meta)
		 */
		public function __construct() {
            //AGREGAMOS LA OPCION DE MENU
            
		}

        /**
         * 
         */
        public function enviar_pedidos_orden($order_id)
        {
        
            //global $woocommerce;
            //$order_id = 20;
        
            $order = wc_get_order($order_id);
        
            //$order    = new WC_Order($order_id);
        
            //print_r($order);
        
        
            $default_path = untrailingslashit( plugin_dir_path(__FILE__) ) . '/emails/';
        
            //echo "<h1>{$default_path}</h1>";
        
            // load the mailer class
        
            $mailer = WC()->mailer();
        
        //format the email
            //$recipient = "joser@keylimetec.com";
            $recipient = $order->get_billing_email();
            $subject   = __("Hola, Información de envío de paquete #".$order_id, 'theme_name');
            $content   = $this->get_custom_email_html($order, $subject, $mailer);
            $headers   = "Content-Type: text/html\r\n";
        
        //send the email through wordpress
            $mailer->send($recipient, $subject, $content, $headers);
        
        }


        
        /**
         * ARMANDO EL CONTENIDO DEL EMAIL
         */
        public function get_custom_email_html($order, $heading = false, $mailer)
        {

            // The template name. 
            $template_name = 'myaccount/student-details.php'; 

            // default args
            $args = array(); 

            // default template
            $template_path = ''; // use default which is usually "woocommerce"

            // default path (look in plugin file!)
            $default_path = untrailingslashit( plugin_dir_path(__FILE__) ) . '/emails/';


            $template = 'tracking-paquetes.php';
            //$template = 'includes/emails/tracking-paquetes.php';
            //$template = '/wp-content/plugins/shippingwoo/includes/emails/my-custom-email-i-want-to-send.php';
            //$template = 'emails/admin-new-order.php';

            return wc_get_template_html($template, array(
                'order'         => $order,
                'email_heading' => $heading,
                'sent_to_admin' => false,
                'plain_text'    => false,
                'email'         => $mailer,
            ),
            $template_path, $default_path

        );

        }

        /**
         * Editar los parametros del email
         */

        public function ajustes_emails(){
            global $wpdb;

            //TENEMOS EN CUENTA LA MODIFICACION DE EMAILS
            $this->modificando_emails();
            
            //DEBEMOS RECORRER LOS EMAILS YA CONFIGURADOS
            $emails = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}emails_shippingwoo");

            //print_r($emails);
            //RECORREMOS TODOS NUESTROS EMAILS
            foreach($emails as $email){
                $nombre     = $email->nombre;
                $estatus    = $email->estatus;
                $subject    = $email->sujeto;//shippingwoo_subject
                $message    = $email->mensaje;//shippingwoo_message

                //echo "<h1>El nombre del email: {$nombre}</h1>";

                $activo     = ($estatus == "1") ? 'selected' : '';
                $inactivo   = ($estatus == "2") ? 'selected' : '';


                //echo "<h1>Activo ({$activo}) Inactivo: ({$inactivo})</h1>";
                ?>

                <!-- SECCION PARA AGREGAR LOS DATOS DE ENVIO -->
                <h1><?php echo __( 'Email details:', 'woocommerce' ); ?></h1>

                <div class="formularios-approval quarterWidth formularioCertificados">
                    <form method="post" action="">
                        <input type="text" name="cambiar_texto_email" value="<?php echo $email->id; ?>" required style="display: none;" />

                        <!-- SECCION DE LAS CATEGORIAS -->
                        <div class="">
                            <!-- Activo inactivo -->
                            <div class="form-group">
                                <label for="Inputestatus"><?php echo __( 'estatus:', 'woocommerce' ); ?></label>
                                <select class="form-control" name="shippingwoo_estatus">
                                
                                    <option value="1" <?php echo $activo; ?>>Activo</option>
                                    <option value="2" <?php echo $inactivo; ?>>Inactivo</option>
                                </select>
                                <small id="emailHelp" class="form-text text-muted">In this field you can add the title of the email so that the client knows what message is reaching them (<?php echo $email->id; ?>)</small>
                            </div>

                            <!-- NOMBRE DEL EMAIL -->
                            <div class="form-group">
                                <label for="InputNombre"><?php echo __( 'nombre:', 'woocommerce' ); ?></label>
                                <input type="text" class="form-control" name="shippingwoo_nombre" value="<?php echo $nombre; ?>" placeholder="Nombre del email">
                                <small id="emailHelp" class="form-text text-muted">In this field you can add the title of the email so that the client knows what message is reaching them (<?php echo $email->id; ?>)</small>
                            </div>

                            <!-- SUJETO DEL EMAIL -->
                            <div class="form-group">
                                <label for="exampleInputEmail1"><?php echo __( 'Subject:', 'woocommerce' ); ?></label>
                                <input type="text" class="form-control" name="shippingwoo_subject" value="<?php echo $subject; ?>" placeholder="Email subject">
                                <small id="emailHelp" class="form-text text-muted">In this field you can add the title of the email so that the client knows what message is reaching them (<?php echo $email->id; ?>)</small>
                            </div>


                            <!-- -->
                            <div class="form-group">
                                <label for="exampleInputEmail1"><?php echo __( 'Message:', 'woocommerce' ); ?></label>
                                <textarea class="form-control" name="shippingwoo_message"><?php echo $message; ?></textarea>
                                <!--
                                <input type="text" class="form-control" name="shippingwoo_message" value="<?php echo $message; ?>" placeholder="Key Privada">
                                -->
                                <small id="emailHelp" class="form-text text-muted">Estas se consiguen en los ajustes de Woocommerce.</small>
                            </div>
                            
                            <!-- BOTON DE ENVIAR -->
                            <?php submit_button("Guardar"); ?>
                        </div>
                    </form>
                </div>

                <?php
            }

            
            /**
             * AGREGAR NUEVOS
             */
            //$this->agregar_nuevos_emails();//LO VEREMOS EN EL FUTURO
        }

        /**
         * MODIFICANDO EMAILS
         */
        public function modificando_emails(){
            if(isset($_POST['cambiar_texto_email'])){
                global $wpdb;

                //echo "<h1>Estamos cambiando el email!</h1>";

                //RECAUDAMOS LA INFORMACION
                $shippingwoo_id             = $_POST['cambiar_texto_email'];
                $shippingwoo_subject        = $_POST['shippingwoo_subject'];
                $shippingwoo_message        = $_POST['shippingwoo_message'];
                $shippingwoo_nombre         = $_POST['shippingwoo_nombre'];
                $shippingwoo_estatus        = $_POST['shippingwoo_estatus'];

                //ACTUALIZAMOS LA INFORMACION
                $wpdb->update(
                    "{$wpdb->prefix}emails_shippingwoo",
                    array(
                        'sujeto'			=> $shippingwoo_subject,
                        'mensaje'			=> $shippingwoo_message,
                        'nombre'			=> $shippingwoo_nombre,
                        'estatus'			=> $shippingwoo_estatus,
                        //'tipo'			=> '1'
                    ),
                    array( 'id'		        => $shippingwoo_id )
                );

                $actualizacion = $wpdb->result;

                if($actualizacion){

                    
                    //echo "<h1>Reescribimos los emails!</h1>";
                    //echo $shippingwoo_subject;
                    //echo $shippingwoo_message;
                }

                //print_r($actualizacion);//DEBERIAMOS MOSTRAR UN MENSAJE DE ACTUALIZACION
            }
        }

        /**
         * MODULO PARA AGREGAR NUEVOS
         */
        public function agregar_nuevos_emails(){
            ?>
                <div class="seccion_boton_nuevo_email">
                    <button>Agregar nuevo</button>
                </div>
            <?php
        }

    }

endif;


//return new Alg_WC_ShippingWoo_Emails();
$shippingwoo_emails = new Alg_WC_ShippingWoo_Emails();

return $shippingwoo_emails;