<?php
/**
 * WooCommerce UPC Admin
 * @since       0.1.0
 */


// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

if( !class_exists( 'Woo_UPC_Admin' ) ) {

    /**
     * Woo_UPC_Admin class
     *
     * @since       0.2.0
     */
    class Woo_UPC_Admin {

        /**
         * @var         Woo_UPC_Admin $instance The one true Woo_UPC_Admin
         * @since       0.2.0
         */
        private static $instance;
        public static $errorpath = '../php-error-log.php';
        public static $active = array();
        // sample: error_log("meta: " . $meta . "\r\n",3,self::$errorpath);

        /**
         * Get active instance
         *
         * @access      public
         * @since       0.2.0
         * @return      object self::$instance The one true Woo_UPC_Admin
         */
        public static function instance() {
            if( !self::$instance ) {
                self::$instance = new Woo_UPC_Admin();
                self::$instance->hooks();
            }

            return self::$instance;
        }


        /**
         * Include necessary files
         *
         * @access      private
         * @since       0.2.0
         * @return      void
         */
        private function hooks() {

            add_filter( 'woocommerce_get_settings_products', array( $this,  'upc_settings' ), 10, 2 );

            add_action( 'woocommerce_product_options_inventory_product_data', array( $this, 'product_tn_field' ) );
            add_action( 'woocommerce_process_product_meta', array( $this, 'save_product_tn' ) );

            add_action( 'woocommerce_product_after_variable_attributes', array( $this, 'variation_tn_field' ), 10, 3 );
            add_action( 'woocommerce_save_product_variation', array( $this, 'save_variations' ), 10, 2 );

        }

        /**
         * Add UPC Field
         *
         * @since       0.1.0
         * @return      void
         */
        public function product_tn_field() {

            global $post;

            $option_text = get_option( 'hwp_upc_text' );

            $label = ( !empty( $option_text ) ? $option_text : 'UPC' );

            //add UPC field for variations
            woocommerce_wp_text_input( 
                array(    
                 'id' => 'hwp_product_upc',
                 'label' => $label,
                 'desc_tip' => 'true',
                 'description' => 'INTRODUZCA EL VALOR DE GTIN DEL PRODUCTO',
                 'value'       => get_post_meta( $post->ID, 'hwp_product_upc', true ),
                )
            );

        }

        /**
         * Add UPC field for variatios
         *
         * @since       0.1.0
         * @return      void
         */
        public function variation_tn_field( $loop, $variation_data, $variation ) {

            $option_text = get_option( 'hwp_upc_text' );

            $label = ( !empty( $option_text ) ? $option_text : 'UPC' );

            //add UPC field for variations
            woocommerce_wp_text_input( 
                array(    
                 'id' => 'hwp_var_upc[' . $variation->ID . ']',
                 'label' => $label,
                 'desc_tip' => 'true',
                 'description' => 'UPC único para la variación? Insértelo aquí.',
                 'value'       => get_post_meta( $variation->ID, 'hwp_var_upc', true ),
                )
            );

        }

        /**
         * Save variation settings
         *
         * @since       0.1.0
         * @return      void
         */
        public function save_variations( $post_id ) {

           $tn_post = $_POST['hwp_var_upc'][ $post_id ];

           // save
           if( isset( $tn_post ) ) {
              update_post_meta( $post_id, 'hwp_var_upc', esc_attr( $tn_post ) );
           }

           // remove if meta is empty
           $tn_meta = get_post_meta( $post_id,'hwp_var_upc', true );

           if ( empty( $tn_meta ) ) {
              delete_post_meta( $post_id, 'hwp_var_upc', '' );
           }

        }

        /**
         * Save simple product UPC settings
         *
         * @since       0.1.0
         * @return      void
         */
        public function save_product_tn( $post_id ) {

            $upc_post = $_POST['hwp_product_upc'];

            // save the upc
            if( isset( $upc_post ) ) {
                update_post_meta( $post_id, 'hwp_product_upc', esc_attr( $upc_post ) );
            }

            // remove if UPC meta is empty
            $upc_meta = get_post_meta( $post_id, 'hwp_product_upc', true );

            if( empty( $upc_meta ) ) {
                delete_post_meta( $post_id, 'hwp_product_upc', '' );
            }

        }

        /**
         * Add settings
         *
         * @access      public
         * @since       0.1
         */
        public function upc_settings( $settings, $current_section ) {

            /**
             * Check the current section is what we want
             **/
            if ( $current_section == 'inventory' ) {
                // Add Title to the Settings
                $settings[] = array( 'name' => __( 'Configurar UPC', 'woo-add-upc' ), 'type' => 'title', 'desc' => __( 'Las siguientes opciones se utilizan cambiar los valores de UPC', 'woo-add-upc' ), 'id' => 'woo-add-upc' );
                // Add first checkbox option
                $settings[] = array(
                    'name'     => __( '¿Ocultar UPC en páginas de un solo producto?', 'woo-add-upc' ),
                    //'desc_tip' => __( 'This will output the UPC on your product pages.', 'woo-add-upc' ),
                    'id'       => 'hwp_display_upc',
                    'type'     => 'checkbox',
                    'css'      => 'min-width:300px;',
                );
                
                $settings[] = array( 'type' => 'sectionend', 'id' => 'woo-add-upc' );

                $settings[] = array(
                    'name'     => __( 'Cambiar Nombre UPC', 'woo-add-upc' ),
                    'desc_tip' => __( 'Enter the label you\'d like to use instead of UPC.', 'woo-add-upc' ),
                    'id'       => 'hwp_upc_text',
                    'type'     => 'text',
                    'placeholder' => 'UPC',
                );
                
                $settings[] = array( 'type' => 'sectionend', 'id' => 'hwp_upc_text' );

                return $settings;
            
            /**
             * If not, return the standard settings
             **/
            } else {
                return $settings;
            }

        }

    }

    $Woo_UPC_Admin = new Woo_UPC_Admin();
    $Woo_UPC_Admin->instance();

} // end class_exists check