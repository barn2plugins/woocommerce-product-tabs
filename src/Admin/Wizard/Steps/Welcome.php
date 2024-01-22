<?php

namespace Barn2\Plugin\WC_Product_Tabs_Free\Admin\Wizard\Steps;

use Barn2\Plugin\WC_Product_Tabs_Free\Dependencies\Setup_Wizard\Steps\Welcome_Free;

/**
 * Welcome Step.
 *
 * @package   Barn2/woocommerce-product-tabs
 * @author    Barn2 Plugins <info@barn2.com>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 */
class Welcome extends Welcome_Free {

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->set_id( 'welcome_free' );
        $this->set_title( 'Welcome to WooCommerce Product Tabs' );
        $this->set_name( esc_html__( 'Welcome', 'woocommerce-product-tabs' ) );
        $this->set_tooltip( FALSE );
        $this->set_description( esc_html__( 'Create custom tabs in no time.', 'document-library-lite' ) );
    }

    /**
     * {@inheritdoc}
     */
    public function setup_fields()
    {
        $fields = array(
            'welcome_messages' => array(
                'type'  => 'heading',
                'raw'   => TRUE,
                'label' => esc_html__( 'Use this setup wizard to create your first tab. After that, you’ll be ready to manage your tabs and create more!
        ', 'woocommerce-product-tabs' ),
                'size'  => 'p',
                'style' => array(
                    'textAlign' => 'center',
                    'color'     => '#757575'
                )
            )
        );

        return $fields;
    }
}
