<?php
/**
 * @package   Barn2\woocommerce-product-tabs
 * @author    Barn2 Plugins <support@barn2.com>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 *
 * @wordpress-plugin
 * Plugin Name: WooCommerce Product Tabs (Free)
 * Plugin URI: https://barn2.com/wordpress-plugins/woocommerce-product-tabs/
 * Description: Boost your product pages by adding custom tabs containing extra information.
 * Version: 2.1.0
 * Author: Barn2 Plugins
 * Author URI: https://barn2.com
 * License: GPL-2.0+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain: woocommerce-product-tabs
 * Domain Path: /languages
 * Requires PHP: 7.4
 * Requires at least: 6.0
 * Tested up to: 6.4
 * WC requires at least: 6.5
 * WC tested up to: 8.4.0
 *
 * @package Woocommerce_Product_Tabs
 */

namespace Barn2\Plugin\WC_Product_Tabs_Free;

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

// Prevent direct file access
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

const PLUGIN_VERSION = '2.1.0';
const PLUGIN_FILE    = __FILE__;

// Autoloader.
require_once __DIR__ . '/vendor/autoload.php';

/**
 * Helper function to access the shared plugin instance.
 *
 * @return Plugin
 */
function woocommerce_product_tabs() {
	return Plugin_Factory::create( PLUGIN_FILE, PLUGIN_VERSION );
}

woocommerce_product_tabs()->register();