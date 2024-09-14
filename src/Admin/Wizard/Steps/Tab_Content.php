<?php

namespace Barn2\Plugin\WC_Product_Tabs_Free\Admin\Wizard\Steps;

use Barn2\Plugin\WC_Product_Tabs_Free\Dependencies\Setup_Wizard\Api;
use Barn2\Plugin\WC_Product_Tabs_Free\Dependencies\Setup_Wizard\Step;
use Barn2\Plugin\WC_Product_Tabs_Free\Dependencies\Lib\Util as Lib_Util;

/**
 * Layout Settings Step.
 *
 * @package   Barn2/woocommerce-product-tabs
 * @author    Barn2 Plugins <info@barn2.com>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 */
class Tab_Content extends Step {

	/**
	 * The default or user setting
	 *
	 * @var array
	 */
	private $values;

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->set_id( 'tab_content' );
		$this->set_name( esc_html__( 'Tab Content', 'woocommerce-product-tabs' ) );
		$this->set_description( esc_html__( "Enter the title for your tab and the content that you'd like to display inside it.", 'woocommerce-product-tabs' ) );
		$this->set_title( esc_html__( 'Tab content', 'woocommerce-product-tabs' ) );
	}

	/**
	 * {@inheritdoc}
	 */
	public function setup_fields() {

		$fields = [
			'title'   => [
				'label'       => __( 'Title', 'woocommerce-product-tabs' ),
				'description' => __( 'Enter the tab title.', 'woocommerce-product-tabs' ),
				'type'        => 'text',
				'value'       => $this->values['title'] ?? '',
			],
			'content' => [
				'label'       => __( 'Content', 'woocommerce-product-tabs' ),
				'description' => __( 'Enter the tab content', 'woocommerce-product-tabs' ),
				'type'        => 'TinyMCE',
				'value'       => $this->values['content'] ?? '',
			],
		];

		return $fields;
	}

	/**
	 * {@inheritdoc}
	 */
	public function submit( array $values ) {

		return Api::send_success_response();
	}
}
