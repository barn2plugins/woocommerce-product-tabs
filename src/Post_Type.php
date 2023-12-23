<?php

namespace Barn2\Plugin\WC_Product_Tabs_Free;

use Barn2\Plugin\WC_Product_Tabs_Free\Dependencies\Lib\Registerable,
Barn2\Plugin\WC_Product_Tabs_Free\Dependencies\Lib\Service;

/**
 * Registering the post type
 *
 * @package   Barn2/woocommerce-product-tabs
 * @author    Barn2 Plugins <info@barn2.com>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 */
class Post_Type implements Registerable, Service {

  public function register() {
    add_action( 'init', [ $this, 'tab_post_type' ], 99 );
    add_action( 'admin_head-post.php', [ $this, 'hide_publishing_actions' ] );
    add_action( 'admin_head-post-new.php', [ $this, 'hide_publishing_actions' ] );
    add_filter( 'manage_woo_product_tab_posts_columns', [ $this, 'add_columns_in_tab_listing' ] );
		add_action( 'manage_woo_product_tab_posts_custom_column', [ $this, 'custom_columns_in_tab_listing' ], 10, 2  );
	  add_filter( 'post_updated_messages', [ $this, 'tab_post_updated_messages' ], 10, 2 );
		add_filter( 'post_row_actions', [ $this, 'tab_post_row_actions' ], 10, 2 );
    add_filter( 'parent_file', [ $this, 'highlight_menu_item' ], 99 );
		add_filter( 'custom_menu_order', '__return_true', 99 );
		add_filter( 'menu_order', [ $this, 'tabs_menu_order' ] );
		add_filter('use_block_editor_for_post_type', [ $this, 'disable_gutenberg_editor' ], 20, 2 );
  }

  public function tab_post_type(){

		$labels = array(
      'name'               => _x( 'Product Tabs', 'post type general name', 'woocommerce-product-tabs' ),
      'singular_name'      => _x( 'Tab', 'post type singular name', 'woocommerce-product-tabs' ),
      'menu_name'          => _x( 'WooCommerce Product Tabs', 'admin menu', 'woocommerce-product-tabs' ),
      'name_admin_bar'     => _x( 'Tab', 'add new on admin bar', 'woocommerce-product-tabs' ),
      'add_new'            => _x( 'Add New', 'woocommerce-product-tabs' ),
      'add_new_item'       => __( 'Add New Tab', 'woocommerce-product-tabs' ),
      'new_item'           => __( 'New Tab', 'woocommerce-product-tabs' ),
      'edit_item'          => __( 'Edit Tab', 'woocommerce-product-tabs' ),
      'view_item'          => __( 'View Tab', 'woocommerce-product-tabs' ),
      'all_items'          => __( 'Product Tabs', 'woocommerce-product-tabs' ),
      'search_items'       => __( 'Search Tabs', 'woocommerce-product-tabs' ),
      'parent_item_colon'  => __( 'Parent Tabs:', 'woocommerce-product-tabs' ),
      'not_found'          => __( 'No tabs found.', 'woocommerce-product-tabs' ),
      'not_found_in_trash' => __( 'No tabs found in Trash.', 'woocommerce-product-tabs' )
    );

    $args = array(
      'labels'             => $labels,
      'public'             	=> FALSE,
      'publicly_queryable' 	=> FALSE,
      'show_ui'            	=> TRUE,
      'query_var'          	=> FALSE,
      'capability_type'    => 'post',
      'has_archive'        => FALSE,
      'hierarchical'       => FALSE,
			'show_in_rest'			 => true,
			'rest_base'					 => 'woo_product_tab',
			'rest_controller_class'	=>	'WP_REST_Posts_Controller',
      'show_in_menu'       => 'edit.php?post_type=product',
      'taxonomies'         => array(),
      'supports'           => array( 'title', 'editor' ),
    );

    register_post_type( 'woo_product_tab', $args );
    add_filter( 'views_edit-post', '__return_null' );
	}

