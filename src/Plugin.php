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
class Plugin extends Simple_Plugin implements Registerable, Service_Provider {

	const NAME    = 'WooCommerce Product Tabs Free';
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
				'settings_path'      => 'admin.php?page=wc-product-tabs',
				'documentation_path' => 'kb/product-tabs-free-documentation/?utm_source=settings&utm_medium=settings&utm_campaign=settingsinline&utm_content=dlw-settings'
			]
		);

		$this->add_service( 'plugin_setup', new Plugin_Setup( $this->get_file(), $this ), true );
	}

	/**
	 * {@inheritdoc}
	 */
	public function register() {
		parent::register();

		add_action( 'plugins_loaded', [ $this, 'add_services' ] );

		add_action( 'init', [ $this, 'register_services' ] );
		add_action( 'init', [ $this, 'load_textdomain' ], 5 );
	}

	/**
	 * Load the plugin.
	 */
	public function maybe_load_plugin() {
		// Don't load plugin if Pro version active
		if ( function_exists( '\Barn2\Plugin\WC_Product_Tabs_Pro\wta' ) ) {
			return;
		}
	}

	public function add_services() {
		$this->add_service( 'wizard', new Setup_Wizard( $this ) );
		$this->add_service( 'post_type', new Post_Type() );
		$this->add_service( 'scripts', new Frontend_Scripts( $this ) );
		$this->add_service( 'review_notice', new Review_Notice( $this ) );

		// Admin only services
		if ( Util::is_admin() ) {
			$this->add_service( 'admin', new Admin\Admin_Controller( $this ) );
		}

	}

	/**
	 * Load the textdomain.
	 */
	public function load_textdomain() {
		load_plugin_textdomain( 'woocommerce-product-tabs', false, $this->get_slug() . '/languages' );
	}

}