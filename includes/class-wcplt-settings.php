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

class WCPLT_Settings extends WC_Settings_Page {

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
        $this->id    = 'wcplt_settings';
        $this->label = __( 'Product List Table', 'wc-product-list-table' );

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
            ''          => __( 'Layout & Display', 'wc-product-list-table' ),
            'text'      => __( 'Button Text', 'wc-product-list-table' ),
            'redirect'  => __( 'Redirect Settings', 'wc-product-list-table' ),
            'styling'   => __( 'Button Styling', 'wc-product-list-table' ),
        );
    }

    /**
     * Get settings array
     */
    public function get_settings( $current_section = '' ) {
        if ( 'text' === $current_section ) {
            return $this->get_button_text_settings();
        } elseif ( 'redirect' === $current_section ) {
            return $this->get_redirect_settings();
        } elseif ( 'styling' === $current_section ) {
            return $this->get_styling_settings();
        } else {
            return $this->get_layout_settings();
        }
    }

    /**
     * Get layout and display settings
     */
    private function get_layout_settings() {
        return array(
            array(
                'title' => __( 'Product List Layout', 'wc-product-list-table' ),
                'type'  => 'title',
                'desc'  => __( 'Control how products are displayed in shop/archive pages', 'wc-product-list-table' ),
                'id'    => 'wcplt_layout_settings',
            ),
            array(
                'title'    => __( 'Enable Table Layout', 'wc-product-list-table' ),
                'desc'     => __( 'Display products in a clean table/list format with improved spacing', 'wc-product-list-table' ),
                'id'       => 'wcplt_enable_table_layout',
                'default'  => 'no',
                'type'     => 'checkbox',
            ),
            array(
                'title'    => __( 'Hide Quantity Selector', 'wc-product-list-table' ),
                'desc'     => __( 'Remove the quantity input field from product listings', 'wc-product-list-table' ),
                'id'       => 'wcplt_hide_quantity',
                'default'  => 'no',
                'type'     => 'checkbox',
            ),
            array(
                'title'    => __( 'Show Product Short Description', 'wc-product-list-table' ),
                'desc'     => __( 'Display product short description in table layout', 'wc-product-list-table' ),
                'id'       => 'wcplt_show_description',
                'default'  => 'yes',
                'type'     => 'checkbox',
            ),
            array(
                'title'       => __( 'Description Character Limit', 'wc-product-list-table' ),
                'desc'        => __( 'Maximum characters for description (0 = unlimited)', 'wc-product-list-table' ),
                'id'          => 'wcplt_description_limit',
                'default'     => '0',
                'type'        => 'number',
                'css'         => 'width: 100px;',
                'custom_attributes' => array(
                    'min'  => '0',
                    'step' => '1',
                ),
            ),
            array(
                'type' => 'sectionend',
                'id'   => 'wcplt_layout_settings',
            ),
            array(
                'title' => __( 'Button Display Options', 'wc-product-list-table' ),
                'type'  => 'title',
                'desc'  => __( 'Customize Add to Cart button appearance and behavior', 'wc-product-list-table' ),
                'id'    => 'wcplt_button_display',
            ),
            array(
                'title'    => __( 'Button Icon', 'wc-product-list-table' ),
                'desc'     => __( 'Show cart icon in Add to Cart button', 'wc-product-list-table' ),
                'id'       => 'wcplt_button_icon',
                'default'  => 'yes',
                'type'     => 'checkbox',
            ),
            array(
                'title'    => __( 'Button Width', 'wc-product-list-table' ),
                'desc'     => __( 'Set button width style', 'wc-product-list-table' ),
                'id'       => 'wcplt_button_width',
                'default'  => 'auto',
                'type'     => 'select',
                'options'  => array(
                    'auto'  => __( 'Auto (fit content)', 'wc-product-list-table' ),
                    'full'  => __( 'Full width', 'wc-product-list-table' ),
                    'fixed' => __( 'Fixed width (specify below)', 'wc-product-list-table' ),
                ),
            ),
            array(
                'title'       => __( 'Fixed Button Width', 'wc-product-list-table' ),
                'desc'        => __( 'Width in pixels when using fixed width (e.g., 200)', 'wc-product-list-table' ),
                'id'          => 'wcplt_button_fixed_width',
                'default'     => '200',
                'type'        => 'number',
                'css'         => 'width: 100px;',
                'custom_attributes' => array(
                    'min'  => '50',
                    'step' => '1',
                ),
            ),
            array(
                'title'    => __( 'Button Size', 'wc-product-list-table' ),
                'desc'     => __( 'Choose button size', 'wc-product-list-table' ),
                'id'       => 'wcplt_button_size',
                'default'  => 'medium',
                'type'     => 'select',
                'options'  => array(
                    'small'  => __( 'Small', 'wc-product-list-table' ),
                    'medium' => __( 'Medium', 'wc-product-list-table' ),
                    'large'  => __( 'Large', 'wc-product-list-table' ),
                ),
            ),
            array(
                'type' => 'sectionend',
                'id'   => 'wcplt_button_display',
            ),
        );
    }

    /**
     * Get button text settings
     */
    private function get_button_text_settings() {
        return array(
            array(
                'title' => __( 'Button Text Settings', 'wc-product-list-table' ),
                'type'  => 'title',
                'desc'  => __( 'Customize the Add to Cart button text for different contexts', 'wc-product-list-table' ),
                'id'    => 'wcplt_text_settings',
            ),
            array(
                'title'    => __( 'Enable Custom Text', 'wc-product-list-table' ),
                'desc'     => __( 'Enable custom button text', 'wc-product-list-table' ),
                'id'       => 'wcplt_enable_custom_text',
                'default'  => 'no',
                'type'     => 'checkbox',
            ),
            array(
                'title'    => __( 'Shop Page Button Text', 'wc-product-list-table' ),
                'desc'     => __( 'Button text on shop/archive pages', 'wc-product-list-table' ),
                'id'       => 'wcplt_shop_button_text',
                'default'  => 'Add to Cart',
                'type'     => 'text',
                'css'      => 'min-width:300px;',
            ),
            array(
                'title'    => __( 'Single Product Button Text', 'wc-product-list-table' ),
                'desc'     => __( 'Button text on single product pages', 'wc-product-list-table' ),
                'id'       => 'wcplt_single_button_text',
                'default'  => 'Add to Cart',
                'type'     => 'text',
                'css'      => 'min-width:300px;',
            ),
            array(
                'title'    => __( 'Variable Product Button Text', 'wc-product-list-table' ),
                'desc'     => __( 'Button text for variable products', 'wc-product-list-table' ),
                'id'       => 'wcplt_variable_button_text',
                'default'  => 'Select Options',
                'type'     => 'text',
                'css'      => 'min-width:300px;',
            ),
            array(
                'title'    => __( 'Grouped Product Button Text', 'wc-product-list-table' ),
                'desc'     => __( 'Button text for grouped products', 'wc-product-list-table' ),
                'id'       => 'wcplt_grouped_button_text',
                'default'  => 'View Products',
                'type'     => 'text',
                'css'      => 'min-width:300px;',
            ),
            array(
                'title'    => __( 'External Product Button Text', 'wc-product-list-table' ),
                'desc'     => __( 'Button text for external/affiliate products', 'wc-product-list-table' ),
                'id'       => 'wcplt_external_button_text',
                'default'  => 'Buy Now',
                'type'     => 'text',
                'css'      => 'min-width:300px;',
            ),
            array(
                'title'    => __( 'Out of Stock Button Text', 'wc-product-list-table' ),
                'desc'     => __( 'Button text when product is out of stock', 'wc-product-list-table' ),
                'id'       => 'wcplt_out_of_stock_text',
                'default'  => 'Read More',
                'type'     => 'text',
                'css'      => 'min-width:300px;',
            ),
            array(
                'type' => 'sectionend',
                'id'   => 'wcplt_text_settings',
            ),
        );
    }

    /**
     * Get redirect settings
     */
    private function get_redirect_settings() {
        return array(
            array(
                'title' => __( 'Redirect Settings', 'wc-product-list-table' ),
                'type'  => 'title',
                'desc'  => __( 'Redirect Add to Cart buttons to product pages instead of adding to cart', 'wc-product-list-table' ),
                'id'    => 'wcplt_redirect_settings',
            ),
            array(
                'title'    => __( 'Enable Redirect', 'wc-product-list-table' ),
                'desc'     => __( 'Redirect to product page instead of adding to cart', 'wc-product-list-table' ),
                'id'       => 'wcplt_enable_redirect',
                'default'  => 'no',
                'type'     => 'checkbox',
            ),
            array(
                'title'    => __( 'Redirect Simple Products', 'wc-product-list-table' ),
                'desc'     => __( 'Enable redirect for simple products', 'wc-product-list-table' ),
                'id'       => 'wcplt_redirect_simple',
                'default'  => 'yes',
                'type'     => 'checkbox',
            ),
            array(
                'title'    => __( 'Redirect Variable Products', 'wc-product-list-table' ),
                'desc'     => __( 'Enable redirect for variable products', 'wc-product-list-table' ),
                'id'       => 'wcplt_redirect_variable',
                'default'  => 'yes',
                'type'     => 'checkbox',
            ),
            array(
                'title'    => __( 'Redirect Grouped Products', 'wc-product-list-table' ),
                'desc'     => __( 'Enable redirect for grouped products', 'wc-product-list-table' ),
                'id'       => 'wcplt_redirect_grouped',
                'default'  => 'yes',
                'type'     => 'checkbox',
            ),
            array(
                'title'    => __( 'Open in New Tab', 'wc-product-list-table' ),
                'desc'     => __( 'Open product page in new tab when redirecting', 'wc-product-list-table' ),
                'id'       => 'wcplt_redirect_new_tab',
                'default'  => 'no',
                'type'     => 'checkbox',
            ),
            array(
                'type' => 'sectionend',
                'id'   => 'wcplt_redirect_settings',
            ),
        );
    }

    /**
     * Get styling settings
     */
    private function get_styling_settings() {
        return array(
            array(
                'title' => __( 'Button Styling', 'wc-product-list-table' ),
                'type'  => 'title',
                'desc'  => __( 'Customize the appearance of Add to Cart buttons', 'wc-product-list-table' ),
                'id'    => 'wcplt_styling_settings',
            ),
            array(
                'title'    => __( 'Enable Custom Styling', 'wc-product-list-table' ),
                'desc'     => __( 'Enable custom button styling', 'wc-product-list-table' ),
                'id'       => 'wcplt_enable_styling',
                'default'  => 'no',
                'type'     => 'checkbox',
            ),
            array(
                'title'    => __( 'Button Background Color', 'wc-product-list-table' ),
                'desc'     => __( 'Choose button background color', 'wc-product-list-table' ),
                'id'       => 'wcplt_bg_color',
                'default'  => '#0073aa',
                'type'     => 'color',
            ),
            array(
                'title'    => __( 'Button Text Color', 'wc-product-list-table' ),
                'desc'     => __( 'Choose button text color', 'wc-product-list-table' ),
                'id'       => 'wcplt_text_color',
                'default'  => '#ffffff',
                'type'     => 'color',
            ),
            array(
                'title'    => __( 'Button Hover Background Color', 'wc-product-list-table' ),
                'desc'     => __( 'Choose button background color on hover', 'wc-product-list-table' ),
                'id'       => 'wcplt_hover_bg_color',
                'default'  => '#005177',
                'type'     => 'color',
            ),
            array(
                'title'    => __( 'Button Hover Text Color', 'wc-product-list-table' ),
                'desc'     => __( 'Choose button text color on hover', 'wc-product-list-table' ),
                'id'       => 'wcplt_hover_text_color',
                'default'  => '#ffffff',
                'type'     => 'color',
            ),
            array(
                'title'       => __( 'Button Border Radius', 'wc-product-list-table' ),
                'desc'        => __( 'Border radius in pixels (e.g., 4)', 'wc-product-list-table' ),
                'id'          => 'wcplt_border_radius',
                'default'     => '4',
                'type'        => 'number',
                'css'         => 'width: 100px;',
                'custom_attributes' => array(
                    'min'  => '0',
                    'step' => '1',
                ),
            ),
            array(
                'title'       => __( 'Button Padding', 'wc-product-list-table' ),
                'desc'        => __( 'Padding in format: top right bottom left (e.g., 10px 20px)', 'wc-product-list-table' ),
                'id'          => 'wcplt_padding',
                'default'     => '10px 20px',
                'type'        => 'text',
                'css'         => 'min-width:300px;',
            ),
            array(
                'title'       => __( 'Custom CSS', 'wc-product-list-table' ),
                'desc'        => __( 'Add custom CSS for further customization', 'wc-product-list-table' ),
                'id'          => 'wcplt_custom_css',
                'default'     => '',
                'type'        => 'textarea',
                'css'         => 'width:100%; height: 150px; font-family: monospace;',
            ),
            array(
                'type' => 'sectionend',
                'id'   => 'wcplt_styling_settings',
            ),
        );
    }

    /**
     * Add product metabox
     */
    public function add_product_metabox() {
        add_meta_box(
            'wcplt_product_settings',
            __( 'Product List Table Settings', 'wc-product-list-table' ),
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
        wp_nonce_field( 'wcplt_product_metabox', 'wcplt_product_metabox_nonce' );

        $override_text = get_post_meta( $post->ID, '_wcplt_override_text', true );
        $custom_text = get_post_meta( $post->ID, '_wcplt_custom_text', true );
        $override_redirect = get_post_meta( $post->ID, '_wcplt_override_redirect', true );
        $disable_redirect = get_post_meta( $post->ID, '_wcplt_disable_redirect', true );

        ?>
        <div class="wcplt-product-settings">
            <p>
                <label>
                    <input type="checkbox" name="wcplt_override_text" value="yes" <?php checked( $override_text, 'yes' ); ?> />
                    <?php esc_html_e( 'Override button text', 'wc-product-list-table' ); ?>
                </label>
            </p>
            <p>
                <label><?php esc_html_e( 'Custom button text:', 'wc-product-list-table' ); ?></label>
                <input type="text" name="wcplt_custom_text" value="<?php echo esc_attr( $custom_text ); ?>" style="width: 100%;" />
            </p>
            <hr>
            <p>
                <label>
                    <input type="checkbox" name="wcplt_override_redirect" value="yes" <?php checked( $override_redirect, 'yes' ); ?> />
                    <?php esc_html_e( 'Override redirect settings', 'wc-product-list-table' ); ?>
                </label>
            </p>
            <p>
                <label>
                    <input type="checkbox" name="wcplt_disable_redirect" value="yes" <?php checked( $disable_redirect, 'yes' ); ?> />
                    <?php esc_html_e( 'Disable redirect for this product', 'wc-product-list-table' ); ?>
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
        if ( ! isset( $_POST['wcplt_product_metabox_nonce'] ) || ! wp_verify_nonce( $_POST['wcplt_product_metabox_nonce'], 'wcplt_product_metabox' ) ) {
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
        $override_text = isset( $_POST['wcplt_override_text'] ) ? 'yes' : 'no';
        update_post_meta( $post_id, '_wcplt_override_text', $override_text );

        // Save custom text
        if ( isset( $_POST['wcplt_custom_text'] ) ) {
            update_post_meta( $post_id, '_wcplt_custom_text', sanitize_text_field( $_POST['wcplt_custom_text'] ) );
        }

        // Save override redirect
        $override_redirect = isset( $_POST['wcplt_override_redirect'] ) ? 'yes' : 'no';
        update_post_meta( $post_id, '_wcplt_override_redirect', $override_redirect );

        // Save disable redirect
        $disable_redirect = isset( $_POST['wcplt_disable_redirect'] ) ? 'yes' : 'no';
        update_post_meta( $post_id, '_wcplt_disable_redirect', $disable_redirect );
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
