<?php

namespace Barn2\Plugin\WC_Product_Tabs_Free\Admin;

use Barn2\Plugin\WC_Product_Tabs_Free\Dependencies\Lib\Registerable;
use Barn2\Plugin\WC_Product_Tabs_Free\Dependencies\Lib\Service\Standard_Service;
use Barn2\Plugin\WC_Product_Tabs_Free\Util;

/**
 * Add metaboxes and handles their behavior for the singled edit tab page
 *
 * @package   Barn2/woocommerce-product-tabs
 * @author    Barn2 Plugins <info@barn2.com>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 */
class Product_Editor_Tabs implements Registerable, Standard_Service {

	private $plugin_dir_path;

	/**
	 * List of the tabs related to the current product
	 */
	private $product_tabs_list;

	public function __construct( $dir_path ) {
		$this->plugin_dir_path = $dir_path;
	}

	public function register() {
		add_filter( 'woocommerce_product_data_tabs', [ $this, 'product_data_tab' ], 99, 1 );
		add_action( 'woocommerce_product_data_panels', [ $this, 'product_data_fields' ] );
		add_action( 'save_post', [ $this, 'save_product_tab_data' ] );
		add_filter( 'wp_insert_post_data', [ $this, 'insert_tab_menu_order' ], 99, 2 );
		add_action( 'admin_head', [ $this, 'post_type_menu_active' ] );
		add_action( 'save_post_product', [ $this, 'make_fields_translatable' ] );
		add_action( 'admin_init', [ $this, 'make_all_fields_translatable' ] );
		add_action( 'admin_notices', [ $this, 'show_notice_for_fields' ] );
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
		$this->product_tabs_list = $this->get_product_tabs_list();
		include_once $this->plugin_dir_path . 'templates/product-tab-html.php';
	}

	/**
	 *  Save product tabs data form product page.
	 *
	 * @since 1.0.0
	 */
	function save_product_tab_data( $post_id ) {
		$nonce = filter_input( INPUT_POST, '_wpt_product_data_nonce', FILTER_SANITIZE_SPECIAL_CHARS );

		// Verify that the nonce is valid.
		if ( empty( $nonce ) || ! wp_verify_nonce( $nonce, 'wpt_product_data' ) ) {
			return;
		}

		if ( 'product' !== filter_input( INPUT_POST, 'post_type', FILTER_SANITIZE_SPECIAL_CHARS ) ) {
			return;
		}

		if ( empty( $this->get_product_tabs_list() ) ) {
			return;
		}

		$posted_tab_data = array_filter(
			$_POST,
			function ( $key ) {
				return '_wpt_field_' === substr( $key, 0, 11 );
			},
			ARRAY_FILTER_USE_KEY
		);

		foreach ( $posted_tab_data as $post_key => $tab_content ) {
			$tab_slug       = substr( $post_key, 11 );
			$override_value = filter_input( INPUT_POST, '_wpt_override_' . $tab_slug, FILTER_SANITIZE_SPECIAL_CHARS );

			if ( 'yes' !== $override_value ) {
				$override_value = 'no';
			}

			update_post_meta( $post_id, '_wpt_override_' . $tab_slug, $override_value );

			if ( 'yes' === $override_value ) {
				// Update the tab content.
				update_post_meta( $post_id, $post_key, wp_kses( $tab_content, $this->get_allowed_tags() ) );
			} else {
				// If the checkbox is not enabled, delete the tab content post meta.
				delete_post_meta( $post_id, $post_key, '' );
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
				jQuery( document ).ready( function() {
					jQuery( 'ul.wp-submenu li a[href*="edit.php?post_type=woo_product_tab"]' ).parent().addClass( 'current' );
				} );
			</script>
			<?php
		}
	}

	public function get_allowed_tags() {
		$allowed_tags           = wp_kses_allowed_html( 'post' );
		$allowed_tags['iframe'] = [
			'src'             => true,
			'height'          => true,
			'width'           => true,
			'title'           => true,
			'allow'           => true,
			'frameborder'     => true,
			'allowfullscreen' => true,
		];

		return apply_filters( 'wt_allowed_kses_tags', $allowed_tags );
	}

	public function get_product_tabs_list() {
		$product_tabs_list = get_posts(
			[
				'post_type'      => 'woo_product_tab',
				'posts_per_page' => -1,
				'orderby'        => 'menu_order',
				'order'          => 'asc',
			]
		);
		if ( ! empty( $product_tabs_list ) ) {
			foreach ( $product_tabs_list as $key => $t ) {
				$product_tabs_list[ $key ]->post_meta = get_post_meta( $product_tabs_list[ $key ]->ID );
			}
		}

		return $product_tabs_list;
	}

	/**
	 * Since the custom tab meta key is generated dynamically, we need to make them translatable every time user saves a product.
	 */
	public function make_fields_translatable() {
		// Ensure this is not an auto-save or a revision.
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}

		if ( wp_is_post_revision( $post_id ) ) {
			return;
		}

		if ( ! Util::is_wpml_active() ) {
			return;
		}

		try {
			$tm                  = \wpml_load_core_tm();
			$postMetaPreferences = $tm->settings['custom_fields_translation'] ?? [];
			$postMetaKeys        = get_post_meta( $post_id );
			$needsPersist        = false;

			foreach ( $postMetaKeys as $key => $value ) {
				if (
					0 === strpos( $key, '_wpt_field_' )
					&& ( ! isset( $postMetaPreferences[ $key ] ) || WPML_TRANSLATE_CUSTOM_FIELD !== $postMetaPreferences[ $key ] )
				) {
					$postMetaPreferences[ $key ] = WPML_TRANSLATE_CUSTOM_FIELD;
					$needsPersist                = true;
				}
			}

			if ( $needsPersist ) {
				$tm->settings['custom_fields_translation'] = $postMetaPreferences;
				$tm->save_settings();
			}
		} catch ( \Throwable $e ) {
			error_log( $e->getMessage() );
		}
	}

