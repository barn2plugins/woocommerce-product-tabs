<?php

namespace Barn2\Plugin\WC_Product_Tabs_Free\Admin;

use Barn2\Plugin\WC_Product_Tabs_Free\Dependencies\Lib\Registerable;
use Barn2\Plugin\WC_Product_Tabs_Free\Dependencies\Lib\Service\Standard_Service;
use Barn2\Plugin\WC_Product_Tabs_Free\Util;

/**
 * Add metaboxes and handles their behavior for the singled edit tab page
 *
 * @package   Barn2/woocommerce-product-tabs
 * @author    Barn2 Plugins <info@barn2.com>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 */
class Single_Tab implements Registerable, Standard_Service {

    public function register()
    {
        add_action( 'add_meta_boxes', [ $this, 'add_tab_meta_boxes' ] );
        add_action( 'save_post', [ $this, 'save_visibility_condition' ] );
        add_action( 'save_post', [ $this, 'save_category_selector' ] );
        add_action( 'save_post', [ $this, 'save_tab_priority' ] );
    }

    /**
     * Categories selector.
     */
    public function wta_inclusion_categories_selector( $post_id, $times_svg_icon )
    {
        $wpt_conditions_category = get_post_meta( $post_id, '_wpt_conditions_category', TRUE );
        $selected_categories     = $this->get_selected_terms( $wpt_conditions_category, 'product_cat' );
        ?>
        <div class="wta-categories-selector wta-inclusion-selector">
            <div class="wta-component-search-field">
                <input data-type="category" type="text" data-taxonomy="categories" id="wta-category-search" class="wta-component-search-field-control" placeholder="<?php _e( 'Search for categories', 'woocommerce-product-tabs' ) ?>">
            </div>
            <div class="wta-spinner wta-loader">
                <svg width="18" height="18" viewBox="0 0 38 38" xmlns="http://www.w3.org/2000/svg" stroke="#c3c4c7"> <g fill="none" fillRule="evenodd"> <g transform="translate(1 1)" strokeWidth="2"> <circle strokeOpacity="1" cx="18" cy="18" r="18"/> <path d="M36 18c0-9.94-8.06-18-18-18"> <animateTransform attributeName="transform" type="rotate" from="0 18 18" to="360 18 18" dur="1s" repeatCount="indefinite"/> </path> </g> </g></svg>
            </div>
            <div class="wta-component-no-results">
                <span><?php _e( 'No categories found', 'woocommerce-product-tabs' ); ?></span>
            </div>
            <ul class="barn2-search-list__list">
            </ul>
            <div class="barn2-search-list__selected <?php echo ($selected_categories) ? '' : 'wpt-hide-selected-terms-section'; ?>">
                <div class="barn2-search-list__selected-header">
                    <strong><?php _e( 'Selected categories', 'woocommerce-product-tabs' );?></strong>
                    <?php 
                        printf(
                            '<button type="button" aria-label="%1$s" class="barn2-search-list-clear__all barn2-remove-inclusions">%1$s</button>',
                            __('Clear all selected categories', 'woocommerce-product-tabs' ),
                        );
                    ?>
                </div>
                <ul class="barn2-search-list__selected_terms">
                    <?php
                    if( $selected_categories ) {
                        foreach( $selected_categories as $category ) : ?>
                            <li data-term-id="<?php echo $category->term_id; ?>">
                                <span class="barn2-selected-list__tag">
                                    <?php
                                        printf(
                                            '<span class="barn2-tag__text" id="barn2-tag__label-%s"><span class="screen-reader-text">%s</span><span aria-hidden="true">%s</span></span>',
                                            $category->term_id,
                                            $category->name,
                                            $category->name
                                        );
                                    ?>
                                    <input type="hidden" name="wpt_category_list[]" value="<?php echo $category->term_id; ?>">
                                    
                                    <?php 
                                        printf(
                                            '<button type="button" aria-describedby="barn2-tag__label-%s" class="components-button barn2-tag__remove" id="barn2-remove-term" aria-label="%s">',
                                            $category->term_id,
                                            $category->name
                                        );
                                        echo $times_svg_icon; 
                                        echo '</button>';
                                    ?>
                                </span>
                            </li>
                        <?php endforeach;
                    }
                    ?>
                </ul>
            </div>
        </div>
    <div class="wta-component-search-field disabled">
            <input disabled type="text" class="wta-component-search-field-control" placeholder="<?php _e( 'Search for products', 'woocommerce-product-tabs' ) ?>">
      <a class="pro-version-link" target="_blank" href="https://barn2.com/wordpress-plugins/woocommerce-product-tabs/?utm_source=settings&utm_medium=settings&utm_campaign=settingsinline&amp;utm_content=wta-settings">
          <?php _e( 'Pro version only', 'woocommerce-product-tabs' ); ?>
      </a>
    </div>
    
    <div class="wta-component-search-field disabled">
            <input disabled type="text" class="wta-component-search-field-control" placeholder="<?php _e( 'Search for tags', 'woocommerce-product-tabs' ) ?>">
      <a class="pro-version-link" target="_blank" href="https://barn2.com/wordpress-plugins/woocommerce-product-tabs/?utm_source=settings&utm_medium=settings&utm_campaign=settingsinline&amp;utm_content=wta-settings">
        <?php _e( 'Pro version only', 'woocommerce-product-tabs' ); ?>
      </a>
    </div>
        <?php
    }

