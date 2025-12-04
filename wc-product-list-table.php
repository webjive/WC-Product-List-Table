<?php
/**
 * Plugin Name: WC Product List Table
 * Plugin URI: https://github.com/webjive/WC-Product-List-Table
 * Description: Customize WooCommerce product displays with advanced cart button customization, styling, and redirect functionality for product list tables
 * Version: 2.2.2
 * Author: WebJIVE
 * Author URI: https://web-jive.com
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: wc-product-list-table
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
define( 'WCPLT_VERSION', '2.2.2' );
define( 'WCPLT_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'WCPLT_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'WCPLT_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );

/**
 * Main WC Product List Table Class
 */
class WC_Product_List_Table {

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

        // Run migration on plugin activation
        register_activation_hook( __FILE__, array( $this, 'activate' ) );
    }

    /**
     * Plugin activation
     */
    public function activate() {
        // Run migration from old plugin
        $this->migrate_from_old_plugin();
    }

    /**
     * Migrate data from old Customize Cart Button plugin
     */
    private function migrate_from_old_plugin() {
        // Check if migration already done
        if ( get_option( 'wcplt_migration_done' ) ) {
            return;
        }

        // Old option keys
        $old_options = array(
            'ccb_enable_custom_text' => 'wcplt_enable_custom_text',
            'ccb_shop_button_text' => 'wcplt_shop_button_text',
            'ccb_single_button_text' => 'wcplt_single_button_text',
            'ccb_variable_button_text' => 'wcplt_variable_button_text',
            'ccb_grouped_button_text' => 'wcplt_grouped_button_text',
            'ccb_external_button_text' => 'wcplt_external_button_text',
            'ccb_out_of_stock_text' => 'wcplt_out_of_stock_text',
            'ccb_enable_redirect' => 'wcplt_enable_redirect',
            'ccb_redirect_simple' => 'wcplt_redirect_simple',
            'ccb_redirect_variable' => 'wcplt_redirect_variable',
            'ccb_redirect_grouped' => 'wcplt_redirect_grouped',
            'ccb_redirect_new_tab' => 'wcplt_redirect_new_tab',
            'ccb_enable_styling' => 'wcplt_enable_styling',
            'ccb_bg_color' => 'wcplt_bg_color',
            'ccb_text_color' => 'wcplt_text_color',
            'ccb_hover_bg_color' => 'wcplt_hover_bg_color',
            'ccb_hover_text_color' => 'wcplt_hover_text_color',
            'ccb_border_radius' => 'wcplt_border_radius',
            'ccb_padding' => 'wcplt_padding',
            'ccb_custom_css' => 'wcplt_custom_css',
        );

        // Migrate options
        foreach ( $old_options as $old_key => $new_key ) {
            $old_value = get_option( $old_key );
            if ( false !== $old_value ) {
                update_option( $new_key, $old_value );
            }
        }

        // Migrate product meta
        global $wpdb;

        $old_meta_keys = array(
            '_ccb_override_text' => '_wcplt_override_text',
            '_ccb_custom_text' => '_wcplt_custom_text',
            '_ccb_override_redirect' => '_wcplt_override_redirect',
            '_ccb_disable_redirect' => '_wcplt_disable_redirect',
        );

        foreach ( $old_meta_keys as $old_meta => $new_meta ) {
            $wpdb->query(
                $wpdb->prepare(
                    "UPDATE {$wpdb->postmeta} SET meta_key = %s WHERE meta_key = %s",
                    $new_meta,
                    $old_meta
                )
            );
        }

        // Mark migration as complete
        update_option( 'wcplt_migration_done', true );
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
        load_plugin_textdomain( 'wc-product-list-table', false, dirname( WCPLT_PLUGIN_BASENAME ) . '/languages' );

        // Initialize features
        $this->includes();
        $this->init_hooks();
    }

    /**
     * Include required files
     */
    private function includes() {
        // Include button customizer (doesn't require WooCommerce classes)
        require_once WCPLT_PLUGIN_DIR . 'includes/class-wcplt-button-customizer.php';

        // Include shortcode handler
        require_once WCPLT_PLUGIN_DIR . 'includes/class-wcplt-shortcode.php';
    }

    /**
     * Initialize hooks
     */
    private function init_hooks() {
        // Add settings page to WooCommerce
        add_filter( 'woocommerce_get_settings_pages', array( $this, 'add_settings_page' ) );

        // Initialize button customizer
        WCPLT_Button_Customizer::get_instance();

        // Initialize shortcode
        WCPLT_Shortcode::get_instance();
    }

    /**
     * Add settings page to WooCommerce
     */
    public function add_settings_page( $settings ) {
        // Include settings class here, after WooCommerce is loaded
        if ( ! class_exists( 'WCPLT_Settings' ) ) {
            require_once WCPLT_PLUGIN_DIR . 'includes/class-wcplt-settings.php';
        }
        $settings[] = WCPLT_Settings::get_instance();
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
            <p><?php esc_html_e( 'WC Product List Table requires WooCommerce to be installed and active.', 'wc-product-list-table' ); ?></p>
        </div>
        <?php
    }
}

/**
 * Initialize plugin
 */
function wc_product_list_table() {
    return WC_Product_List_Table::get_instance();
}

// Start the plugin
wc_product_list_table();
