<?php
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

if( !class_exists( 'Woo_MPN' ) ) {

    /**
     * Main Woo_MPN class
     *
     * @since       0.1.0
     */
    class Woo_MPN {

        /**
         * @var         Woo_MPN $instance The one true Woo_MPN
         * @since       0.1.0
         */
        private static $instance;


        /**
         * Get active instance
         *
         * @access      public
         * @since       0.1.0
         * @return      self The one true Woo_MPN
         */
        public static function instance() {
            if( !self::$instance ) {
                self::$instance = new Woo_MPN();
                self::$instance->setup_constants();
                self::$instance->includes();
                self::$instance->load_textdomain();
                // self::$instance->hooks();
            }

            return self::$instance;
        }


        /**
         * Setup plugin constants
         *
         * @access      private
         * @since       0.1.0
         * @return      void
         */
        private function setup_constants() {
            // Plugin version
            define( 'Woo_MPN_VER', '0.5.1' );

            // Plugin path
            define( 'Woo_MPN_DIR', plugin_dir_path( __FILE__ ) );

            // Plugin URL
            define( 'Woo_MPN_URL', plugin_dir_url( __FILE__ ) );
        }


        /**
         * Include necessary files
         *
         * @access      private
         * @since       0.1.0
         * @return      void
         */
        private function includes() {

            require_once Woo_MPN_DIR . 'includes/class-woo-mpn-functions.php';

            if( is_admin() )
                require_once Woo_MPN_DIR . 'includes/class-woo-mpn-admin.php';
            
        }


        /**
         * Run action and filter hooks
         *
         * @access      private
         * @since       0.1.0
         * @return      void
         *
         *
         */
        private function hooks() {

        }


        /**
         * Internationalization
         *
         * @access      public
         * @since       0.1.0
         * @return      void
         */
        public function load_textdomain() {

            load_plugin_textdomain( 'woo-add-mpn' );
            
        }

    }
} // End if class_exists check


/**
 * The main function responsible for returning the one true
 * instance to functions everywhere
 *
 * @since       0.1.0
 * @return      \Woo_MPN The one true Woo_MPB
 *
 */
function woo_mpn_load() {

    // Check if WooCommerce is active
    if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
        return Woo_MPN::instance();
    }

}
add_action( 'plugins_loaded', 'woo_mpn_load' );


/**
 * The activation hook is called outside of the singleton because WordPress doesn't
 * register the call from within the class, since we are preferring the plugins_loaded
 * hook for compatibility, we also can't reference a function inside the plugin class
 * for the activation function. If you need an activation function, put it here.
 *
 * @since       0.1.0
 * @return      void
 */
function woo_mpn_activation() {
    /* Activation functions here */
}
register_activation_hook( __FILE__, 'woo_mpn_activation' );