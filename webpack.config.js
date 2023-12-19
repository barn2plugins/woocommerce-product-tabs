const defaultConfig = require( '@wordpress/scripts/config/webpack.config' );
const Barn2Configuration = require( '@barn2plugins/webpack-config' );

const config = new Barn2Configuration(
	[
		'admin/settings.js',
		'admin/wizard/wizard.js',
		'admin/product.js'
	],
	[
		'admin/tab.scss',
		'admin/wizard.scss'
	],
	defaultConfig
);

module.exports = config.getWebpackConfig();