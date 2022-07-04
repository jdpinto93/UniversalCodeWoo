<?php
/**
 * WooCommerce EAN Admin
 * @since       0.1.0
 */


// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

if( !class_exists( 'Woo_EAN_Admin' ) ) {

    /**
     * Woo_EAN_Admin class
     *
     * @since       0.2.0
     */
    class Woo_EAN_Admin {

        /**
         * @var         Woo_EAN_Admin $instance The one true Woo_EAN_Admin
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
         * @return      object self::$instance The one true Woo_EAN_Admin
         */
        public static function instance() {
            if( !self::$instance ) {
                self::$instance = new Woo_EAN_Admin();
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

            add_filter( 'woocommerce_get_settings_products', array( $this,  'ean_settings' ), 10, 2 );

            add_action( 'woocommerce_product_options_inventory_product_data', array( $this, 'product_tn_field' ) );
            add_action( 'woocommerce_process_product_meta', array( $this, 'save_product_tn' ) );

            add_action( 'woocommerce_product_after_variable_attributes', array( $this, 'variation_tn_field' ), 10, 3 );
            add_action( 'woocommerce_save_product_variation', array( $this, 'save_variations' ), 10, 2 );

        }

        /**
         * Add EAN Field
         *
         * @since       0.1.0
         * @return      void
         */
        public function product_tn_field() {

            global $post;

            $option_text = get_option( 'hwp_ean_text' );

            $label = ( !empty( $option_text ) ? $option_text : 'EAN' );

            //add EAN field for variations
            woocommerce_wp_text_input( 
                array(    
                 'id' => 'hwp_product_ean',
                 'label' => $label,
                 'desc_tip' => 'true',
                 'description' => 'INTRODUZCA EL VALOR DE EAN DEL PRODUCTO',
                 'value'       => get_post_meta( $post->ID, 'hwp_product_ean', true ),
                )
            );

        }

        /**
         * Add EAN Field for variations
         *
         * @since       0.1.0
         * @return      void
         */
        public function variation_tn_field( $loop, $variation_data, $variation ) {

            $option_text = get_option( 'hwp_ean_text' );

            $label = ( !empty( $option_text ) ? $option_text : 'EAN' );

            //add EAN field for variations
            woocommerce_wp_text_input( 
                array(    
                 'id' => 'hwp_var_ean[' . $variation->ID . ']',
                 'label' => $label,
                 'desc_tip' => 'true',
                 'description' => 'Unique EAN for variation? Enter it here.',
                 'value'       => get_post_meta( $variation->ID, 'hwp_var_ean', true ),
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

           $tn_post = $_POST['hwp_var_ean'][ $post_id ];

           // save
           if( isset( $tn_post ) ) {
              update_post_meta( $post_id, 'hwp_var_ean', esc_attr( $tn_post ) );
           }

           // remove if meta is empty
           $tn_meta = get_post_meta( $post_id,'hwp_var_ean', true );

           if ( empty( $tn_meta ) ) {
              delete_post_meta( $post_id, 'hwp_var_ean', '' );
           }

        }

        /**
         * Save simple product EAN settings
         *
         * @since       0.1.0
         * @return      void
         */
        public function save_product_tn( $post_id ) {

            $ean_post = $_POST['hwp_product_ean'];

            // save the ean
            if( isset( $ean_post ) ) {
                update_post_meta( $post_id, 'hwp_product_ean', esc_attr( $ean_post ) );
            }

            // remove if EAN meta is empty
            $ean_meta = get_post_meta( $post_id, 'hwp_product_ean', true );

            if( empty( $ean_meta ) ) {
                delete_post_meta( $post_id, 'hwp_product_ean', '' );
            }

        }

        /**
         * Add settings
         *
         * @access      public
         * @since       0.1
         */
        public function ean_settings( $settings, $current_section ) {

            /**
             * Check the current section is what we want
             **/
            if ( $current_section == 'inventory' ) {
                // Add Title to the Settings
                $settings[] = array( 'name' => __( 'Configurar EAN', 'woo-add-ean' ), 'type' => 'title', 'desc' => __( 'Las siguientes opciones se utilizan cambiar los valores de EAN', 'woo-add-ean' ), 'id' => 'woo-add-ean' );
                // Add first checkbox option
                $settings[] = array(
                    'name'     => __( '¿Ocultar EAN en páginas de un solo producto?', 'woo-add-ean' ),
                    //'desc_tip' => __( 'This will output the EAN on your product pages.', 'woo-add-ean' ),
                    'id'       => 'hwp_display_ean',
                    'type'     => 'checkbox',
                    'css'      => 'min-width:300px;',
                );
                
                $settings[] = array( 'type' => 'sectionend', 'id' => 'woo-add-ean' );

                $settings[] = array(
                    'name'     => __( 'Cambiar Nombre EAN', 'woo-add-ean' ),
                    'desc_tip' => __( 'Enter the label you\'d like to use instead of EAN.', 'woo-add-ean' ),
                    'id'       => 'hwp_ean_text',
                    'type'     => 'text',
                    'placeholder' => 'EAN',
                );
                
                $settings[] = array( 'type' => 'sectionend', 'id' => 'hwp_ean_text' );

                return $settings;
            
            /**
             * If not, return the standard settings
             **/
            } else {
                return $settings;
            }

        }

    }

    $Woo_EAN_Admin = new Woo_EAN_Admin();
    $Woo_EAN_Admin->instance();

} // end class_exists check