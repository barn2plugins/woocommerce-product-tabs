<?php
use Barn2\Plugin\WC_Product_Tabs_Free\Util;

defined( 'ABSPATH' ) || exit;
?>
<div id="product_tabs" class="panel woocommerce_options_panel">
	<?php
	$post_id  = get_the_ID();
	$cat_list = wp_get_post_terms( $post_id, 'product_cat', [ 'fields' => 'ids' ] );

	$required_tabs = $this->product_tabs_list;
	if ( ! empty( $required_tabs ) ) {
		echo '<div class="tab-content-wrap">';
		foreach ( $required_tabs as $key => $tab ) {

			if ( defined( 'ICL_SITEPRESS_VERSION' ) ) {
				$lang     = ICL_LANGUAGE_CODE;
				$tab_lang = wpml_get_language_information( '', $tab->ID );
			}
			$show = true;
			if ( 'yes' === $tab->_wpt_display_tab_globally ) {
				$show = true;
			} else {
				if ( empty( $tab->_wpt_conditions_category ) ) {
					$show = true;
				} else {
					if ( ! empty( $tab->_wpt_conditions_category ) && is_array( $tab->_wpt_conditions_category ) && array_intersect( $cat_list, Util::get_all_categories( $tab->_wpt_conditions_category ) ) ) {
						$show = true;
					} else {
						$show = false;
					}
				}
			}

			if ( $show === false ) {
				unset( $tab );
			} elseif ( defined( 'ICL_SITEPRESS_VERSION' ) && strlen( $tab_lang['language_code'] ) > 0 && $lang !== $tab_lang['language_code'] ) {
				unset( $tab );
			} else {

				echo '<h4 class="wpt_accordion">' . esc_html( $tab->post_title ) . '</h4>';
				$tab_value = get_post_meta( $post_id, '_wpt_field_' . $tab->post_name, true );

				if ( empty( $tab_value ) ) {
					$tab_value = $tab->post_content;
				}

				$settings = [
					'textarea_name' => '_wpt_field_' . $tab->post_name,
					'editor_height' => '150px',
					'editor_class'  => 'test-class'
				];
				echo '<div class="tab-container hidden">';

				$override_value = Util::is_tab_overridden( $tab->post_name, $post_id ) ? 'yes' : 'no';

				// Checking this option would enable the content
				$args = array(
					'label'         => __( 'Override the default tab content for this product', 'woocommerce-product-tabs' ),
					'id'            => '_wpt_override_' . $tab->post_name,
					'name'          => '_wpt_override_' . $tab->post_name,
					'class'         => 'override-tab-content',
					'wrapper_class' => 'override-tab-content-label',
					'value'         => $override_value,
				);
				woocommerce_wp_checkbox( $args );

				wp_editor( $tab_value, '_wpt_field_' . esc_attr( $tab->post_name ), $settings );
				echo '<div class="edit-tab-product edit-tab-footer">';
				echo '<a class="edit-global-tab" target="_blank" href="' . get_edit_post_link( $tab->ID ) . '"><span class="dashicons dashicons-edit"></span> ' . __( 'Manage global tab', 'woocommerce-product-tabs' ) . '</a>';
				echo '</div></div><br />';
			}
		}
		echo '</div>';

	}
	?>

	<input type="hidden" name="count" value="0" id="count">
	<?php wp_nonce_field( 'wpt_product_data', '_wpt_product_data_nonce' ); ?>
	<div class="tabs-layout hidden">
		<?php
		woocommerce_wp_text_input(
			[
				'label'       => '',
				'id'          => 'hidden_duplicate_title',
				'placeholder' => 'Title',
				'class'       => 'tab_title_field'
			]
		);

		woocommerce_wp_textarea_input(
			[
				'label' => '',
				'id'    => 'hidden_duplicate_content',
				'class' => 'tabs_content_field'
			]
		);

		echo '<div class="tab-divider"></div>';
		?>
	</div>

</div>
