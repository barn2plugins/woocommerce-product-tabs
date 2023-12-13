<?php

namespace Barn2\Plugin\WC_Product_Tabs_Free\Admin;

use Barn2\Plugin\WC_Product_Tabs_Free\Dependencies\Lib\Registerable,
Barn2\Plugin\WC_Product_Tabs_Free\Dependencies\Lib\Service;

/**
 * Add metaboxes and handles their behavior for the singled edit tab page
 *
 * @package   Barn2/woocommerce-product-tabs
 * @author    Barn2 Plugins <info@barn2.com>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 */
class Product_Tabs implements Registerable, Service {

  /**
   * List of the tabs related to the current product
   */
  private $product_tabs_list;

  public function __construct() {
		$this->product_tabs_list = get_posts(
			[
				'post_type'      => 'woo_product_tab',
				'posts_per_page' => -1,
				'orderby'        => 'menu_order',
				'order'          => 'asc',
			]
		);
		if ( ! empty( $this->product_tabs_list ) ) {
			foreach ( $this->product_tabs_list as $key => $t ) {
				$this->product_tabs_list[ $key ]->post_meta = get_post_meta( $this->product_tabs_list[ $key ]->ID );
			}
		}
  }

  public function register() {
    add_filter( 'woocommerce_product_data_tabs', [ $this, 'product_data_tab' ], 99, 1 );
		add_action( 'woocommerce_product_data_panels', [ $this, 'product_data_fields' ] );
		add_action( 'save_post', [ $this, 'save_product_tab_data' ] );
		add_filter( 'wp_insert_post_data', [ $this, 'insert_tab_menu_order' ], 99, 2 );
		add_action( 'admin_head', [ $this, 'post_type_menu_active' ] );
  }
  /**
	 * Add Product Tabs in Product Page.
	 *
	 * @since 1.0.0
	 */
	function product_data_tab( $product_data_tabs ) {
		$product_data_tabs['product-tab'] = [
			'label'  => __( 'Product Tabs', 'woocommerce-product-tabs' ),
			'target' => 'product_tabs',
		];
		return $product_data_tabs;
	}

	/**
	 * View product tabs in product page.
	 *
	 * @since 1.0.0
	 */
	function product_data_fields() {

		include(WP_PLUGIN_DIR . '/woocommerce-product-tabs/templates/product-tab-html.php' );

	}

	/**
	 *  Save product tabs data form product page.
	 *
	 * @since 1.0.0
	 */
	function save_product_tab_data( $post_id ) {

		if ( ! isset( $_POST['_wpt_product_data_nonce'] ) ) {
			return;
		}
		// Verify that the nonce is valid.
		if ( ! wp_verify_nonce( $_POST['_wpt_product_data_nonce'], 'wpt_product_data' ) ) {
			return;
		}

		if ( ( get_post_type( $post_id ) == 'product' ) ) {

			$tabs = $this->product_tabs_list;

			if ( ! empty( $tabs ) ) {
				foreach ( $_POST as $key => $p ) {
					$str = substr( $key, 0, 11 );
					if ( ( '_wpt_field_' === $str ) ) {
						$tab_id = intval( substr( $key, 15 ) );
						// Update the meta field in the database.
						$override_value = $_POST[ '_wpt_override_wpt-' . $tab_id ] ?? 'no';
						update_post_meta( $post_id, '_wpt_override_wpt-' . $tab_id, $override_value );
						if( $override_value === 'yes' ) {
							// Update the tab content
							if ( empty( $p ) ) {
								delete_post_meta( $post_id, $key );
							} else {
								update_post_meta( $post_id, $key, wp_kses_post( $p ) );	
							}

							// Update the tab icon 
							if( empty( $_POST[ '_wpt_icon_wpt-' . $tab_id ] ) ) {
								delete_post_meta( $post_id, '_wpt_icon_wpt-' . $tab_id );
							} else {
								update_post_meta( $post_id, '_wpt_icon_wpt-' . $tab_id, $_POST[ '_wpt_icon_wpt-' . $tab_id ] );
							}
						} else {
							// If the checkbox is not enabled, replace the content and icon with the default
							delete_post_meta( $post_id, $key );
							delete_post_meta( $post_id, '_wpt_icon_wpt-' . $tab_id );
						}	
					}
				}
			}
		}

	}

	function insert_tab_menu_order( $data, $postarr ) {
		if ( $data['post_type'] == 'woo_product_tab' && $data['post_status'] == 'auto-draft' ) {
			global $wpdb;
			if ( $wpdb->get_var( "SELECT menu_order FROM {$wpdb->posts} WHERE post_type='woo_product_tab'" ) ) {
				$data['menu_order'] = $wpdb->get_var( "SELECT MAX(menu_order)+1 AS menu_order FROM {$wpdb->posts} WHERE post_type='woo_product_tab'" );
			}
		}
		return $data;
	}

	/**
	 * Add active in menu product tabs
	 *
	 * @since 1.0.0
	 */
	function post_type_menu_active() {
		$screen = get_current_screen();
		if ( $screen->post_type === 'woo_product_tab' ) {
			?>
		<script type="text/javascript">
			jQuery(document).ready(function(){
			jQuery('ul.wp-submenu li a[href*="edit.php?post_type=woo_product_tab"]').parent().addClass('current');
			});
		</script>
			<?php
		}
	}


}
