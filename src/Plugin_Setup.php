<?php

namespace Barn2\Plugin\WC_Product_Tabs_Free;

use Barn2\Plugin\WC_Product_Tabs_Free\Admin\Wizard\Starter,
	Barn2\Plugin\WC_Product_Tabs_Free\Dependencies\Lib\Plugin\Plugin,
	Barn2\Plugin\WC_Product_Tabs_Free\Dependencies\Lib\Plugin\Plugin_Activation_Listener,
	Barn2\Plugin\WC_Product_Tabs_Free\Dependencies\Lib\Util as Lib_Util,
	Barn2\Plugin\WC_Product_Tabs_Free\Dependencies\Lib\Registerable;

/**
 * Plugin Setup
 *
 * @package   Barn2/woocommerce-product-tabs
 * @author    Barn2 Plugins <info@barn2.com>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 */
class Plugin_Setup implements Registerable {
	/**
	 * Plugin's entry file
	 *
	 * @var string
	 */
	private $file;

	/**
	 * Plugin instance
	 *
	 * @var Plugin
	 */
	private $plugin;

	/**
	 * Wizard starter.
	 *
	 * @var Starter
	 */
	private $starter;

	/**
	 * Constructor.
	 *
	 * @param mixed $file
	 * @param Plugin $plugin
	 */
	public function __construct( $file, Plugin $plugin ) {
		$this->file    = $file;
		$this->plugin  = $plugin;
		$this->starter = new Starter( $this->plugin );
	}

	/**
	 * Register the service.
	 */
	public function register() {
		register_activation_hook( $this->file, [ $this, 'on_activate' ] );
		add_action( 'admin_init', [ $this, 'after_plugin_activation' ] );
	}

	/**
	 * On activation.
	 *
	 * @param mixed $network_wide
	 */
	public function on_activate( $network_wide ) {
    /**
     * Determine if setup wizard should run.
     */
    if ( $this->starter->should_start() ) {
      $this->starter->create_transient();
    }

	}

	/**
	 * Do nothing.
	 *
	 * @param bool $network_wide
	 */
	public function on_deactivate( $network_wide ) {
	}

	/**
	 * Detect the transient and redirect to wizard.
	 *
	 * @return void
	 */
	public function after_plugin_activation() {

		if ( ! $this->starter->detected() ) {
			return;
		}

		$this->starter->delete_transient();
		$this->starter->create_option(); 
		$this->starter->redirect();
	}
}
