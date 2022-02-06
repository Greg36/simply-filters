<?php

/**
 * The plugin bootstrap file
 *
 * @link              https://gregn.pl
 * @since             1.0.0
 * @package           Simply_Filters
 *
 * @wordpress-plugin
 * Plugin Name:       Simply Filters for WooCommerce
 * Plugin URI:        https://gregn.pl/simply-woocommerce-filters
 * Description:       This is a short description of what the plugin does. It's displayed in the WordPress admin area.
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

define( 'SIMPLY_FILTERS_VERSION', '1.0.0' );

/**
 * Run during plugin activation
 */
function activate_simply_filters() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-simply-filters-activator.php';
	Simply_Filters_Activator::activate();
}
register_activation_hook( __FILE__, 'activate_simply_filters' );

/**
 * Run during plugin deactivation
 */
function deactivate_simply_filters() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-simply-filters-deactivator.php';
	Simply_Filters_Deactivator::deactivate();
}
register_deactivation_hook( __FILE__, 'deactivate_simply_filters' );

/**
 * Load the core plugin class
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-simply-filters.php';

/**
 * Begins execution of the plugin.
 *
 * @since    1.0.0
 */
function run_simply_filters() {

	$plugin = new Simply_Filters();
	$plugin->run();

}
run_simply_filters();
