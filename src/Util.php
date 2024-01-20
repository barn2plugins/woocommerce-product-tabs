<?php
namespace Barn2\Plugin\WC_Product_Tabs_Free;

/**
 * Utility functions for WooCommerce Product Tabs.
 *
 * @package   Barn2\woocommerce-product-tabs
 * @author    Barn2 Plugins <support@barn2.com>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 */
final class Util {

  public static function is_tab_global( $tab_id ) {
    // In the older versions of the plugin, the _wpt_display_tab_globally meta doesn't exist 
    if( ! metadata_exists( 'post', $tab_id, '_wpt_display_tab_globally' ) ) {
      if( get_post_meta( $tab_id, '_wpt_conditions_category', true ) ) {
        return 'no';
      } else {
        return 'yes';
      }
    } else {
      return get_post_meta( $tab_id, '_wpt_display_tab_globally', true );
    }
  }
}