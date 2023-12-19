<?php

namespace Barn2\Plugin\WC_Product_Tabs_Free\Admin\Wizard;

use Barn2\Plugin\WC_Product_Tabs_Free\Dependencies\Setup_Wizard\Starter as Setup_Wizard_Starter;

/**
 * Setup Wizard Starter
 *
 * @package   Barn2/woocommerce-product-tabs
 * @author    Barn2 Plugins <info@barn2.com>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 */
class Starter extends Setup_Wizard_Starter {

  /**
	 * Determine if the conditions to start the wizard are met.
	 *
	 * @return boolean
	 */
	public function should_start() {
		$setup_happened = get_option( 'woocommerce-product-tabs-setup-wizard_completed' ) ?: false;
		return ! $setup_happened;
	}
}
