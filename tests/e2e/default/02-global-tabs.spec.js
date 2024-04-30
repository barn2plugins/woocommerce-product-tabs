/**
 * External dependencies
 */
import {
	test,
	expect
} from '@wordpress/e2e-test-utils-playwright';

test.describe('global tabs', (props) => {
	test('a tab should be displayed globally', async ({
		page,
		admin,
	}, testInfo) => {
    await admin.visitAdminPage( 'edit.php?post_type=woo_product_tab' );

    // Delete old tabs if we have them
    if( await page.locator( "#bulk-action-selector-top" ).isVisible() ) {
      await page.locator( "#cb-select-all-1" ).check();
      await page.locator( "#bulk-action-selector-top" ).selectOption( {value: 'trash'} );
      await page.locator( "#doaction" ).click();
    }
		await admin.visitAdminPage( 'post-new.php?post_type=woo_product_tab' );
    await page.getByLabel( 'Add title' ).fill( 'First Tab' );
    await page.frameLocator('iframe#content_ifr').locator('#tinymce').fill( 'Global content for the first tab.' );
    await page.getByRole('button', { name: 'Publish', exact: true }).click();

    await page.goto( '/product/shoes' );
    let tabTitle = page.getByRole( 'link', {name: 'First Tab'} );
    await expect( tabTitle ).toBeVisible();
    await tabTitle.click();
    await expect( page.locator( '.woocommerce-Tabs-panel.wc-tab' ) ).toHaveText( /Global content for the first tab./i );
    
    await page.goto( '/product/jacket/' );
    tabTitle = page.getByRole( 'link', {name: 'First Tab'} );
    await expect( tabTitle ).toBeVisible();
    await tabTitle.click();
    await expect( page.locator( '.woocommerce-Tabs-panel.wc-tab' ) ).toHaveText( /Global content for the first tab./i );
    
  });

  test('a tab should be displayed on a category', async ({
		page,
		admin,
	}, testInfo) => {
    await admin.visitAdminPage( 'post-new.php?post_type=woo_product_tab' );
    await page.getByLabel( 'Add title' ).fill( 'Second Tab' );
    await page.frameLocator('iframe#content_ifr').locator('#tinymce').fill( 'Global content for the second tab.' );
    await page.getByLabel( /Show on specific categories/i ).click();

    // Search for a category that doesn't exist to see the error
    await page.getByPlaceholder( /Search for categories/i ).pressSequentially( 'Wrong category' );
    await page.waitForTimeout( 4000 );
    await expect( page.locator( '.wta-component-no-results' ) ).toBeVisible();

    await page.getByPlaceholder( /Search for categories/i ).pressSequentially( 'Shirts' );
    await page.waitForTimeout( 4000 );
    await page.locator( '.barn2-search-list__item-input' ).click();
    await page.getByRole( 'button', {name: 'Publish'} ).click();

    await page.goto( '/product/blouse/' );
    let tabTitle = page.getByRole( 'link', {name: 'First Tab'} );
    await expect( tabTitle ).toBeVisible();
    await tabTitle.click();
    await expect( page.locator( '.woocommerce-Tabs-panel.wc-tab' ) ).toHaveText( /Global content for the first tab./i );
    
    await page.goto( '/product/sweater' );
    tabTitle = page.getByRole( 'link', {name: 'First Tab'} );
    await expect( tabTitle ).not.toBeVisible();

	});
});
