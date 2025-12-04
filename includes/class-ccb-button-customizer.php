<?php
/**
 * Button Customizer Class
 *
 * Handles button text customization and redirect functionality
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class CCB_Button_Customizer {
    
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
        // Hook into WooCommerce button text filters
        add_filter( 'woocommerce_product_add_to_cart_text', array( $this, 'custom_button_text' ), 10, 2 );
        add_filter( 'woocommerce_product_single_add_to_cart_text', array( $this, 'custom_single_button_text' ), 10, 2 );
        
        // Hook into button URL for redirect functionality
        add_filter( 'woocommerce_loop_add_to_cart_link', array( $this, 'redirect_to_product_page' ), 10, 2 );
        
        // Add custom styles
        add_action( 'wp_head', array( $this, 'add_custom_styles' ) );
    }
    
    /**
     * Customize button text for shop/archive pages
     */
    public function custom_button_text( $text, $product ) {
        // Check if custom text is enabled
        if ( 'yes' !== get_option( 'ccb_enable_custom_text', 'no' ) ) {
            return $text;
        }
        
        // Check for product-level override
        $override_text = get_post_meta( $product->get_id(), '_ccb_override_text', true );
        if ( 'yes' === $override_text ) {
            $custom_text = get_post_meta( $product->get_id(), '_ccb_custom_text', true );
            if ( ! empty( $custom_text ) ) {
                return $custom_text;
            }
        }
        
        // Check if product is out of stock
        if ( ! $product->is_in_stock() ) {
            $out_of_stock_text = get_option( 'ccb_out_of_stock_text', '' );
            if ( ! empty( $out_of_stock_text ) ) {
                return $out_of_stock_text;
            }
        }
        
        // Get custom text based on product type
        $product_type = $product->get_type();
        
        switch ( $product_type ) {
            case 'simple':
                $custom_text = get_option( 'ccb_shop_button_text', '' );
                break;
            case 'variable':
                $custom_text = get_option( 'ccb_variable_button_text', '' );
                break;
            case 'grouped':
                $custom_text = get_option( 'ccb_grouped_button_text', '' );
                break;
            case 'external':
                $custom_text = get_option( 'ccb_external_button_text', '' );
                break;
            default:
                $custom_text = get_option( 'ccb_shop_button_text', '' );
                break;
        }
        
        return ! empty( $custom_text ) ? $custom_text : $text;
    }
    
    /**
     * Customize button text for single product pages
     */
    public function custom_single_button_text( $text, $product ) {
        // Check if custom text is enabled
        if ( 'yes' !== get_option( 'ccb_enable_custom_text', 'no' ) ) {
            return $text;
        }
        
        // Check for product-level override
        $override_text = get_post_meta( $product->get_id(), '_ccb_override_text', true );
        if ( 'yes' === $override_text ) {
            $custom_text = get_post_meta( $product->get_id(), '_ccb_custom_text', true );
            if ( ! empty( $custom_text ) ) {
                return $custom_text;
            }
        }
        
        // Check if product is out of stock
        if ( ! $product->is_in_stock() ) {
            $out_of_stock_text = get_option( 'ccb_out_of_stock_text', '' );
            if ( ! empty( $out_of_stock_text ) ) {
                return $out_of_stock_text;
            }
        }
        
        // Get custom text for single product
        $custom_text = get_option( 'ccb_single_button_text', '' );
        
        return ! empty( $custom_text ) ? $custom_text : $text;
    }
    
    /**
     * Redirect button to product page
     */
    public function redirect_to_product_page( $link, $product ) {
        // Check if redirect is enabled globally
        if ( 'yes' !== get_option( 'ccb_enable_redirect', 'no' ) ) {
            return $link;
        }
        
        // Check for product-level override
        $override_redirect = get_post_meta( $product->get_id(), '_ccb_override_redirect', true );
        if ( 'yes' === $override_redirect ) {
            $disable_redirect = get_post_meta( $product->get_id(), '_ccb_disable_redirect', true );
            if ( 'yes' === $disable_redirect ) {
                return $link;
            }
        }
        
        // Check if redirect is enabled for this product type
        $product_type = $product->get_type();
        $redirect_enabled = false;
        
        switch ( $product_type ) {
            case 'simple':
                $redirect_enabled = 'yes' === get_option( 'ccb_redirect_simple', 'yes' );
                break;
            case 'variable':
                $redirect_enabled = 'yes' === get_option( 'ccb_redirect_variable', 'yes' );
                break;
            case 'grouped':
                $redirect_enabled = 'yes' === get_option( 'ccb_redirect_grouped', 'yes' );
                break;
        }
        
        if ( ! $redirect_enabled ) {
            return $link;
        }
        
        // Get product URL
        $product_url = get_permalink( $product->get_id() );
        
        // Check if new tab is enabled
        $new_tab = 'yes' === get_option( 'ccb_redirect_new_tab', 'no' );
        $target = $new_tab ? ' target="_blank"' : '';
        
        // Get button text
        $button_text = $product->add_to_cart_text();
        
        // Build new link
        $link = sprintf(
            '<a href="%s" data-quantity="1" class="%s" %s rel="nofollow">%s</a>',
            esc_url( $product_url ),
            esc_attr( isset( $args['class'] ) ? $args['class'] : 'button product_type_' . $product_type ),
            $target,
            esc_html( $button_text )
        );
        
        return $link;
    }
    
    /**
     * Add custom styles
     */
    public function add_custom_styles() {
        // Check if custom styling is enabled
        if ( 'yes' !== get_option( 'ccb_enable_styling', 'no' ) ) {
            return;
        }
        
        // Get style settings
        $bg_color = get_option( 'ccb_bg_color', '#0073aa' );
        $text_color = get_option( 'ccb_text_color', '#ffffff' );
        $hover_bg_color = get_option( 'ccb_hover_bg_color', '#005177' );
        $hover_text_color = get_option( 'ccb_hover_text_color', '#ffffff' );
        $border_radius = get_option( 'ccb_border_radius', '4' );
        $padding = get_option( 'ccb_padding', '10px 20px' );
        $custom_css = get_option( 'ccb_custom_css', '' );
        
        ?>
        <style type="text/css">
            /* Customize Cart Button Styles */
            .woocommerce a.button.add_to_cart_button,
            .woocommerce a.button.product_type_simple,
            .woocommerce a.button.product_type_variable,
            .woocommerce a.button.product_type_grouped,
            .woocommerce a.button.product_type_external,
            .woocommerce button.button.single_add_to_cart_button,
            .woocommerce .single-product button.button.alt {
                background-color: <?php echo esc_attr( $bg_color ); ?> !important;
                color: <?php echo esc_attr( $text_color ); ?> !important;
                border-radius: <?php echo esc_attr( $border_radius ); ?>px !important;
                padding: <?php echo esc_attr( $padding ); ?> !important;
            }
            
            .woocommerce a.button.add_to_cart_button:hover,
            .woocommerce a.button.product_type_simple:hover,
            .woocommerce a.button.product_type_variable:hover,
            .woocommerce a.button.product_type_grouped:hover,
            .woocommerce a.button.product_type_external:hover,
            .woocommerce button.button.single_add_to_cart_button:hover,
            .woocommerce .single-product button.button.alt:hover {
                background-color: <?php echo esc_attr( $hover_bg_color ); ?> !important;
                color: <?php echo esc_attr( $hover_text_color ); ?> !important;
            }
            
            <?php
            // Output custom CSS
            if ( ! empty( $custom_css ) ) {
                echo wp_kses_post( $custom_css );
            }
            ?>
        </style>
        <?php
    }
}
