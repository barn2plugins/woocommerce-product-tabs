import { addFilter } from '@wordpress/hooks';
import ContentStep from './tab_content';


addFilter('barn2_setup_wizard_steps', 'woocommerce-product-tabs-setup-wizard', (steps) => {
	steps[1].component = ContentStep
	return steps;
});
