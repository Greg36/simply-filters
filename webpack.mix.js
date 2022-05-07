/**
 * Laravel Mix configuration file.
 *
 * @since     1.0.0
 * @package   SimplyFilters
 */

// Import required packages.
const mix = require( 'laravel-mix' ),
	CopyWebpackPlugin = require( 'copy-webpack-plugin' ),
	DependencyExtractionWebpackPlugin = require( '@wordpress/dependency-extraction-webpack-plugin' );

/*
 * Disable the success notification
 */
mix.disableSuccessNotifications();

/*
 * Path to the generated assets.
 */
mix.setPublicPath( 'assets' );

/*
 * Set Laravel Mix options.
 */
mix.options( {
	postCss: [require( 'postcss-preset-env' )()],
	processCssUrls: false
} );

/*
 * Builds sources maps for assets.
 */
mix.sourceMaps();

/*
 * Versioning and cache busting. Append a unique hash for production assets.
 */
mix.version();

/*
 * Compile JavaScript.
 */
mix.js( 'resources/js/admin.js', 'js' )
	.js( 'resources/js/public.js', 'js' );

/**
 * Compile Gutenberg blocks.
 */
mix.js( 'resources/js/blocks/filter-block/index.js', 'js/blocks/filter-block.js' ).react();

/*
 * Compile CSS.
 */

// Sass configuration.
var sassConfig = {
	outputStyle: 'expanded',
	indentType: 'tab',
	indentWidth: 1
};

// Compile SASS/CSS.
mix.sass( 'resources/scss/admin.scss', 'css' )
	.sass( 'resources/scss/public.scss', 'css' )
	.options( sassConfig );

/*
 * Add custom Webpack configuration for images, svg and fonts.
 */
mix.webpackConfig( {
	stats: 'minimal',
	devtool: mix.inProduction() ? false : 'source-map',
	performance: { hints: false },
	plugins: [
		// @link https://github.com/webpack-contrib/copy-webpack-plugin
		new CopyWebpackPlugin(
			{
				patterns: [
					{ from: 'resources/img',        to: './img', noErrorOnMissing: true },
					{ from: 'resources/svg',        to: './svg', noErrorOnMissing: true },
					{ from: 'resources/fonts',      to: './fonts', noErrorOnMissing: true },
					{ from: 'resources/css/vendor', to: './css/vendor', noErrorOnMissing: true },
					{ from: 'resources/js/vendor',  to: './js/vendor', noErrorOnMissing: true },
				],
			}
		),
		new DependencyExtractionWebpackPlugin()
	]
} );