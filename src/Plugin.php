<?php

namespace Barn2\Plugin\WC_Product_Tabs_Free;

use Barn2\Plugin\WC_Product_Tabs_Free\Dependencies\Lib\Plugin\Simple_Plugin;
use Barn2\Plugin\WC_Product_Tabs_Free\Dependencies\Lib\Registerable;
use Barn2\Plugin\WC_Product_Tabs_Free\Dependencies\Lib\Service;
use Barn2\Plugin\WC_Product_Tabs_Free\Dependencies\Lib\Service_Provider;
use Barn2\Plugin\WC_Product_Tabs_Free\Dependencies\Lib\Util;
use Barn2\Plugin\WC_Product_Tabs_Free\Admin\Wizard\Setup_Wizard;

/**
 * The main plugin class.
 *
 * @package   Barn2\woocommerce-product-tabs
 * @author    Barn2 Plugins <support@barn2.com>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 */
class Plugin extends Simple_Plugin {

	const NAME    = 'Product Tabs for WooCommerce Free';
	const ITEM_ID = 559044;

	/**
	 * Services array.
	 *
	 * @var array $services
	 */
	private $services;

	/**
	 * Constructs and initializes the WooCommerce Product Tabs plugin instance.
	 *
	 * @param string $file    The main plugin __FILE__
	 * @param string $version The current plugin version
	 */
	public function __construct( $file = null, $version = '1.0' ) {
		parent::__construct(
			[
				'id'                 => self::ITEM_ID,
				'name'               => self::NAME,
				'version'            => $version,
				'file'               => $file,
				'is_woocommerce'		 => true,
				'settings_path'      => 'edit.php?post_type=woo_product_tab',
				'documentation_path' => 'kb/woocommerce-product-tabs-free-documentation/?utm_source=settings&utm_medium=settings&utm_campaign=settingsinline&utm_content=wta-settings'
			]
		);
	}

	/**
	 * Load the plugin.
	 */
	public function maybe_load_plugin() {
		// Don't load plugin if Pro version active
		if ( ! function_exists( '\\Barn2\\Plugin\\WC_Product_Tabs_Pro\\wta' ) ) {
			add_action('after_setup_theme', [$this, 'start_standard_services']);
		}
	}

	public function add_services() {
		$this->add_service( 'plugin_setup', new Plugin_Setup( $this->get_file(), $this ), true );
		$this->add_service( 'wizard', new Setup_Wizard( $this ) );
		$this->add_service( 'post_type', new Post_Type() );
		$this->add_service( 'admin', new Admin\Admin_Controller( $this ) );
		$this->add_service( 'product_tabs', new Product_Tabs() );
	}
}
