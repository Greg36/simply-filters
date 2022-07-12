<?php

namespace SimplyFilters;

/**
 * Collection of assets handling methods
 *
 * @since   1.0.0
 */
trait Assets {

	/**
	 * Helper function for outputting an asset URL in the plugin. This integrates
	 * with Laravel Mix for handling cache busting.
	 *
	 * @param string $path A relative path/file to append to the `dist` folder.
	 *
	 * @return  string
	 */
	public function getAssetPath( $path ) {
		// Get the Laravel Mix manifest.
		$manifest_path = SF_PATH . 'assets/mix-manifest.json';

		$manifest = file_exists( $manifest_path ) ? json_decode( file_get_contents( $manifest_path ), true ) : null; // phpcs:ignore WordPressVIPMinimum.Performance.FetchingRemoteData.FileGetContentsUnknown

		// Make sure to trim any slashes from the front of the path.
		$path = '/' . ltrim( $path, '/' );

		if ( $manifest && isset( $manifest[ $path ] ) ) {
			$path = $manifest[ $path ];
		}

		return SF_URL . 'assets' . $path;
	}

	/**
	 * Include dynamic style variables
	 *
	 * @param string $handle Enqueued stylesheet handle
	 */
	public function enqueue_dynamic_styles( $handle ) {
		$options = get_option( 'sf-settings' );

		// Element colors
		$colors = isset( $options['colors'] ) ? $options['colors'] : [];
		$colors = array_filter( $colors, function ( $option ) {
			return sanitize_hex_color( $option );
		} );

		$defaults = [
			'accent'       => '#4F76A3',
			'accent-dark'  => '',
			'highlight'    => '#3987e1',
			'background'   => '#ffffff',
			'font_titles'  => '#404040',
			'font_options' => '#445C78'
		];

		$colors                = wp_parse_args( $colors, $defaults );
		$colors['accent-dark'] = adjustBrightness( $colors['accent'], - 20 );

		// Create styles string
		$styles = '';
		foreach ( $colors as $key => $option ) {
			if ( ! array_key_exists( $key, $defaults ) ) {
				continue;
			}

			$styles .= '--sf-' . $key . ': ' . $option . '; ';
		}

		// Elements style
		$element_style = isset( $options['style'] ) ? esc_attr( $options['style'] ) : 'rounded';
		if ( $element_style === 'rounded' ) {
			$styles .= '--sf-corner: 3px; ';
			$styles .= '--sf-corner-button: 5px; ';
		} else {
			$styles .= '--sf-corner: 0; ';
			$styles .= '--sf-corner-button: 0; ';
		}

		wp_add_inline_style( $handle, "
		:root {
			 {$styles}
		}
		" );
	}
}