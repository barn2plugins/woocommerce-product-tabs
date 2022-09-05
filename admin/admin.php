<?php
/**
 * Admin functions
 *
 * @package Woocommerce_Product_Tabs
 */

/**
 * Render admin page.
 *
 * @since 1.0.0
 */
function wpt_render_admin_page() {
	?>
	<div class="wrap">
		<h1><?php echo esc_html( get_admin_page_title() ); ?></h1>

		<div id="poststuff">

			<div id="post-body" class="metabox-holder columns-2">

				<div id="post-body-content">

					<div class="tab-wrapper">
						<ul class="tabs-nav">
							<li class="tab-active"><a href="#tab-free-vs-pro" class="button">Free vs Pro</a></li>
							<li><a href="#tab-features" class="button">Features</a></li>
							<li><a href="#tab-support" class="button">Support</a></li>
						</ul>
					</div><!-- .tab-wrapper -->

					<div class="tabs-stage">

						<div id="tab-free-vs-pro" class="meta-box-sortables ui-sortable">
							<div class="postbox">
								<div class="inside inside-content">
									<img src="<?php echo plugin_dir_url( __DIR__ ) . 'admin/images/free-vs-pro.png'; ?>" alt="" />
									<a href="<?php echo esc_url( WPT_UPGRADE_URL ); ?>" id="purchase" class="button button-primary" target="_blank">Upgrade to Pro</a>
								</div><!-- .inside -->
							</div><!-- .postbox -->
						</div><!-- .meta-box-sortables -->

						<div id="tab-features" class="meta-box-sortables ui-sortable active">
							<div class="postbox">
								<div class="inside inside-content">
									<h4>Key features</h4>
									<ul>
									<li>Create multiple tabs; no limit in number of tabs</li>
									<li>Supports shortcodes and embedded codes</li>
									<li>Setup common tab for all products</li>
									<li>Enable tab based on product category</li>
									<li>Change order of the tabs</li>
									</ul>
								</div><!-- .inside -->
							</div><!-- .postbox -->
						</div><!-- .meta-box-sortables -->

						<div id="tab-support" class="meta-box-sortables ui-sortable">
							<div class="postbox">
								<div class="inside inside-content">
									<h3><span>Need Support?</span></h3>
									<div class="inside">
										<a href="https://wordpress.org/support/plugin/woocommerce-product-tabs/" target="_blank">Go to Support Forum</a>
									</div><!-- .inside -->

									<h3><span>Have any queries?</span></h3>
									<div class="inside">
										<p>If you have any queries or feedback, please feel free to send us an email to <code>support@wpconcern.com</code></p>
									</div><!-- .inside -->
								</div><!-- .inside -->
							</div><!-- .postbox -->
						</div><!-- .meta-box-sortables -->


					</div><!-- .tabs-stage -->

				</div><!-- #post-body-content -->

				<div id="postbox-container-1" class="postbox-container">

					<div class="meta-box-sortables">
						<div class="postbox">

							<h3><span>Upgrade to Pro</span></h3>
							<div class="inside">
								<p>Buy pro plugin unlock more awesome features.</p>
								<a href="<?php echo esc_url( WPT_UPGRADE_URL ); ?>" id="purchase" class="button button-primary" target="_blank">Buy Pro Plugin</a>
							</div> <!-- .inside -->

						</div><!-- .postbox -->
					</div><!-- .meta-box-sortables -->

					<div class="meta-box-sortables">
						<div class="postbox">

							<h3><span>Important Links</span></h3>
							<div class="inside">
								<ol>
								<li><a href="https://wpconcern.com/documentation/woocommerce-product-tabs/" target="_blank">Documentation</a></li>
								<li><a href="https://wpconcern.com/request-customization/" target="_blank">Customization Request</a></li>
								<li><a href="https://wordpress.org/plugins/woocommerce-product-tabs/#reviews" target="_blank">Submit a Review</a></li>
								</ol>
							</div> <!-- .inside -->

						</div><!-- .postbox -->
					</div><!-- .meta-box-sortables -->

					<div class="meta-box-sortables">

						<div class="postbox">

							<h3><span>Recommended Plugins</span></h3>
							<div class="wpc-plugins-list inside"></div>

						</div><!-- .postbox -->
					</div><!-- .meta-box-sortables -->

				</div><!-- #postbox-container-1 .postbox-container -->

			</div><!-- #post-body -->
		</div><!-- #poststuff -->

	</div><!-- .wrap -->
	<?php
}

/**
 * Register menu page.
 *
 * @since 1.0.0
 */
function wpt_register_menu() {
	add_submenu_page( 'edit.php?post_type=woo_product_tab', esc_html__( 'WooCommerce Product Tabs', 'woocommerce-product-tabs' ), esc_html__( 'Getting Started', 'woocommerce-product-tabs' ), 'manage_options', 'wpt-welcome', 'wpt_render_admin_page' );
}

add_action( 'admin_menu', 'wpt_register_menu' );

/**
 * Load admin assets.
 *
 * @since 1.0.0
 *
 * @param string $hook Hook name.
 */
function wpt_load_admin_scripts( $hook ) {
	if ( 'woo_product_tab_page_wpt-welcome' === $hook ) {
		wp_enqueue_style( 'wpt-admin-style', plugins_url( 'admin/css/admin.css', dirname( __FILE__ ) ), array(), WOOCOMMERCE_PRODUCT_TABS_VERSION );
		wp_enqueue_script( 'wpt-admin-script', plugins_url( 'admin/js/admin.js', dirname( __FILE__ ) ), array( 'jquery' ), WOOCOMMERCE_PRODUCT_TABS_VERSION, true );
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
	$transient_key = 'wpc_plugins_list';

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

add_action( 'wp_ajax_nopriv_wpc_get_plugins_list', 'wpt_get_list_ajax_callback' );
add_action( 'wp_ajax_wpc_get_plugins_list', 'wpt_get_list_ajax_callback' );

/**
 * Add admin notice.
 *
 * @since 2.0.14
 */
function wpt_add_admin_notice() {
	// Setup notice.
	\Nilambar\AdminNotice\Notice::init(
		array(
			'slug' => WOOCOMMERCE_PRODUCT_TABS_SLUG,
			'name' => esc_html__( 'WooCommerce Product Tabs', 'woocommerce-product-tabs' ),
		)
	);
}

add_action( 'admin_init', 'wpt_add_admin_notice' );
