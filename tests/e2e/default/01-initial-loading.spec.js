/**
 * External dependencies
 */
import {
	test,
	expect
} from '@wordpress/e2e-test-utils-playwright';

test.describe('initial loading', (props) => {
	test('should have the "wpwrap" DOM element', async ({
		page,
		admin,
	}, testInfo) => {
		await admin.visitAdminPage('index.php');

		const wpwrap = await page.$('#wpwrap');

		expect(wpwrap).toBeTruthy();
	});
});
