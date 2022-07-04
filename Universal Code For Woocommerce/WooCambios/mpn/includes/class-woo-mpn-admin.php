<?php
/**
 * WooCommerce MPN Admin
 * @since       0.1.0
 */


// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

if( !class_exists( 'Woo_MPN_Admin' ) ) {

    /**
     * Woo_MPN_Admin class
     *
     * @since       0.2.0
     */
    class Woo_MPN_Admin {

        /**
         * @var         Woo_MPN_Admin $instance The one true Woo_MPB_Admin
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
         * @return      object self::$instance The one true Woo_MPN_Admin
         */
        public static function instance() {
            if( !self::$instance ) {
                self::$instance = new Woo_MPN_Admin();
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

            add_filter( 'woocommerce_get_settings_products', array( $this,  'mpn_settings' ), 10, 2 );

            add_action( 'woocommerce_product_options_inventory_product_data', array( $this, 'product_tn_field' ) );
            add_action( 'woocommerce_process_product_meta', array( $this, 'save_product_tn' ) );

            add_action( 'woocommerce_product_after_variable_attributes', array( $this, 'variation_tn_field' ), 10, 3 );
            add_action( 'woocommerce_save_product_variation', array( $this, 'save_variations' ), 10, 2 );

        }

        /**
         * Add MPN Field
         *
         * @since       0.1.0
         * @return      void
         */
        public function product_tn_field() {

            global $post;

            $option_text = get_option( 'hwp_mpn_text' );

            $label = ( !empty( $option_text ) ? $option_text : 'MPN' );

            //add MPN field for variations
            woocommerce_wp_text_input( 
                array(    
                 'id' => 'hwp_product_mpn',
                 'label' => $label,
                 'desc_tip' => 'true',
                 'description' => 'INTRODUZCA EL VALOR DE MPN DEL PRODUCTO',
                 'value'       => get_post_meta( $post->ID, 'hwp_product_mpn', true ),
                )
            );

        }

        /**
         * Add MPN Field for variations
         *
         * @since       0.1.0
         * @return      void
         */
        public function variation_tn_field( $loop, $variation_data, $variation ) {

            $option_text = get_option( 'hwp_mpn_text' );

            $label = ( !empty( $option_text ) ? $option_text : 'MPN' );

            //add mpn field for variations
            woocommerce_wp_text_input( 
                array(    
                 'id' => 'hwp_var_mpn[' . $variation->ID . ']',
                 'label' => $label,
                 'desc_tip' => 'true',
                 'description' => 'Unique MPN for variation? Enter it here.',
                 'value'       => get_post_meta( $variation->ID, 'hwp_var_mpn', true ),
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

           $tn_post = $_POST['hwp_var_mpn'][ $post_id ];

           // save
           if( isset( $tn_post ) ) {
              update_post_meta( $post_id, 'hwp_var_mpn', esc_attr( $tn_post ) );
           }

           // remove if meta is empty
           $tn_meta = get_post_meta( $post_id,'hwp_var_mpn', true );

           if ( empty( $tn_meta ) ) {
              delete_post_meta( $post_id, 'hwp_var_mpn', '' );
           }

        }

        /**
         * Save simple product MPN settings
         *
         * @since       0.1.0
         * @return      void
         */
        public function save_product_tn( $post_id ) {

            $mpn_post = $_POST['hwp_product_mpn'];

            // save the mpn
            if( isset( $mpn_post ) ) {
                update_post_meta( $post_id, 'hwp_product_mpn', esc_attr( $mpn_post ) );
            }

            // remove if MPN meta is empty
            $mpn_meta = get_post_meta( $post_id, 'hwp_product_mpn', true );

            if( empty( $mpn_meta ) ) {
                delete_post_meta( $post_id, 'hwp_product_mpn', '' );
            }

        }

        /**
         * Add settings
         *
         * @access      public
         * @since       0.1
         */
        public function mpn_settings( $settings, $current_section ) {

            /**
             * Check the current section is what we want
             **/
            if ( $current_section == 'inventory' ) {
                // Add Title to the Settings
                $settings[] = array( 'name' => __( 'Configurar MPN', 'woo-add-mpn' ), 'type' => 'title', 'desc' => __( 'Las siguientes opciones se utilizan cambiar los valores de MPN', 'woo-add-mpn' ), 'id' => 'woo-add-mpn' );
                // Add first checkbox option
                $settings[] = array(
                    'name'     => __( '¿Ocultar MPN en páginas de un solo producto?', 'woo-add-mpn' ),
                    //'desc_tip' => __( 'This will output the MPN on your product pages.', 'woo-add-mpn' ),
                    'id'       => 'hwp_display_mpn',
                    'type'     => 'checkbox',
                    'css'      => 'min-width:300px;',
                );
                
                $settings[] = array( 'type' => 'sectionend', 'id' => 'woo-add-mpn' );

                $settings[] = array(
                    'name'     => __( 'Cambiar Nombre MPN', 'woo-add-mpn' ),
                    'desc_tip' => __( 'Enter the label you\'d like to use instead of MPN.', 'woo-add-mpn' ),
                    'id'       => 'hwp_mpn_text',
                    'type'     => 'text',
                    'placeholder' => 'MPN',
                );
                
                $settings[] = array( 'type' => 'sectionend', 'id' => 'hwp_mpn_text' );

                return $settings;
            
            /**
             * If not, return the standard settings
             **/
            } else {
                return $settings;
            }

        }

    }

    $Woo_MPN_Admin = new Woo_MPN_Admin();
    $Woo_MPN_Admin->instance();

} // end class_exists check