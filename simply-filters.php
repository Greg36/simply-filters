<?php

/**
 * The plugin bootstrap file
 *
 * @link              https://gregn.pl
 * @since             1.0.0
 *
 * @wordpress-plugin
 * Plugin Name:       Simply Filters for WooCommerce
 * Plugin URI:        https://gregn.pl/simply-filters
 * Description:       Simply add product's price, category and attribute filters to WooCommerce.
 * Version:           1.0.0
 * Author:            Grzegorz Niedzielski
 * Author URI:        https://gregn.pl
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       simply-filters
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

define( 'SF_VERSION', '1.0.0' );
define( 'SF_URL', plugin_dir_url( __FILE__ ) );
define( 'SF_PATH', plugin_dir_path( __FILE__ ) );
define( 'SF_FILE', plugin_basename( __FILE__ ) );

/*
 * Require Composer autoload
 */
if ( file_exists( SF_PATH . 'vendor/autoload.php' ) ) {
	require_once( SF_PATH . 'vendor/autoload.php' );
}

/*
 * Autoload functions files
 */
array_map( function ( $file ) {
	require_once( SF_PATH . "app/{$file}.php" );
}, [
	'functions-template',
	'functions-taxonomy',
	'functions-helpers'
] );

/**
 * Run during plugin activation
 */
function activate_SimplyFilters() {
	\SimplyFilters\Activator::activate();
}

register_activation_hook( __FILE__, 'activate_SimplyFilters' );


/**
 * Begin execution of the plugin.
 */
\SimplyFilters\SimplyFilters::factory();
