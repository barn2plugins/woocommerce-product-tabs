<?php

namespace Barn2\Plugin\WC_Product_Tabs_Free\Admin\Wizard;

use Barn2\Plugin\WC_Product_Tabs_Free\Admin\Wizard\Steps;
use Barn2\Plugin\WC_Product_Tabs_Free\Dependencies\Lib\Plugin\Plugin;
use Barn2\Plugin\WC_Product_Tabs_Free\Dependencies\Lib\Registerable;
use Barn2\Plugin\WC_Product_Tabs_Free\Dependencies\Lib\Service\Standard_Service;
use Barn2\Plugin\WC_Product_Tabs_Free\Dependencies\Lib\Util as Lib_Util;
use Barn2\Plugin\WC_Product_Tabs_Free\Dependencies\Setup_Wizard\Setup_Wizard as Wizard;

/**
 * Main Setup Wizard Loader
 *
 * @package   Barn2/document-library-lite
 * @author    Barn2 Plugins <info@barn2.com>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 */
class Setup_Wizard implements Registerable, Standard_Service {

	private $plugin;
	private $wizard;

	/**
	 * Constructor.
	 *
	 * @param Plugin $plugin
	 */
	public function __construct( Plugin $plugin ) {

		$this->plugin = $plugin;

		$steps = [
			new Steps\Welcome(),
			new Steps\Tab_Content(),
			new Steps\Upsell(),
			new Steps\Completed(),
		];

		$wizard = new Wizard( $this->plugin, $steps, false );

		$wizard->configure(
			[
				'skip_url'    => admin_url( 'edit.php?post_type=woo_product_tab' ),
				'premium_url' => 'https://barn2.com/wordpress-plugins/woocommerce-product-tabs/',
				'utm_id'      => 'wtaf',
				'signpost'    => [
					[
						'title' => 'Manage tabs',
						'href'  => admin_url( 'edit.php?post_type=woo_product_tab' ),
					],
					[
						'title' => 'Create a new tab',
						'href'  => admin_url( 'post-new.php?post_type=woo_product_tab' ),
					],
					[
						'title' => 'Tab settings',
						'href'  => admin_url( 'admin.php?page=wta_settings' ),
					],
				],
			]
		);

		$wizard->add_custom_asset(
			$plugin->get_dir_url() . 'assets/js/admin/wizard/wizard.js',
			Lib_Util::get_script_dependencies( $this->plugin, 'admin/wizard/wizard.js' )
		);

		$wizard->add_restart_link( '', '' );

		$this->wizard = $wizard;
	}

	/**
	 * {@inheritdoc}
	 */
	public function register() {
		$this->wizard->boot();
	}
}
