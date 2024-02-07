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

  /**
   * Checks the tab against the old version to see if it's global or not.
   * 
   * @param int $tab_id
   * 
   * @return string 
   */
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

  /**
   * Combines the list of the categories with their child categories
   * @param array $conditions_categories
   * 
   * @return array
   */
  public static function get_all_categories( $conditions_categories ) {

    if( ! $conditions_categories || ! is_array( $conditions_categories ) ) {
      return [];
    }

    if( is_array( $conditions_categories ) && empty( $conditions_categories ) ) {
      return [];
    }

    $child_categories = [];
    foreach ( $conditions_categories as $category ) {
      $child_terms = get_terms( [
        'child_of'   => $category,
        'hide_empty' => true,
        'taxonomy'   => 'product_cat',
        'fields'     => 'ids'
      ] );

      if ( is_array( $child_terms ) ) {
        $child_categories = array_unique( array_merge( $child_categories, $child_terms ) );
      }
    }
    return array_unique( array_merge( $conditions_categories, $child_categories ) );
  }

  /**
   * Determines if a tab has a custom content
   * @param string $tab_key
   * @param int $product_id
   * 
   * @return boolean
   */
  public static function is_tab_overridden( $tab_key, $product_id ) {
    
    if( ! $tab_key || ! $product_id ) {
      return false;
    }

    $override_meta = get_post_meta( $product_id, '_wpt_override_' . $tab_key, true );

		// The _wpt_override key doesn't exist in the older version of the plugin and the best way
		// to check it, is to check for the _wpt_field_ meta for the product
		if ( empty( $override_meta ) && get_post_meta( $product_id, '_wpt_field_' . $tab_key, true ) ) {
			$override_meta = 'yes';
		}

		return 'yes' === $override_meta;
  }
  
}
