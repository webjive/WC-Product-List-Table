<?php
/**
 * Admin Settings Class
 *
 * Handles the admin settings page and options
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class CCB_Settings extends WC_Settings_Page {
    
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
    public function __construct() {
        $this->id    = 'ccb_settings';
        $this->label = __( 'Cart Button', 'customize-cart-button' );
        
        parent::__construct();
        
        // Add product metabox
        add_action( 'add_meta_boxes', array( $this, 'add_product_metabox' ) );
        add_action( 'save_post', array( $this, 'save_product_metabox' ) );
    }
    
    /**
     * Get sections
     */
    public function get_sections() {
        return array(
            ''          => __( 'Button Text', 'customize-cart-button' ),
            'redirect'  => __( 'Redirect Settings', 'customize-cart-button' ),
            'styling'   => __( 'Button Styling', 'customize-cart-button' ),
        );
    }
    
    /**
     * Get settings array
     */
    public function get_settings( $current_section = '' ) {
        if ( 'redirect' === $current_section ) {
            return $this->get_redirect_settings();
        } elseif ( 'styling' === $current_section ) {
            return $this->get_styling_settings();
        } else {
            return $this->get_button_text_settings();
        }
    }
    
    /**
     * Get button text settings
     */
    private function get_button_text_settings() {
        return array(
            array(
                'title' => __( 'Button Text Settings', 'customize-cart-button' ),
                'type'  => 'title',
                'desc'  => __( 'Customize the Add to Cart button text for different contexts', 'customize-cart-button' ),
                'id'    => 'ccb_text_settings',
            ),
            array(
                'title'    => __( 'Enable Custom Text', 'customize-cart-button' ),
                'desc'     => __( 'Enable custom button text', 'customize-cart-button' ),
                'id'       => 'ccb_enable_custom_text',
                'default'  => 'no',
                'type'     => 'checkbox',
            ),
            array(
                'title'    => __( 'Shop Page Button Text', 'customize-cart-button' ),
                'desc'     => __( 'Button text on shop/archive pages', 'customize-cart-button' ),
                'id'       => 'ccb_shop_button_text',
                'default'  => 'Add to Cart',
                'type'     => 'text',
                'css'      => 'min-width:300px;',
            ),
            array(
                'title'    => __( 'Single Product Button Text', 'customize-cart-button' ),
                'desc'     => __( 'Button text on single product pages', 'customize-cart-button' ),
                'id'       => 'ccb_single_button_text',
                'default'  => 'Add to Cart',
                'type'     => 'text',
                'css'      => 'min-width:300px;',
            ),
            array(
                'title'    => __( 'Variable Product Button Text', 'customize-cart-button' ),
                'desc'     => __( 'Button text for variable products', 'customize-cart-button' ),
                'id'       => 'ccb_variable_button_text',
                'default'  => 'Select Options',
                'type'     => 'text',
                'css'      => 'min-width:300px;',
            ),
            array(
                'title'    => __( 'Grouped Product Button Text', 'customize-cart-button' ),
                'desc'     => __( 'Button text for grouped products', 'customize-cart-button' ),
                'id'       => 'ccb_grouped_button_text',
                'default'  => 'View Products',
                'type'     => 'text',
                'css'      => 'min-width:300px;',
            ),
            array(
                'title'    => __( 'External Product Button Text', 'customize-cart-button' ),
                'desc'     => __( 'Button text for external/affiliate products', 'customize-cart-button' ),
                'id'       => 'ccb_external_button_text',
                'default'  => 'Buy Now',
                'type'     => 'text',
                'css'      => 'min-width:300px;',
            ),
            array(
                'title'    => __( 'Out of Stock Button Text', 'customize-cart-button' ),
                'desc'     => __( 'Button text when product is out of stock', 'customize-cart-button' ),
                'id'       => 'ccb_out_of_stock_text',
                'default'  => 'Read More',
                'type'     => 'text',
                'css'      => 'min-width:300px;',
            ),
            array(
                'type' => 'sectionend',
                'id'   => 'ccb_text_settings',
            ),
        );
    }
    
    /**
     * Get redirect settings
     */
    private function get_redirect_settings() {
        return array(
            array(
                'title' => __( 'Redirect Settings', 'customize-cart-button' ),
                'type'  => 'title',
                'desc'  => __( 'Redirect Add to Cart buttons to product pages instead of adding to cart', 'customize-cart-button' ),
                'id'    => 'ccb_redirect_settings',
            ),
            array(
                'title'    => __( 'Enable Redirect', 'customize-cart-button' ),
                'desc'     => __( 'Redirect to product page instead of adding to cart', 'customize-cart-button' ),
                'id'       => 'ccb_enable_redirect',
                'default'  => 'no',
                'type'     => 'checkbox',
            ),
            array(
                'title'    => __( 'Redirect Simple Products', 'customize-cart-button' ),
                'desc'     => __( 'Enable redirect for simple products', 'customize-cart-button' ),
                'id'       => 'ccb_redirect_simple',
                'default'  => 'yes',
                'type'     => 'checkbox',
            ),
            array(
                'title'    => __( 'Redirect Variable Products', 'customize-cart-button' ),
                'desc'     => __( 'Enable redirect for variable products', 'customize-cart-button' ),
                'id'       => 'ccb_redirect_variable',
                'default'  => 'yes',
                'type'     => 'checkbox',
            ),
            array(
                'title'    => __( 'Redirect Grouped Products', 'customize-cart-button' ),
                'desc'     => __( 'Enable redirect for grouped products', 'customize-cart-button' ),
                'id'       => 'ccb_redirect_grouped',
                'default'  => 'yes',
                'type'     => 'checkbox',
            ),
            array(
                'title'    => __( 'Open in New Tab', 'customize-cart-button' ),
                'desc'     => __( 'Open product page in new tab when redirecting', 'customize-cart-button' ),
                'id'       => 'ccb_redirect_new_tab',
                'default'  => 'no',
                'type'     => 'checkbox',
            ),
            array(
                'type' => 'sectionend',
                'id'   => 'ccb_redirect_settings',
            ),
        );
    }
    
    /**
     * Get styling settings
     */
    private function get_styling_settings() {
        return array(
            array(
                'title' => __( 'Button Styling', 'customize-cart-button' ),
                'type'  => 'title',
                'desc'  => __( 'Customize the appearance of Add to Cart buttons', 'customize-cart-button' ),
                'id'    => 'ccb_styling_settings',
            ),
            array(
                'title'    => __( 'Enable Custom Styling', 'customize-cart-button' ),
                'desc'     => __( 'Enable custom button styling', 'customize-cart-button' ),
                'id'       => 'ccb_enable_styling',
                'default'  => 'no',
                'type'     => 'checkbox',
            ),
            array(
                'title'    => __( 'Button Background Color', 'customize-cart-button' ),
                'desc'     => __( 'Choose button background color', 'customize-cart-button' ),
                'id'       => 'ccb_bg_color',
                'default'  => '#0073aa',
                'type'     => 'color',
            ),
            array(
                'title'    => __( 'Button Text Color', 'customize-cart-button' ),
                'desc'     => __( 'Choose button text color', 'customize-cart-button' ),
                'id'       => 'ccb_text_color',
                'default'  => '#ffffff',
                'type'     => 'color',
            ),
            array(
                'title'    => __( 'Button Hover Background Color', 'customize-cart-button' ),
                'desc'     => __( 'Choose button background color on hover', 'customize-cart-button' ),
                'id'       => 'ccb_hover_bg_color',
                'default'  => '#005177',
                'type'     => 'color',
            ),
            array(
                'title'    => __( 'Button Hover Text Color', 'customize-cart-button' ),
                'desc'     => __( 'Choose button text color on hover', 'customize-cart-button' ),
                'id'       => 'ccb_hover_text_color',
                'default'  => '#ffffff',
                'type'     => 'color',
            ),
            array(
                'title'       => __( 'Button Border Radius', 'customize-cart-button' ),
                'desc'        => __( 'Border radius in pixels (e.g., 4)', 'customize-cart-button' ),
                'id'          => 'ccb_border_radius',
                'default'     => '4',
                'type'        => 'number',
                'css'         => 'width: 100px;',
                'custom_attributes' => array(
                    'min'  => '0',
                    'step' => '1',
                ),
            ),
            array(
                'title'       => __( 'Button Padding', 'customize-cart-button' ),
                'desc'        => __( 'Padding in format: top right bottom left (e.g., 10px 20px)', 'customize-cart-button' ),
                'id'          => 'ccb_padding',
                'default'     => '10px 20px',
                'type'        => 'text',
                'css'         => 'min-width:300px;',
            ),
            array(
                'title'       => __( 'Custom CSS', 'customize-cart-button' ),
                'desc'        => __( 'Add custom CSS for further customization', 'customize-cart-button' ),
                'id'          => 'ccb_custom_css',
                'default'     => '',
                'type'        => 'textarea',
                'css'         => 'width:100%; height: 150px; font-family: monospace;',
            ),
            array(
                'type' => 'sectionend',
                'id'   => 'ccb_styling_settings',
            ),
        );
    }
    
    /**
     * Add product metabox
     */
    public function add_product_metabox() {
        add_meta_box(
            'ccb_product_settings',
            __( 'Customize Cart Button', 'customize-cart-button' ),
            array( $this, 'render_product_metabox' ),
            'product',
            'side',
            'default'
        );
    }
    
    /**
     * Render product metabox
     */
    public function render_product_metabox( $post ) {
        wp_nonce_field( 'ccb_product_metabox', 'ccb_product_metabox_nonce' );
        
        $override_text = get_post_meta( $post->ID, '_ccb_override_text', true );
        $custom_text = get_post_meta( $post->ID, '_ccb_custom_text', true );
        $override_redirect = get_post_meta( $post->ID, '_ccb_override_redirect', true );
        $disable_redirect = get_post_meta( $post->ID, '_ccb_disable_redirect', true );
        
        ?>
        <div class="ccb-product-settings">
            <p>
                <label>
                    <input type="checkbox" name="ccb_override_text" value="yes" <?php checked( $override_text, 'yes' ); ?> />
                    <?php esc_html_e( 'Override button text', 'customize-cart-button' ); ?>
                </label>
            </p>
            <p>
                <label><?php esc_html_e( 'Custom button text:', 'customize-cart-button' ); ?></label>
                <input type="text" name="ccb_custom_text" value="<?php echo esc_attr( $custom_text ); ?>" style="width: 100%;" />
            </p>
            <hr>
            <p>
                <label>
                    <input type="checkbox" name="ccb_override_redirect" value="yes" <?php checked( $override_redirect, 'yes' ); ?> />
                    <?php esc_html_e( 'Override redirect settings', 'customize-cart-button' ); ?>
                </label>
            </p>
            <p>
                <label>
                    <input type="checkbox" name="ccb_disable_redirect" value="yes" <?php checked( $disable_redirect, 'yes' ); ?> />
                    <?php esc_html_e( 'Disable redirect for this product', 'customize-cart-button' ); ?>
                </label>
            </p>
        </div>
        <?php
    }
    
    /**
     * Save product metabox
     */
    public function save_product_metabox( $post_id ) {
        // Check nonce
        if ( ! isset( $_POST['ccb_product_metabox_nonce'] ) || ! wp_verify_nonce( $_POST['ccb_product_metabox_nonce'], 'ccb_product_metabox' ) ) {
            return;
        }
        
        // Check autosave
        if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
            return;
        }
        
        // Check permissions
        if ( ! current_user_can( 'edit_post', $post_id ) ) {
            return;
        }
        
        // Save override text
        $override_text = isset( $_POST['ccb_override_text'] ) ? 'yes' : 'no';
        update_post_meta( $post_id, '_ccb_override_text', $override_text );
        
        // Save custom text
        if ( isset( $_POST['ccb_custom_text'] ) ) {
            update_post_meta( $post_id, '_ccb_custom_text', sanitize_text_field( $_POST['ccb_custom_text'] ) );
        }
        
        // Save override redirect
        $override_redirect = isset( $_POST['ccb_override_redirect'] ) ? 'yes' : 'no';
        update_post_meta( $post_id, '_ccb_override_redirect', $override_redirect );
        
        // Save disable redirect
        $disable_redirect = isset( $_POST['ccb_disable_redirect'] ) ? 'yes' : 'no';
        update_post_meta( $post_id, '_ccb_disable_redirect', $disable_redirect );
    }
    
    /**
     * Get option with default
     */
    public static function get_option( $key, $default = '' ) {
        return get_option( $key, $default );
    }
    
    /**
     * Update option
     */
    public static function update_option( $key, $value ) {
        return update_option( $key, $value );
    }
}
