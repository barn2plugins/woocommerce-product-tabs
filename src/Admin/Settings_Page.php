<?php

namespace Barn2\Plugin\WC_Product_Tabs_Free\Admin;

use Barn2\Plugin\WC_Product_Tabs_Free\Dependencies\Lib\Conditional;
use Barn2\Plugin\WC_Product_Tabs_Free\Dependencies\Lib\Plugin\Plugin;
use Barn2\Plugin\WC_Product_Tabs_Free\Dependencies\Lib\Registerable;
use Barn2\Plugin\WC_Product_Tabs_Free\Dependencies\Lib\Service;
use Barn2\Plugin\WC_Product_Tabs_Free\Dependencies\Lib\Util;

/**
 * The settings page.
 *
 * @package   Barn2\woocommerce-product-tabs
 * @author    Barn2 Plugins <support@barn2.com>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 */
class Settings_Page implements Service, Registerable, Conditional {

	/**
	 * Plugin handling the page.
	 *
	 * @var Plugin
	 */
	public $plugin;

	/**
	 * List of settings.
	 *
	 * @var array
	 */
	public $registered_settings = [];

	/**
	 * Constructor.
	 *
	 * @param Plugin $plugin
	 */
	public function __construct( Plugin $plugin ) {
		$this->plugin              = $plugin;
		// $this->registered_settings = $this->get_settings_tabs();
	}

	/**
	 * {@inheritdoc}
	 */
	public function is_required() {
		return Util::is_admin();
	}

	/**
	 * {@inheritdoc}
	 */
	public function register() {
		$this->register_settings_tabs();
	}

	/**
	 * Retrieves the settings tab classes.
	 *
	 * @return array
	 */
	private function get_settings_tabs(): array {
		$settings_tabs = [
			Settings_Tab\Product_Tabs::TAB_ID => new Settings_Tab\Product_Tabs( $this->plugin ),
			Settings_Tab\Settings::TAB_ID         => new Settings_Tab\Settings( $this->plugin ),
		];

		return $settings_tabs;
	}

	/**
	 * Register the settings tab classes.
	 *
	 * @return void
	 */
	private function register_settings_tabs(): void {
		array_map(
			function( $setting_tab ) {
				if ( $setting_tab instanceof Registerable ) {
					$setting_tab->register();
				}
			},
			$this->registered_settings
		);
	}

	/**
	 * Render the Settings page.
	 */
	public function render_settings_page(): void {
		$active_tab = filter_input( INPUT_GET, 'tab', FILTER_SANITIZE_FULL_SPECIAL_CHARS ) ?? 'product_tabs';

		?>
		<div class='woocommerce-layout__header'>
			<div class="woocommerce-layout__header-wrapper">
				<h3 class='woocommerce-layout__header-heading'>
					<?php esc_html_e( 'Product Tabs', 'woocommerce-product-tabs' ); ?>
				</h3>
				<div class="links-area">
					<?php $this->support_links(); ?>
				</div>
			</div>
		</div>

		<div class="wrap barn2-settings">

			<?php do_action( 'barn2_before_plugin_settings', $this->plugin->get_id() ); ?>

			<div class="barn2-settings-inner">

				<h2 class="nav-tab-wrapper">
					<?php
					foreach ( $this->registered_settings as $setting_tab ) {
						$active_class = $active_tab === $setting_tab::TAB_ID ? ' nav-tab-active' : '';
						?>
							<a href="<?php echo esc_url( add_query_arg( 'tab', $setting_tab::TAB_ID, $this->plugin->get_settings_page_url() ) ); ?>" class="<?php echo esc_attr( sprintf( 'nav-tab%s', $active_class ) ); ?>">
								<?php echo esc_html( $setting_tab->get_title() ); ?>
							</a>
							<?php
					}
					?>
				</h2>

				<h1></h1>

				<div class="barn2-inside-wrapper">
					<?php if ( $active_tab === 'product_tabs' ) : ?>
						<?php echo $this->registered_settings[ $active_tab ]->output(); //phpcs:ignore ?>
					<?php else : ?>
						<h2>
							<?php esc_html_e( 'General', 'woocommerce-product-tabs' ); ?>
						</h2>
						<p>
							<?php esc_html_e( 'The following options control the WooCommerce Product Options extension.', 'woocommerce-product-tabs' ); ?>
						</p>

					<?php endif; ?>
				</div>

			</div>

			<?php do_action( 'barn2_after_plugin_settings', $this->plugin->get_id() ); ?>
		</div>
		<?php
	}

	/**
	 * Output the Barn2 Support Links.
	 */
	public function support_links(): void {
		printf(
			'<p>%s | %s | %s</p>',
            // phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped
			Util::format_link( $this->plugin->get_documentation_url(), __( 'Documentation', 'woocommerce-product-tabs' ), true ),
			Util::format_link( $this->plugin->get_support_url(), __( 'Support', 'woocommerce-product-tabs' ), true ),
			sprintf(
				'<a class="barn2-wiz-restart-btn" href="%s">%s</a>',
				add_query_arg( [ 'page' => $this->plugin->get_slug() . '-setup-wizard' ], admin_url( 'admin.php' ) ),
				__( 'Setup wizard', 'woocommerce-product-tabs' )
			)
            // phpcs:enable
		);
	}

	/**
	 * Product Tab setting lists.
	 *
	 * @since 2.0.2
	 *
	 * @return array Add custom settings for product tabs.
	 */
	function wpt_get_settings() {
		$settings = array(
			'tab_section' => array(
				'name'      => esc_html__( 'Product Tab Settings', 'woocommerce-product-tabs' ),
				'type'      => 'title',
				'id'        => 'wpt_tab_section',
			),
			'wpt_disable_content_filter'  => array(
				'name'    => esc_html__( 'Disable the_content Filter', 'woocommerce-product-tabs' ),
				'type'    => 'checkbox',
				'desc'    => esc_html__( 'Enable this checkbox if you are using a page builder and have problems with the content preview.', 'woocommerce-product-tabs' ),
				'default' => 'no',
				'class'   => 'wpt_disable_content_filter',
				'id'      => 'wpt_disable_content_filter',
			),
			'tab_section_end' => array(
				'type'          => 'sectionend',
				'id'            => 'wpt_tab_section_end',
			),
		);

		return $settings;
	}
}
