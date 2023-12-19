<?php

namespace Barn2\Plugin\WC_Product_Tabs_Free\Admin\Wizard\Steps;

use Barn2\Plugin\WC_Product_Tabs_Free\Dependencies\Setup_Wizard\Steps\Cross_Selling;

/**
 * Upsell Step.
 *
 * @package   Barn2/woocommerce-product-tabs
 * @author    Barn2 Plugins <info@barn2.com>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 */
class Upsell extends Cross_Selling {
	/**
	 * Constructor.
	 */
	public function __construct()
	{
		parent::__construct();
		$this->set_name( esc_html__( 'Extra Features', 'woocommerce-product-tabs' ) );
		$this->set_title( esc_html__( 'Extra features', 'woocommerce-product-tabs' ) );
		$this->set_description( __( 'Enhance your store with these fantastic plugins from Barn2.', 'woocommerce-product-tabs' ) );
	}
}
