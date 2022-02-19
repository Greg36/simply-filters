<?php

namespace SimplyFilters;

trait Assets {

	/**
	 * Helper function for outputting an asset URL in the plugin. This integrates
	 * with Laravel Mix for handling cache busting.
	 *
	 * @since  1.0.0
	 * @access private
	 * @param  string  $path  A relative path/file to append to the `dist` folder.
	 * @return string
	 */
	public function getAssetPath( $path ) {
		// Get the Laravel Mix manifest.
		$manifest_path = SF_PATH . 'assets/mix-manifest.json';

		$manifest = file_exists( $manifest_path ) ? json_decode( file_get_contents( $manifest_path ), true ) : null;

		// Make sure to trim any slashes from the front of the path.
		$path = '/' . ltrim( $path, '/' );

		if ( $manifest && isset( $manifest[ $path ] ) ) {
			$path = $manifest[ $path ];
		}

		return SF_URL . 'assets' . $path;
	}
}