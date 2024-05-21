/**
 * External dependencies
 */
import {
	test,
	expect
} from '../fixtures';

test.describe('global tabs', (props) => {
	test('a tab should be displayed globally', async ({
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
		await admin.visitAdminPage( 'post-new.php?post_type=woo_product_tab' );
    await page.getByLabel( 'Add title' ).fill( 'First Tab' );
    await page.frameLocator('iframe#content_ifr').locator('#tinymce').fill( 'Global content for the first tab.' );
    await page.getByRole('button', { name: 'Publish', exact: true }).click();

    await page.goto( '/product/shoes' );
    expect( await pluginUtil.isProductTabVisible( 'First Tab', 'Global content for the first tab.' ) ).toBe( true );
   
    await page.goto( '/product/jacket/' );
    expect( await pluginUtil.isProductTabVisible( 'First Tab', "Global content for the first tab." ) ).toBe( true );    
    
  });

  test('a tab should be displayed on a category', async ({
		page,
		admin,
    pluginUtil
	}, testInfo) => {
    await admin.visitAdminPage( 'post-new.php?post_type=woo_product_tab' );
    await page.getByLabel( 'Add title' ).fill( 'Second Tab' );
    await page.frameLocator('iframe#content_ifr').locator('#tinymce').fill( 'Global content for the second tab.' );
    await page.getByLabel( /Show on specific categories/i ).click();

    // Search for a category that doesn't exist to see the error
    await page.getByPlaceholder( /Search for categories/i ).pressSequentially( 'Wrong category' );
    await page.waitForTimeout( 4000 );
    await expect( page.locator( '.wta-component-no-results' ) ).toBeVisible();

    await page.getByPlaceholder( /Search for categories/i ).fill( '' );
    await page.getByPlaceholder( /Search for categories/i ).pressSequentially( 'Shirts' );
    await page.waitForTimeout( 4000 );
    await page.locator( '.barn2-search-list__item-input' ).click();
    await page.getByRole('button', { name: 'Publish', exact: true }).click();

    await page.goto( '/product/blouse/' );
    expect( await pluginUtil.isProductTabVisible( 'Second Tab', 'Global content for the second tab.' ) ).toBe( true );

    await page.goto( '/product/sweater' );
    expect( await pluginUtil.isProductTabVisible( 'Second Tab', 'Second Tab', 'Global content for the second tab.' ) ).toBe( false );

	});
});
