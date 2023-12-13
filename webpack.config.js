const defaultConfig = require( '@wordpress/scripts/config/webpack.config' );
const Barn2Configuration = require( '@barn2media/webpack-config' );

const config = new Barn2Configuration(
	[
		'admin/settings.js',
		'admin/wizard.js',
		'admin/product.js'
	],
	[
		'admin/tab.scss',
	],
	defaultConfig
);

module.exports = config.getWebpackConfig();