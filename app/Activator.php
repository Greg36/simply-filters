<?php

namespace SimplyFilters;

/**
 * Fired during plugin activation.
 *
 * @since   1.0.0
 */
class Activator {

	public static function activate() {
		self::set_default_general_settings();
	}

	/**
	 * Set default options for general settings
	 */
	private static function set_default_general_settings() {
		update_option( 'sf-settings',  [
			'colors' => [
				'accent' => '#4F76A3',
				'highlight' => '#3987e1',
				'background' => '#ffffff',
				'font_options' => '#445C78',
				'font_titles' => '#404040'
			],
			'style' => 'rounded',
		] );
	}

}
