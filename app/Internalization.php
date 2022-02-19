<?php

namespace SimplyFilters;

/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link       https://gregn.pl
 * @since      1.0.0
 *
 * @package    SimplyFilters
 * @subpackage SimplyFilters/includes
 */

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      1.0.0
 * @package    SimplyFilters
 * @subpackage SimplyFilters/includes
 * @author     Grzegorz Niedzielski <admin@gregn.pl>
 */
class Internalization {


	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function load_plugin_textdomain() {

		load_plugin_textdomain(
			'simply-filters',
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);

	}



}
