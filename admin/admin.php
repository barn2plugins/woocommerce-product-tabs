<?php
/**
 * Admin functions
 *
 * @package Woocommerce_Product_Tabs
 */

use Nilambar\Welcome\Welcome;

add_action(
	'wp_welcome_init',
	function() {
		$obj = new Welcome( 'plugin', 'woocommerce-product-tabs' );

		$obj->set_page(
			array(
				'page_title'    => esc_html__( 'WooCommerce Product Tabs', 'woocommerce-product-tabs' ),
				'page_subtitle' => sprintf( esc_html__( 'Version: %s', 'woocommerce-product-tabs' ), WOOCOMMERCE_PRODUCT_TABS_VERSION ),
				'menu_title'    => esc_html__( 'Getting Started', 'woocommerce-product-tabs' ),
				'menu_slug'     => 'wpt-welcome',
				'parent_page'   => 'edit.php?post_type=woo_product_tab',
			)
		);

		$obj->set_quick_links(
			array(
				array(
					'text' => 'Plugin Page',
					'url'  => 'https://barn2.com/wordpress-plugins/woocommerce-product-tabs/',
					'type' => 'primary',
				),
				array(
					'text' => 'View Documentation',
					'url'  => 'https://barn2.com/kb/woocommerce-product-tabs-free-documentation/',
					'type' => 'secondary',
				),
			)
		);

		$obj->set_admin_notice(
			array(
				'screens' => array( 'dashboard' ),
			)
		);

		$obj->add_tab(
			array(
				'id'    => 'welcome',
				'title' => 'Welcome',
				'type'  => 'grid',
				'items' => array(
					array(
						'title'       => 'Custom Tabs',
						'icon'        => 'dashicons dashicons-list-view',
						'description' => 'Click button below to start creating custom tabs and assign those to products. You can also create tabs with default content for the product.',
						'button_text' => 'Product Tabs',
						'button_url'  => admin_url( 'edit.php?post_type=woo_product_tab' ),
						'button_type' => 'primary',
						'is_new_tab'  => false,
					),
					array(
						'title'       => 'Having issue with page builders?',
						'icon'        => 'dashicons dashicons-info',
						'description' => 'Click here to fix the_content filter issue if custom tabs are not working with page builders.',
						'button_text' => 'Go to Settings',
						'button_url'  => admin_url( 'admin.php?page=wc-settings&tab=wpt_settings' ),
						'button_type' => 'secondary',
						'is_new_tab'  => false,
					),
					array(
						'title'       => 'Get Support',
						'icon'        => 'dashicons dashicons-editor-help',
						'description' => 'Got plugin support question or found bug or got some feedbacks? Please visit support forum in the WordPress.org directory.',
						'button_text' => 'Visit Support',
						'button_url'  => 'https://wordpress.org/support/plugin/woocommerce-product-tabs/#new-post',
						'button_type' => 'secondary',
						'is_new_tab'  => true,
					),
					array(
						'title'       => 'Customization Request',
						'icon'        => 'dashicons dashicons-admin-generic',
						'description' => 'Feel free to contact us if you need any customization service.',
						'button_text' => 'Customization Request',
						'button_url'  => 'https://barn2.com/kb/plugin-customizations/',
						'button_type' => 'secondary',
						'is_new_tab'  => true,
					),
				),
			)
		);

		$obj->add_tab(
			array(
				'id'             => 'free-vs-pro',
				'title'          => 'Free Vs. Pro',
				'type'           => 'comparison',
				'upgrade_button' => array(
					'url' => WPT_UPGRADE_URL,
				),
				'items'          => array(
					array(
						'title' => 'Add Unlimited Additional Tabs',
						'free'  => 'yes',
						'pro'   => 'yes',
					),
					array(
						'title' => 'Shortcode, HTML, Images & Embedded Code Support',
						'free'  => 'yes',
						'pro'   => 'yes',
					),
					array(
						'title' => 'Set as Default Tab',
						'free'  => 'yes',
						'pro'   => 'yes',
					),
					array(
						'title' => 'Change Priority',
						'free'  => 'yes',
						'pro'   => 'yes',
					),
					array(
						'title' => 'Default Tabs Reorder',
						'free'  => 'no',
						'pro'   => 'yes',
					),
					array(
						'title' => 'Rename Default Tabs',
						'free'  => 'no',
						'pro'   => 'yes',
					),
					array(
						'title'       => 'Reorder All Tabs',
						'description' => 'Drag & Drop',
						'free'        => 'no',
						'pro'         => 'yes',
					),
					array(
						'title'       => 'Add Tab Icon',
						'description' => 'Applies to Default Tabs as well',
						'free'        => 'no',
						'pro'         => 'yes',
					),
					array(
						'title'       => 'Search By Product Tabs',
						'description' => 'Search based on the title and content of the tabs added',
						'free'        => 'no',
						'pro'         => 'yes',
					),
					array(
						'title'       => 'Convert Tabs to Accordion',
						'description' => 'Convert existing tabs to accordion below specific viewport',
						'free'        => 'no',
						'pro'         => 'yes',
					),
					array(
						'title' => 'Hide Default Tabs',
						'free'  => 'no',
						'pro'   => 'yes',
					),
					array(
						'title' => 'Add Tabs from Individual Product Page',
						'free'  => 'no',
						'pro'   => 'yes',
					),
					array(
						'title'       => 'Categories, Tags & Product Based Tabs',
						'description' => 'Enable tabs based on the categories, tags and products',
						'free'        => 'no',
						'pro'         => 'yes',
					),
				),
			)
		);

		$obj->set_sidebar(
			array(
				'render_callback' => 'wpt_render_welcome_page_sidebar',
			)
		);

		$obj->run();
	}
);

