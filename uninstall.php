<?php
/**
 * Fired when the plugin is uninstalled.
 *
 * @package   Barn2\woocommerce-product-tabs
 * @author    Barn2 Plugins <support@barn2.com>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 */

// If uninstall not called from WordPress, then exit.
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

$settings = get_option( 'wpt_options', [] );

if ( ! isset( $settings['delete_data'] ) || ! $settings['delete_data'] ) {
	return;
}

foreach ( $settings as $option ) {
	delete_option( $option );
}

// Delete all the tabs
$tabs = get_posts( array(
    'post_type'      => 'woo_product_tab',
    'posts_per_page' => -1,
    'post_status'    => 'any',
) );

foreach ( $tabs as $tab ) {
    wp_delete_post( $tab->ID, true );
}

// Delete all the custom tabs including content and icon
global $wpdb;

// Get all WooCommerce products
$product_ids = $wpdb->get_col( "
    SELECT ID FROM {$wpdb->posts}
    WHERE post_type = 'product'
" );
foreach ( $product_ids as $product_id ) {
    // Delete meta keys containing '_wpt_override_', '_wpt_field_', '_wpt_icon_'
    $meta_keys = array(
        '_wpt_override_',
        '_wpt_field_',
        '_wpt_icon_'
    );

    foreach ( $meta_keys as $key ) {
        $wpdb->query( $wpdb->prepare(
            "DELETE FROM {$wpdb->postmeta}
             WHERE post_id = %d
             AND meta_key LIKE %s",
             $product_id, '%' . $wpdb->esc_like($key) . '%'
        ) );
    }
}
