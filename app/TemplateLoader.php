<?php

namespace SimplyFilters;

/**
 * Takes care of rendering partials and passing data to the view.
 *
 * @since   1.0.0
 */
class TemplateLoader {

	/**
	 * Include provided template and pass through variables
	 *
	 * @param string $view_path
	 * @param array $args
	 */
	public static function render( $view_path, $args = array(), $type = 'Admin' ) {
		if ( substr( $view_path, - 4 ) !== '.php' ) {
			$view_path = static::get_path( "app/{$type}/views/{$view_path}.php" );
		}

		// Include view
		if ( file_exists( $view_path ) ) {
			$args = static::default_values( $args );

			// EXTR_SKIP prevents already present values to accidentally being overwritten
			extract( $args, EXTR_SKIP );
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
