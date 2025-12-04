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

class WCPLT_Button_Customizer {

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

        // Hide quantity selector if enabled
        add_filter( 'woocommerce_loop_add_to_cart_args', array( $this, 'hide_quantity_selector' ), 10, 2 );

        // Apply table layout if enabled
        if ( 'yes' === get_option( 'wcplt_enable_table_layout', 'no' ) ) {
            add_action( 'woocommerce_before_shop_loop_item', array( $this, 'table_layout_start' ), 1 );
            add_action( 'woocommerce_after_shop_loop_item', array( $this, 'table_layout_end' ), 100 );
        }

        // Add custom styles
        add_action( 'wp_head', array( $this, 'add_custom_styles' ) );
    }

    /**
     * Customize button text for shop/archive pages
     */
    public function custom_button_text( $text, $product ) {
        // Check if custom text is enabled
        if ( 'yes' !== get_option( 'wcplt_enable_custom_text', 'no' ) ) {
            return $text;
        }

        // Check for product-level override
        $override_text = get_post_meta( $product->get_id(), '_wcplt_override_text', true );
        if ( 'yes' === $override_text ) {
            $custom_text = get_post_meta( $product->get_id(), '_wcplt_custom_text', true );
            if ( ! empty( $custom_text ) ) {
                return $custom_text;
            }
        }

        // Check if product is out of stock
        if ( ! $product->is_in_stock() ) {
            $out_of_stock_text = get_option( 'wcplt_out_of_stock_text', '' );
            if ( ! empty( $out_of_stock_text ) ) {
                return $out_of_stock_text;
            }
        }

        // Get custom text based on product type
        $product_type = $product->get_type();

        switch ( $product_type ) {
            case 'simple':
                $custom_text = get_option( 'wcplt_shop_button_text', '' );
                break;
            case 'variable':
                $custom_text = get_option( 'wcplt_variable_button_text', '' );
                break;
            case 'grouped':
                $custom_text = get_option( 'wcplt_grouped_button_text', '' );
                break;
            case 'external':
                $custom_text = get_option( 'wcplt_external_button_text', '' );
                break;
            default:
                $custom_text = get_option( 'wcplt_shop_button_text', '' );
                break;
        }

        return ! empty( $custom_text ) ? $custom_text : $text;
    }

    /**
     * Customize button text for single product pages
     */
    public function custom_single_button_text( $text, $product ) {
        // Check if custom text is enabled
        if ( 'yes' !== get_option( 'wcplt_enable_custom_text', 'no' ) ) {
            return $text;
        }

        // Check for product-level override
        $override_text = get_post_meta( $product->get_id(), '_wcplt_override_text', true );
        if ( 'yes' === $override_text ) {
            $custom_text = get_post_meta( $product->get_id(), '_wcplt_custom_text', true );
            if ( ! empty( $custom_text ) ) {
                return $custom_text;
            }
        }

        // Check if product is out of stock
        if ( ! $product->is_in_stock() ) {
            $out_of_stock_text = get_option( 'wcplt_out_of_stock_text', '' );
            if ( ! empty( $out_of_stock_text ) ) {
                return $out_of_stock_text;
            }
        }

        // Get custom text for single product
        $custom_text = get_option( 'wcplt_single_button_text', '' );

        return ! empty( $custom_text ) ? $custom_text : $text;
    }

    /**
     * Redirect button to product page
     */
    public function redirect_to_product_page( $link, $product ) {
        // Check if redirect is enabled globally
        if ( 'yes' !== get_option( 'wcplt_enable_redirect', 'no' ) ) {
            return $link;
        }

        // Check for product-level override
        $override_redirect = get_post_meta( $product->get_id(), '_wcplt_override_redirect', true );
        if ( 'yes' === $override_redirect ) {
            $disable_redirect = get_post_meta( $product->get_id(), '_wcplt_disable_redirect', true );
            if ( 'yes' === $disable_redirect ) {
                return $link;
            }
        }

        // Check if redirect is enabled for this product type
        $product_type = $product->get_type();
        $redirect_enabled = false;

        switch ( $product_type ) {
            case 'simple':
                $redirect_enabled = 'yes' === get_option( 'wcplt_redirect_simple', 'yes' );
                break;
            case 'variable':
                $redirect_enabled = 'yes' === get_option( 'wcplt_redirect_variable', 'yes' );
                break;
            case 'grouped':
                $redirect_enabled = 'yes' === get_option( 'wcplt_redirect_grouped', 'yes' );
                break;
        }

        if ( ! $redirect_enabled ) {
            return $link;
        }

        // Get product URL
        $product_url = get_permalink( $product->get_id() );

        // Check if new tab is enabled
        $new_tab = 'yes' === get_option( 'wcplt_redirect_new_tab', 'no' );
        $target = $new_tab ? ' target="_blank"' : '';

        // Get button text
        $button_text = $product->add_to_cart_text();

        // Build new link
        $link = sprintf(
            '<a href="%s" data-quantity="1" class="%s" %s rel="nofollow">%s</a>',
            esc_url( $product_url ),
            esc_attr( 'button product_type_' . $product_type ),
            $target,
            esc_html( $button_text )
        );

        return $link;
    }

    /**
     * Hide quantity selector
     */
    public function hide_quantity_selector( $args, $product ) {
        if ( 'yes' === get_option( 'wcplt_hide_quantity', 'no' ) ) {
            $args['quantity'] = 1;
        }
        return $args;
    }

    /**
     * Start table layout wrapper
     */
    public function table_layout_start() {
        global $product;
        echo '<div class="wcplt-table-row">';
        echo '<div class="wcplt-table-content">';
        echo '<div class="wcplt-table-title-desc">';
    }

    /**
     * End table layout wrapper
     */
    public function table_layout_end() {
        global $product;

        // Close title-desc wrapper
        echo '</div>'; // .wcplt-table-title-desc

        // Add description if enabled
        if ( 'yes' === get_option( 'wcplt_show_description', 'yes' ) ) {
            $description = $product->get_short_description();
            $char_limit = intval( get_option( 'wcplt_description_limit', 0 ) );

            if ( ! empty( $description ) ) {
                if ( $char_limit > 0 && strlen( $description ) > $char_limit ) {
                    $description = substr( $description, 0, $char_limit ) . '...';
                }
                echo '<div class="wcplt-table-description">' . wp_kses_post( $description ) . '</div>';
            }
        }

        // Close content wrapper
        echo '</div>'; // .wcplt-table-content

        // Add price and button wrapper
        echo '<div class="wcplt-table-actions">';

        // Note: WooCommerce will add price and button here, then we close in after_shop_loop_item
        echo '</div>'; // .wcplt-table-actions
        echo '</div>'; // .wcplt-table-row
    }

    /**
     * Add custom styles
     */
    public function add_custom_styles() {
        // Get all settings
        $enable_table_layout = get_option( 'wcplt_enable_table_layout', 'no' );
        $hide_quantity = get_option( 'wcplt_hide_quantity', 'no' );
        $enable_styling = get_option( 'wcplt_enable_styling', 'no' );
        $button_icon = get_option( 'wcplt_button_icon', 'yes' );
        $button_width = get_option( 'wcplt_button_width', 'auto' );
        $button_fixed_width = get_option( 'wcplt_button_fixed_width', '200' );
        $button_size = get_option( 'wcplt_button_size', 'medium' );

        // Get styling settings
        $bg_color = get_option( 'wcplt_bg_color', '#0073aa' );
        $text_color = get_option( 'wcplt_text_color', '#ffffff' );
        $hover_bg_color = get_option( 'wcplt_hover_bg_color', '#005177' );
        $hover_text_color = get_option( 'wcplt_hover_text_color', '#ffffff' );
        $border_radius = get_option( 'wcplt_border_radius', '4' );
        $padding = get_option( 'wcplt_padding', '10px 20px' );
        $custom_css = get_option( 'wcplt_custom_css', '' );

        ?>
        <style type="text/css">
            /* WC Product List Table - Layout Styles */
            <?php if ( 'yes' === $enable_table_layout ) : ?>
            .woocommerce ul.products li.product {
                display: block !important;
                width: 100% !important;
                margin: 0 0 1.5em !important;
                padding: 1.5em !important;
                background: #fff !important;
                border: 1px solid #e0e0e0 !important;
                border-radius: 4px !important;
                box-shadow: 0 1px 3px rgba(0,0,0,0.1) !important;
            }

            .wcplt-table-row {
                display: flex !important;
                align-items: flex-start !important;
                gap: 20px !important;
                width: 100% !important;
            }

            .wcplt-table-content {
                flex: 1 !important;
                display: flex !important;
                flex-direction: column !important;
                gap: 10px !important;
            }

            .wcplt-table-title-desc {
                display: flex !important;
                flex-direction: column !important;
                gap: 5px !important;
            }

            .woocommerce ul.products li.product .woocommerce-loop-product__title {
                font-size: 1.2em !important;
                margin: 0 !important;
                padding: 0 !important;
                color: #8b0000 !important;
            }

            .wcplt-table-description {
                color: #666 !important;
                font-size: 0.95em !important;
                line-height: 1.5 !important;
                margin: 0 !important;
            }

            .wcplt-table-actions {
                display: flex !important;
                flex-direction: column !important;
                align-items: flex-end !important;
                gap: 10px !important;
                min-width: 200px !important;
            }

            .woocommerce ul.products li.product .price {
                font-size: 1.5em !important;
                font-weight: bold !important;
                color: #333 !important;
                margin: 0 !important;
            }

            .woocommerce ul.products li.product .button {
                margin: 0 !important;
            }
            <?php endif; ?>

            /* Hide Quantity Selector */
            <?php if ( 'yes' === $hide_quantity ) : ?>
            .woocommerce ul.products li.product .quantity,
            .woocommerce div.product form.cart .quantity {
                display: none !important;
            }
            <?php endif; ?>

            /* Button Icon */
            <?php if ( 'yes' === $button_icon ) : ?>
            .woocommerce a.button.add_to_cart_button:before,
            .woocommerce a.button.product_type_simple:before,
            .woocommerce a.button.product_type_variable:before,
            .woocommerce a.button.product_type_grouped:before,
            .woocommerce a.button.product_type_external:before {
                content: "\f07a" !important;
                font-family: "dashicons" !important;
                margin-right: 0.5em !important;
                display: inline-block !important;
            }
            <?php endif; ?>

            /* Button Width */
            <?php if ( 'full' === $button_width ) : ?>
            .woocommerce a.button.add_to_cart_button,
            .woocommerce a.button.product_type_simple,
            .woocommerce a.button.product_type_variable,
            .woocommerce a.button.product_type_grouped,
            .woocommerce a.button.product_type_external {
                width: 100% !important;
                display: block !important;
            }
            <?php elseif ( 'fixed' === $button_width ) : ?>
            .woocommerce a.button.add_to_cart_button,
            .woocommerce a.button.product_type_simple,
            .woocommerce a.button.product_type_variable,
            .woocommerce a.button.product_type_grouped,
            .woocommerce a.button.product_type_external {
                width: <?php echo esc_attr( $button_fixed_width ); ?>px !important;
                display: inline-block !important;
                text-align: center !important;
            }
            <?php endif; ?>

            /* Button Size */
            <?php if ( 'small' === $button_size ) : ?>
            .woocommerce a.button.add_to_cart_button,
            .woocommerce a.button.product_type_simple,
            .woocommerce a.button.product_type_variable,
            .woocommerce a.button.product_type_grouped,
            .woocommerce a.button.product_type_external {
                font-size: 0.85em !important;
                padding: 6px 12px !important;
            }
            <?php elseif ( 'large' === $button_size ) : ?>
            .woocommerce a.button.add_to_cart_button,
            .woocommerce a.button.product_type_simple,
            .woocommerce a.button.product_type_variable,
            .woocommerce a.button.product_type_grouped,
            .woocommerce a.button.product_type_external {
                font-size: 1.15em !important;
                padding: 14px 28px !important;
            }
            <?php endif; ?>

            /* Custom Button Styling */
            <?php if ( 'yes' === $enable_styling ) : ?>
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
            <?php endif; ?>

            /* Custom CSS */
            <?php
            if ( ! empty( $custom_css ) ) {
                echo wp_kses_post( $custom_css );
            }
            ?>
        </style>
        <?php
    }
}
