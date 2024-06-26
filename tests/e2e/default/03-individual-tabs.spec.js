/**
 * External dependencies
 */
import {
	test,
	expect
} from '../fixtures';

test.describe('individual tabs', (props) => {
	test('an individual tab should have the right content', async ({
		page,
		admin,
    pluginUtil
	}, testInfo) => {
    await admin.visitAdminPage( 'edit.php?post_type=woo_product_tab' );

    // Delete old tabs if we have them
    if( await page.locator( "#bulk-action-selector-top" ).isVisible() ) {
      await page.locator( "#cb-select-all-1" ).check();
      await page.locator( "#bulk-action-selector-top" ).selectOption( {value: 'trash'} );
      await page.locator( "#doaction" ).click();
    }

    // Create the global tab
		await admin.visitAdminPage( 'post-new.php?post_type=woo_product_tab' );
    await page.getByLabel( 'Add title' ).fill( 'First Tab' );
    await page.frameLocator('iframe#content_ifr').locator('#tinymce').fill( 'Global content for the first tab.' );
    await page.getByRole('button', { name: 'Publish', exact: true }).click();

    // Edit the tab content for one product
    await admin.visitAdminPage( "edit.php?post_type=product" );
    await page.getByRole( 'link', {name: 'Shoes', exact: true} ).click();
    await page.waitForLoadState( 'domcontentloaded' );
    await page.locator( ".product-tab_tab" ).click();
    await page.getByRole( "heading", {name: "First Tab"} ).click();
    await page.locator( "input.override-tab-content" ).check();
    await page.frameLocator('iframe[id*=_wpt_field_wpt]').locator('#tinymce').fill( 'Individual content for the first tab.' );
    await page.getByRole('button', { name: 'Update', exact: true }).click();

    // check if the individual content is showing in the product we edited
    await page.goto( '/product/shoes' );
    expect( await pluginUtil.isProductTabVisible( 'First Tab', 'Individual content for the first tab.' ) ).toBe( true );
        
    // Other products should have the global content
    await page.goto( '/product/jacket/' );
    expect( await pluginUtil.isProductTabVisible( 'First Tab', 'Global content for the first tab.' ) ).toBe( true );

  });

  test('correct tabs should be shown in the product edit page', async ({
		page,
		admin,
	}, testInfo) => {
    // Create a tab that is displayed only in the Shirts catgeory
    await admin.visitAdminPage( 'post-new.php?post_type=woo_product_tab' );
    await page.getByLabel( 'Add title' ).fill( 'Second Tab' );
    await page.frameLocator('iframe#content_ifr').locator('#tinymce').fill( 'Global content for the second tab.' );
    await page.getByLabel( /Show on specific categories/i ).click();
    await page.getByPlaceholder( /Search for categories/i ).pressSequentially( 'Shirts' );
    await page.waitForTimeout( 4000 );
    await page.locator( '.barn2-search-list__item-input' ).click();
    await page.getByRole('button', { name: 'Publish', exact: true }).click();

    // Check if the tab is showing in the product edit screen
    await admin.visitAdminPage( "edit.php?post_type=product" );
    await page.getByRole( 'link', {name: 'Blouse', exact: true} ).click();
    await page.waitForLoadState( 'domcontentloaded' );
    await page.locator( ".product-tab_tab" ).click();
    await expect( page.getByRole( "heading", {name: "Second Tab"} ) ).toBeVisible();

    // Produts in other categories should not have this tab
    await admin.visitAdminPage( "edit.php?post_type=product" );
    await page.getByRole( 'link', {name: 'Shoes', exact: true} ).click();
    await page.waitForLoadState( 'domcontentloaded' );
    await page.locator( ".product-tab_tab" ).click();
    await expect( page.getByRole( "heading", {name: "Second Tab"} ) ).not.toBeVisible();

	});
});