  /**
	 * Hide publishing actions.
	 *
	 * @since 1.0.0
	 */
	function hide_publishing_actions() {
		global $post;
		if ( 'woo_product_tab' !== $post->post_type ) {
				return;
		}
		?>
		<style type="text/css">
		#misc-publishing-actions,#minor-publishing-actions{
				display:none;
		}
		</style>
		<?php
		return;
	}

  public function add_columns_in_tab_listing( $columns ){

		unset($columns['date']);
		$columns['display-globally'] = __('Display globally','woocommerce-product-tabs');
		$columns['tab-key']         = __('Tab Key','woocommerce-product-tabs');

		return $columns;
	}

	public function custom_columns_in_tab_listing( $column, $post_id ){

		$post = get_post($post_id);
		switch ( $column ) {
			case 'priority':
				echo $post->menu_order;
				break;
			case 'tab-key':
				echo '<code>'.$post->post_name.'</code>';
				break;
			case 'display-globally':
				$flag_default_for_all = get_post_meta( $post_id,'_wpt_display_tab_globally', true );
				$tab_categories = get_post_meta( $post_id, '_wpt_conditions_category', true );
				if ( 'no' === $flag_default_for_all && $tab_categories ) {
					echo '<span class="dashicons dashicons-no-alt"></span>';
				}
				else{
					echo '<span class="dashicons dashicons-yes"></span>';
				}
				break;
			default:
			break;
		}

	}

  public function tab_post_updated_messages( $messages ){

		$post = get_post();

		$messages['woo_product_tab'] = array(

			0 => '', // Unused. Messages start at index 1.
			1 => __( 'Tab updated.', 'woocommerce-product-tabs' ),
			2 => __( 'Custom field updated.', 'woocommerce-product-tabs' ),
			3 => __( 'Custom field deleted.', 'woocommerce-product-tabs' ),
			4 => __( 'Tab updated.', 'woocommerce-product-tabs' ),
			5 => isset( $_GET['revision'] ) ? sprintf( __( 'Tab restored to revision from %s', 'woocommerce-product-tabs' ), wp_post_revision_title( (int) $_GET['revision'], false ) ) : false,
			6 => __( 'Tab published.', 'woocommerce-product-tabs' ),
			7 => __( 'Tab saved.', 'woocommerce-product-tabs' ),
			8 => __( 'Tab submitted.', 'woocommerce-product-tabs' ),
			9  => sprintf(
						__( 'Tab scheduled for: <strong>%1$s</strong>.', 'woocommerce-product-tabs' ),
						date_i18n( __( 'M j, Y @ G:i', 'woocommerce-product-tabs' ), strtotime( $post->post_date ) )
					),
			10 => __( 'Tab draft updated.', 'woocommerce-product-tabs' )
			);
		return $messages;

	}

  public function tab_post_row_actions( $actions, $post ){
		if ( 'woo_product_tab' == $post->post_type && isset( $actions['inline hide-if-no-js'] ) ){
			unset( $actions['inline hide-if-no-js'] );
		}
		return $actions;

	}

  public function highlight_menu_item( $file ) {
		global $plugin_page, $submenu_file;

		if ( $plugin_page === 'wta_settings' ) {
			$plugin_page = 'edit.php?post_type=product';
			$submenu_file = 'edit.php?post_type=woo_product_tab';
		}
		return $file;
	}

	public function tabs_menu_order( $menu_order ) {
		global $submenu;
		
		if( $submenu[ 'edit.php?post_type=product' ] ) {
			$index = 0;
			foreach( $submenu[ 'edit.php?post_type=product' ] as $i => $item ) {
				if( $item[2] === 'edit.php?post_type=woo_product_tab' ) {
					$index = $i;
					break;
				}
			}
			if( $index ) {
				$temp = $submenu[ 'edit.php?post_type=product' ][$index];
				unset( $submenu[ 'edit.php?post_type=product' ][$index] );
				$submenu[ 'edit.php?post_type=product' ][] = $temp;
			}
		}

		return $menu_order;
	}

	public function disable_gutenberg_editor( $is_enabled, $post_type ) {
		if ( 'woo_product_tab' === $post_type ) {
			return false;
		}
		return $is_enabled;
	}
}