	public function make_all_fields_translatable() {
		if ( isset( $_GET['run_wpml_translation'] ) && '1' === $_GET['run_wpml_translation'] ) {
			if ( ! isset( $_GET['_wpnonce'] ) || ! wp_verify_nonce( $_GET['_wpnonce'], 'run_wpml_translation_nonce' ) ) {
				wp_die( 'Invalid request. Please try again.' );
			}

			if ( ! Util::is_wpml_active() ) {
				wp_safe_redirect( admin_url() );
			}

			try {
				// Query all WooCommerce products.
				$args     = [
					'post_type'      => 'product',
					'posts_per_page' => -1,
					'fields'         => 'ids',
				];
				$products = get_posts( $args );

				if ( empty( $products ) ) {
					return;
				}

				// Loop through each product.
				foreach ( $products as $product_id ) {
					$postMetaKeys = get_post_meta( $product_id );

					$hasWptField = false;

					// Check for `_wpt_field_` custom fields.
					foreach ( $postMetaKeys as $key => $value ) {
						if ( 0 === strpos( $key, '_wpt_field_' ) ) {
							$hasWptField = true;
							break;
						}
					}

					// If the product has `_wpt_field_` custom fields, save it.
					if ( $hasWptField ) {
						wp_update_post(
							[
								'ID' => $product_id,
							]
						);
					}
				}

				wp_safe_redirect( add_query_arg( 'run_wpml_translation_done', '1', admin_url() ) );
				exit;
			} catch ( \Throwable $e ) {
				echo $e->getMessage();
				exit;
			}
		}

		// Display a success message if the task was completed.
		if ( isset( $_GET['run_wpml_translation_done'] ) && '1' === $_GET['run_wpml_translation_done'] ) {
			add_action(
				'admin_notices',
				function () {
					echo '<div class="notice notice-success is-dismissible">
					<p>' . __( 'Your custom fields are translatable now!', 'woocommerce-product-tabs' ) . '</p>
				</div>';
				}
			);
		}
	}

	public function show_notice_for_fields() {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		// Bail if we have done this before
		if ( get_option( 'wc_product_tabs_made_fields_translatable' ) ) {
			return;
		}

		if ( ! Util::is_wpml_active() ) {
			return;
		}

		$url = add_query_arg(
			[
				'run_wpml_translation' => '1',
				'_wpnonce'             => wp_create_nonce( 'run_wpml_translation_nonce' ),
			],
			admin_url()
		);

		echo '<div class="notice notice-info is-dismissible">
			<p>' . __( 'If you have used WPML to translate the tab custom content fields, please click on the button below to make them translatable in the Translation Editor.', 'woocommerce-product-tabs' ) . '</p>
			<p><a href="' . esc_url( $url ) . '" class="button button-primary">Run Now</a></p>
		</div>';
	}
}
