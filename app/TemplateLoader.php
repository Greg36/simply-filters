<?php

namespace SimplyFilters;

/**
 * Takes care of rendering partials and passing
 * data to the view.
 *
 * @package    SimplyFilters
 * @subpackage SimplyFilters/Admin
 * @author     Grzegorz Niedzielski <admin@gregn.pl>
 */
class TemplateLoader {

	/**
	 * Load provided template from admin views folder and pass through variables
	 *
	 * @param string $view_path
	 * @param array $view_args
	 */
	public static function render( $view_path = '', $view_args = array() ) {
		if ( substr( $view_path, -4 ) !== '.php' ) {
			$view_path = static::get_path( "app/Admin/views/{$view_path}.php" );
		}

		// Include view
		if ( file_exists( $view_path ) ) {
			// EXTR_SKIP prevents already present values to accidentally being overwritten
			$view_args = static::default_values( $view_args );
			extract( $view_args, EXTR_SKIP );
			include $view_path;
		}
	}

	/**
	 * Instead of outputting the view directly return it.
	 *
	 * @param string $view_path
	 * @param array $view_args
	 *
	 * @return false|string Rendered view's HTML
	 */
	public static function get( $view_path = '', $view_args = array() ) {
		ob_start();
		static::render( $view_path, $view_args );
		return ob_get_clean();
	}

	/**
	 * Returns full path to a specific file
	 *
	 * @param $filename
	 *
	 * @return string Complete template path
	 */
	private static function get_path( $filename ) {
		return SF_PATH . ltrim( $filename, '/' );
	}

	/**
	 * Include default values for every view
	 *
	 * @param array $view_args
	 */
	private static function default_values( array $view_args ) {
		// Locale
		$default = [
			'locale' => \Hybrid\app( 'locale' )
		];

		return wp_parse_args( $view_args, $default );
	}
}
