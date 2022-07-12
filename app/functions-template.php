<?php

namespace SimplyFilters;

/**
 * Load SVG file directly into the markup from theme's files
 *
 * @param string $filename Name of SVG file with .svg extension
 *
 * @return string SVG file contents
 */
function load_inline_svg( $filename ) {

	$svg_path = SF_PATH . 'assets/svg/';

	if ( substr( $filename, - 4 ) !== '.svg' ) {
		$filename .= '.svg';
	}

	if ( file_exists( $svg_path . $filename ) ) {

		return file_get_contents( $svg_path . $filename ); // phpcs:ignore WordPressVIPMinimum.Performance.FetchingRemoteData.FileGetContentsUnknown
	}

	return '';
}

/**
 * Returns SVG file from theme's directory
 *
 * @param string $file SVG filename
 *
 * @return string URL to theme's SVG file
 */
function get_svg( $file ) {
	return SF_URL . 'assets/svg/' . $file . '.svg';
}

/**
 * Returns URL to image file from theme's directory
 *
 * @param string $file Filename
 *
 * @return string URL to theme's image file
 */
function get_image( $file ) {
	return SF_URL . 'assets/img/' . $file;
}

/**
 * Get stars rating with given highlighted stars
 *
 * @param int $count Start of count
 * @param int $max End of count
 *
 * @return string
 */
function get_stars( $count = 1, $max = 5 ) {
	$stars = '';

	for ( $i = 1; $i <= $count; $i ++ ) {
		$stars .= '<span class="sf-star sf-star--full"></span>';
	}

	for ( $i = $count + 1; $i <= $max; $i ++ ) {
		$stars .= '<span class="sf-star"></span>';
	}

	return $stars;
}

/**
 * Return product HTML count label
 *
 * @param array $value
 * @param array $option
 *
 * @return string
 */
function get_product_count( $value, $option ) {
	$label = '';
	if ( $value !== false && isset( $option['id'] ) ) {
		$label .= '<span class="sf-label-count">';
		$label .= isset( $value[ $option['id'] ] ) ? '&nbsp;(' . intval( $value[ $option['id'] ] ) . ')' : ' (0)';
		$label .= '</span>';
	}

	return $label;
}

/**
 * Return show more options HTML button
 *
 * @param array $group_settings
 * @param int $options_count
 */
function more_options_button( $group_settings, $options_count ) {
	if ( $group_settings['more_show'] && $options_count > intval( $group_settings['more_count'] ) ) {
		printf( '<button class="sf-more-btn" aria-expanded="false">%s&nbsp;(%d)</button>',
			esc_html__( 'Show more', \Hybrid\app( 'locale' ) ),
			intval( $options_count - intval( $group_settings['more_count'] ) )
		);
	}
}