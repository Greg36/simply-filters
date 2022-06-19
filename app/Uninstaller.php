<?php

namespace SimplyFilters;

/**
 * Fired during plugin deactivation
 *
 * @link       https://gregn.pl
 * @since      1.0.0
 *
 * @package    SimplyFilters
 * @subpackage SimplyFilters/includes
 */

class Uninstaller {

	public static function uninstall() {
		delete_option( 'sf-settings' );
	}

}
