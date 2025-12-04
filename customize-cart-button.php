<?php
/**
 * Plugin Name: Customize Cart Button
 * Plugin URI: https://github.com/webjive/Customize-Cart-Button
 * Description: Customize WooCommerce Add to Cart button text, styling, and redirect to product page instead of adding to cart
 * Version: 1.0.0
 * Author: WebJIVE
 * Author URI: https://web-jive.com
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: customize-cart-button
 * Requires at least: 5.0
 * Requires PHP: 7.2
 * WC requires at least: 3.0
 * WC tested up to: 9.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Define plugin constants
define( 'CCB_VERSION', '1.0.0' );
define( 'CCB_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'CCB_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'CCB_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );

/**
 * Main Customize Cart Button Class
 */
class Customize_Cart_Button {
    
    /**
     * Instance of this class
     */
    private static $instance = null;
    
    /**
     * Get instance
     */
    public static function get_instance() {
        if ( null === self::$instance ) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    /**
     * Constructor
     */
    private function __construct() {
        add_action( 'plugins_loaded', array( $this, 'init' ) );
    }
    
    /**
     * Initialize plugin
     */
    public function init() {
        // Check if WooCommerce is active
        if ( ! $this->is_woocommerce_active() ) {
            add_action( 'admin_notices', array( $this, 'woocommerce_missing_notice' ) );
            return;
        }
        
        // Load text domain
        load_plugin_textdomain( 'customize-cart-button', false, dirname( CCB_PLUGIN_BASENAME ) . '/languages' );
        
        // Initialize features
        $this->includes();
        $this->init_hooks();
    }
    
    /**
     * Include required files
     */
    private function includes() {
        // Include button customizer (doesn't require WooCommerce classes)
        require_once CCB_PLUGIN_DIR . 'includes/class-ccb-button-customizer.php';
    }
    
    /**
     * Initialize hooks
     */
    private function init_hooks() {
        // Add settings page to WooCommerce
        add_filter( 'woocommerce_get_settings_pages', array( $this, 'add_settings_page' ) );
        
        // Initialize button customizer
        CCB_Button_Customizer::get_instance();
    }
    
    /**
     * Add settings page to WooCommerce
     */
    public function add_settings_page( $settings ) {
        // Include settings class here, after WooCommerce is loaded
        if ( ! class_exists( 'CCB_Settings' ) ) {
            require_once CCB_PLUGIN_DIR . 'includes/class-ccb-settings.php';
        }
        $settings[] = CCB_Settings::get_instance();
        return $settings;
    }
    
    /**
     * Check if WooCommerce is active
     */
    private function is_woocommerce_active() {
        return class_exists( 'WooCommerce' );
    }
    
    /**
     * WooCommerce missing notice
     */
    public function woocommerce_missing_notice() {
        ?>
        <div class="notice notice-error">
            <p><?php esc_html_e( 'Customize Cart Button requires WooCommerce to be installed and active.', 'customize-cart-button' ); ?></p>
        </div>
        <?php
    }
}

/**
 * Initialize plugin
 */
function customize_cart_button() {
    return Customize_Cart_Button::get_instance();
}

// Start the plugin
customize_cart_button();
