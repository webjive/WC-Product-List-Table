<?php
/**
 * Uninstall Script
 *
 * Fired when the plugin is uninstalled.
 */

// If uninstall not called from WordPress, exit
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
    exit;
}

// Delete plugin options
$options = array(
    'ccb_enable_custom_text',
    'ccb_shop_button_text',
    'ccb_single_button_text',
    'ccb_variable_button_text',
    'ccb_grouped_button_text',
    'ccb_external_button_text',
    'ccb_out_of_stock_text',
    'ccb_enable_redirect',
    'ccb_redirect_simple',
    'ccb_redirect_variable',
    'ccb_redirect_grouped',
    'ccb_redirect_new_tab',
    'ccb_enable_styling',
    'ccb_bg_color',
    'ccb_text_color',
    'ccb_hover_bg_color',
    'ccb_hover_text_color',
    'ccb_border_radius',
    'ccb_padding',
    'ccb_custom_css',
);

foreach ( $options as $option ) {
    delete_option( $option );
}

// Delete product meta
global $wpdb;

$wpdb->query(
    "DELETE FROM {$wpdb->postmeta} 
    WHERE meta_key IN (
        '_ccb_override_text',
        '_ccb_custom_text',
        '_ccb_override_redirect',
        '_ccb_disable_redirect'
    )"
);