    /**
     * Get inclusion section selected taxonomy terms
     */
    public function get_selected_terms( $terms_ids, $taxonomy )
    {
        if ( empty( $terms_ids) ) {
            return;
        }

        $term_args  = [
            'taxonomy'        => $taxonomy,
            'hide_empty'    => FALSE,
            'fields'            => 'all',
            'count'                => TRUE,
            'include'            => $terms_ids
        ];
        $term_query = new \WP_Term_Query( $term_args );

        return $term_query->terms;
    }

    /**
     *  Save post category tab.
     *
     * @since 1.0.0
     */
    public function save_category_selector( $post_id )
    {
        // Check if our nonce is set.
        if ( ! isset( $_POST['wpt_meta_box_tab_nonce'] ) ) {
            return;
        }
            // Verify that the nonce is valid.
        if ( ! wp_verify_nonce( $_POST['wpt_meta_box_tab_nonce'], 'wpt_tab_meta_box' ) ) {
            return;
        }
        
        // If this is an autosave, our form has not been submitted, so we don't want to do anything.
        if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
            return;
        }

        if ( 'woo_product_tab' != $_POST['post_type'] ) {
            return;
        }

        $wpt_conditions_category = '';
        if ( isset( $_POST['wpt_category_list'] ) && ! empty( $_POST['wpt_category_list'] ) ) {
            $wpt_conditions_category = $_POST['wpt_category_list'];
        }
        if ( ! isset( $_POST['wpt_category_list'] ) ) {
            delete_post_meta( $post_id, '_wpt_conditions_category' );
            update_post_meta( $post_id, '_wpt_display_tab_globally', 'yes' );
            return;
        }
        update_post_meta( $post_id, '_wpt_conditions_category', $wpt_conditions_category );

    }

    /**
     *  Save product tabs settings.
     *
     * @since 1.0.0
     */
    public function save_visibility_condition( $post_id )
    {
        // Check if our nonce is set.
        if ( ! isset( $_POST['wpt_meta_box_tab_nonce'] ) ) {
            return;
        }
            // Verify that the nonce is valid.
        if ( ! wp_verify_nonce( $_POST['wpt_meta_box_tab_nonce'], 'wpt_tab_meta_box' ) ) {
            return;
        }
            // If this is an autosave, our form has not been submitted, so we don't want to do anything.
        if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
            return;
        }

        if ( 'woo_product_tab' != $_POST['post_type'] ) {
            return;
        }

            // Show tabs on all products
        $display_globally = '';
        if ( isset( $_POST['_wpt_display_tab_globally'] ) ) {
            $display_globally = $_POST['_wpt_display_tab_globally'];
        }
        if ( ! isset( $_POST['_wpt_display_tab_globally'] ) ) {
            $display_globally = 'no';
        }
        // show each tab on the product screen by default
        update_post_meta( $post_id, '_wpt_option_use_default_for_all', 'no' );
        update_post_meta( $post_id, '_wpt_display_tab_globally', $display_globally );

    }

    public function save_tab_priority( $post_id ) {
        // Check if our nonce is set.
        if ( ! isset( $_POST['wpt_meta_box_tab_nonce'] ) ) {
            return;
        }
            // Verify that the nonce is valid.
        if ( ! wp_verify_nonce( $_POST['wpt_meta_box_tab_nonce'], 'wpt_tab_meta_box' ) ) {
            return;
        }
            // If this is an autosave, our form has not been submitted, so we don't want to do anything.
        if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
            return;
        }

        if ( 'woo_product_tab' != $_POST['post_type'] ) {
            return;
        }
        // priority
		$priority = $_POST['_wpt_option_priority'];
		$priority = absint( $priority );

        global $wpdb;
		$sql = $wpdb->prepare('UPDATE '. $wpdb->posts.' SET `menu_order`=%d WHERE ID=%d',
			$priority,
			$post_id
		);
		$wpdb->query($sql);
    }

    /**
     * Add meta box in product tabs.
     *
     * @since 1.0.0
     */
    public function add_tab_meta_boxes()
    {

        $screens = [ 'woo_product_tab' ];

        foreach ( $screens as $screen ) {
            // Settings Metabox
            add_meta_box(
                'woocommerce-product-tabs_conditions_section',
                __( 'Conditions', 'woocommerce-product-tabs' ),
                [ $this, 'wpt_conditions_section' ],
                $screen,
                'normal',
                'high'
            );
            add_meta_box(
                'woocommerce-product-tabs_icon_section',
                __( 'Select icon', 'woocommerce-product-tabs' ),
                [ $this, 'wpt_icon_section' ],
                $screen,
                'side',
                'high'
            );
            add_meta_box(
                'woocommerce-product-tabs_priority_section',
                __( 'Settings', 'woocommerce-product-tabs' ),
                [ $this, 'wpt_priority_section' ],
                $screen,
                'side',
            );
        }
    }

    public function wpt_conditions_section( $post )
    {
        $post_id = $post->ID;

        // Add an nonce field so we can check for it later.
        wp_nonce_field( 'wpt_tab_meta_box', 'wpt_meta_box_tab_nonce' );
        $is_tab_global  = Util::is_tab_global( $post_id );
        $times_svg_icon = '<svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" width="20" height="20" class="clear-icon" aria-hidden="true" focusable="false"><path d="M12 21C16.9706 21 21 16.9706 21 12C21 7.02944 16.9706 3 12 3C7.02944 3 3 7.02944 3 12C3 16.9706 7.02944 21 12 21ZM15.5303 8.46967C15.8232 8.76256 15.8232 9.23744 15.5303 9.53033L13.0607 12L15.5303 14.4697C15.8232 14.7626 15.8232 15.2374 15.5303 15.5303C15.2374 15.8232 14.7626 15.8232 14.4697 15.5303L12 13.0607L9.53033 15.5303C9.23744 15.8232 8.76256 15.8232 8.46967 15.5303C8.17678 15.2374 8.17678 14.7626 8.46967 14.4697L10.9393 12L8.46967 9.53033C8.17678 9.23744 8.17678 8.76256 8.46967 8.46967C8.76256 8.17678 9.23744 8.17678 9.53033 8.46967L12 10.9393L14.4697 8.46967C14.7626 8.17678 15.2374 8.17678 15.5303 8.46967Z"></path></svg>';
        ?>
            <table class="form-table visibility-form">
                <tbody>
                    <tr>
                        <th><?php _e( 'Visibility', 'woocommerce-product-tabs' ); ?></th>
                        <td>
                            <fieldset>
                                <legend class="screen-reader-text"><span><?php _e( 'Visibility', 'woocommerce-product-tabs' ); ?></span></legend>
                                <label>
                                    <input type="radio" id="_wpt_display_tab_globally" name="_wpt_display_tab_globally" class="wta-visibility_condition" checked="checked" value="yes" <?php checked( 'yes', $is_tab_global, TRUE ); ?>>
                                        <?php _e( 'Display globally on all products', 'woocommerce-product-tabs' ); ?>
                                </label><br>
                                <label>
                                    <input type="radio" id="_wpt_display_tab_globally" name="_wpt_display_tab_globally" class="wta-visibility_condition" value="no" <?php checked( 'no', $is_tab_global, TRUE ); ?>>
                                        <?php _e( 'Show on specific categories', 'woocommerce-product-tabs'); ?>
                                </label><br>
                            </fieldset>
                        </td>
                    </tr>
                </tbody>
            </table>

            <table id="inclusions-list" class="form-table <?php echo ($is_tab_global === 'no') ? '' : 'hide-section'; ?> ">
                <tbody>
                    <tr>
                        <th><?php _e( 'Inclusions', 'woocommerce-product-tabs' ); ?></th>
                        <td class="wta-term-inclusions-section">
                            <?php 
                            $this->wta_inclusion_categories_selector( $post_id, $times_svg_icon );
                            ?>
                        </td>
                    </tr>
                </tbody>
            </table>
        <?php 
    }

    public function wpt_icon_section()
    {
        ?>
        <div class="icon-wrap">
        <a href="#" class="tab_icon disabled button button-secondary"><?php esc_html_e( 'Select Icon', 'woocommerce-product-tabs' ); ?></a>
        <a href="https://barn2.com/wordpress-plugins/woocommerce-product-tabs/?utm_source=settings&utm_medium=settings&utm_campaign=settingsinline&amp;utm_content=wta-settings" class="pro-version-link" target="_blank"><?php _e( 'Pro version only' ); ?></a>
        </div>
        <?php
    }

    public function wpt_priority_section( $post ) {
        $priority = $post->menu_order;
		echo '<p><label for="_wpt_option_priority"><strong>';
        echo __( 'Priority', 'woocommerce-product-tabs' );
		echo '</strong></label></p>';
        echo '<input type="number" name="_wpt_option_priority" id="_wpt_option_priority" value="'.$priority.'" min="0" style="max-width:70px;"/>';
    }
}
