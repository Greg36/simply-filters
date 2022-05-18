<?php

namespace SimplyFilters;

/**
 * Load SVG file directly into the markup from theme's files
 *
 * @param $filename string Name of SVG file with .svg extension
 *
 * @return string SVG file contents
 */
function load_inline_svg( $filename ) {

	$svg_path = SF_PATH . 'assets/svg/';

	if( substr( $filename, -4 ) !== '.svg' ) $filename .= '.svg';

	if ( file_exists( $svg_path . $filename ) ) {

		return file_get_contents( $svg_path . $filename );
	}

	return '';
}

/**
 * Returns SVG file from theme's directory
 *
 * @param $file string SVG filename
 *
 * @return string URL to theme's SVG file
 */
function get_svg( $file ) {
	return SF_URL . 'assets/svg/' . $file . '.svg';
}

/**
 * Returns URL to image file from theme's directory
 *
 * @param $file string filename
 *
 * @return string URL to theme's image file
 */
function get_image( $file ) {
	return SF_URL . 'assets/img/' . $file;
}

/**
 * Get stars rating with given highlighted stars
 *
 * @param $count
 *
 * @return string
 */
function get_stars( $count = 1, $max = 5 ) {
	$stars = '';

	for ( $i = 1; $i <= $count; $i++ ) {
		$stars .= '<span class="sf-star sf-star--full"></span>';
	}

	for ( $i = $count + 1; $i <= $max; $i++ ) {
		$stars .= '<span class="sf-star"></span>';
	}

	return $stars;
}