/**
 * Render welcome page sidebar.
 *
 * @since 2.0.16
 *
 * @param Welcome $object Instance of Welcome class.
 */
function wpt_render_welcome_page_sidebar( $object ) {
	$object->render_sidebar_box(
		array(
			'title'        => 'Upgrade to Pro',
			'content'      => 'Upgrade to pro version for additional features and options.',
			'class'        => 'gray',
			'button_text'  => 'Upgrade Now',
			'button_url'   => WPT_UPGRADE_URL,
			'button_class' => 'button button-primary button-upgrade',
		),
		$object
	);

	$object->render_sidebar_box(
		array(
			'title'        => 'Leave a Review',
			'content'      => $object->get_stars() . sprintf( 'Are you enjoying %1$s? We would appreciate a review.', $object->get_name() ),
			'button_text'  => 'Submit Review',
			'button_url'   => 'https://wordpress.org/support/plugin/woocommerce-product-tabs/reviews/#new-post',
			'button_class' => 'button',
		),
		$object
	);
}

/**
 * Load admin assets.
 *
 * @since 1.0.0
 *
 * @param string $hook Hook name.
 */
function wpt_load_admin_scripts( $hook ) {
	if ( 'woo_product_tab_page_wpt-welcome' === $hook ) {
		wp_enqueue_script( 'wpt-plugins-list', plugins_url( 'admin/js/plugins-list.js', dirname( __FILE__ ) ), array( 'jquery' ), WOOCOMMERCE_PRODUCT_TABS_VERSION, true );
	}
}

add_action( 'admin_enqueue_scripts', 'wpt_load_admin_scripts' );

/**
 * Return plugins list.
 *
 * @since 1.0.0
 *
 * @return array Plugins list array.
 */
function wpt_get_plugins_list() {
	$transient_key = 'wpt_wpc_plugins_list';

	$transient_period = 21 * DAY_IN_SECONDS;

	$output = get_transient( $transient_key );

	if ( false === $output ) {
		$output = array();

		$request = wp_safe_remote_get( 'https://wpconcern.com/wpc-api/plugins-list' );

		if ( is_wp_error( $request ) ) {
			return $output;
		}

		$body = wp_remote_retrieve_body( $request );
		$json = json_decode( $body, true );

		if ( isset( $json['plugins'] ) && ! empty( $json['plugins'] ) ) {
			$output = $json['plugins'];
		}

		set_transient( $transient_key, $output, $transient_period );
	}

	return $output;
}

function wpt_get_list_ajax_callback() {
	$output = array();

	$posts = wpt_get_plugins_list();

	if ( ! empty( $posts ) ) {
		$output = $posts;
	}

	if ( ! empty( $output ) ) {
		wp_send_json_success( $output, 200 );
	} else {
		wp_send_json_error( $output, 404 );
	}
}

add_action( 'wp_ajax_nopriv_wpt_wpc_get_plugins_list', 'wpt_get_list_ajax_callback' );
add_action( 'wp_ajax_wpt_wpc_get_plugins_list', 'wpt_get_list_ajax_callback' );

/**
 * Add admin notice.
 *
 * @since 2.0.14
 */
function wpt_add_admin_notice() {
	\Nilambar\AdminNotice\Notice::init(
		array(
			'slug' => WOOCOMMERCE_PRODUCT_TABS_SLUG,
			'name' => esc_html__( 'WooCommerce Product Tabs', 'woocommerce-product-tabs' ),
		)
	);
}

add_action( 'admin_init', 'wpt_add_admin_notice' );
