<?php
namespace Barn2\Plugin\WC_Product_Tabs_Free;

/**
 * Factory to create/return the shared plugin instance.
 *
 * @package   Barn2\woocommerce-product-tabs
 * @author    Barn2 Plugins <support@barn2.com>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 */
class Plugin_Factory {

	private static $plugin = null;

	/**
	 * Return the shared instance of the plugin.
	 *
	 * @param  string $file
	 * @param  float $version
	 * @return Plugin
	 */
	public static function create( $file, $version )
	{
		if ( null === self::$plugin ) {
			self::$plugin = new Plugin( $file, $version );
		}
		return self::$plugin;
	}

}