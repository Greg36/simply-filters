<?php

namespace SimplyFilters;

/**
 * Calculate relative luminance of color based on WCAG definition
 *
 * @link https://www.w3.org/WAI/GL/wiki/Relative_luminance
 *
 * @param $color string Hex value of a color or converted color object
 * @returns {number} Relative luminance value
 */
function calculateLuminance( $color ) {

	// Prefix # if not present
	if( substr($color, 0, 1) !== '#' ) $color = '#' . $color;

	// Convert hex 2 RGB
	list( $r, $g, $b ) = sscanf( $color, "#%02x%02x%02x" );

	$r /= 255;
	$g /= 255;
	$b /= 255;

	$_r = ($r <= 0.03928) ? $r / 12.92 : pow( ($r + 0.055) / 1.055, 2.4 );
	$_g = ($g <= 0.03928) ? $g / 12.92 : pow( ($g + 0.055) / 1.055, 2.4 );
	$_b = ($b <= 0.03928) ? $b / 12.92 : pow( ($b + 0.055) / 1.055, 2.4 );

	// For the sRGB colorspace, the relative luminance of a color is defined as:
	return 0.2126 * $_r + 0.7152 * $_g + 0.0722 * $_b;
}

/**
 * Limit the value to not be outside set min-max range
 *
 * @param $value
 * @param $min
 * @param $max
 *
 * @return int
 */
function limit_value_to_range( $value, $min, $max ) {
	if( $value < $min ) return $min;
	if( $value > $max ) return $max;

	return $value;
}