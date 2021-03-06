<?php

namespace SimplyFilters;

/**
 * Calculate relative luminance of color based on WCAG definition
 *
 * @link https://www.w3.org/WAI/GL/wiki/Relative_luminance
 *
 * @param string $color Hex value of a color or converted color object
 *
 * @returns float Relative luminance value
 */
function calculateLuminance( $color ) {

	// Prefix # if not present
	if ( substr( $color, 0, 1 ) !== '#' ) {
		$color = '#' . $color;
	}

	// Convert hex 2 RGB
	list( $r, $g, $b ) = sscanf( $color, "#%02x%02x%02x" );

	$r /= 255;
	$g /= 255;
	$b /= 255;

	$_r = ( $r <= 0.03928 ) ? $r / 12.92 : pow( ( $r + 0.055 ) / 1.055, 2.4 );
	$_g = ( $g <= 0.03928 ) ? $g / 12.92 : pow( ( $g + 0.055 ) / 1.055, 2.4 );
	$_b = ( $b <= 0.03928 ) ? $b / 12.92 : pow( ( $b + 0.055 ) / 1.055, 2.4 );

	// For the sRGB colorspace, the relative luminance of a color is defined as:
	return 0.2126 * $_r + 0.7152 * $_g + 0.0722 * $_b;
}

/**
 * Limit the value to not be outside set min-max range
 *
 * @param int $value
 * @param int $min
 * @param int $max
 *
 * @return int
 */
function limit_value_to_range( $value, $min, $max ) {
	if ( $value < $min ) {
		return $min;
	}
	if ( $value > $max ) {
		return $max;
	}

	return $value;
}

/**
 * Adjust hex color brightness
 *
 * @param string $hex
 * @param int $steps
 *
 * @return string
 */
function adjustBrightness( $hex, $steps ) {
	// Steps should be between -255 and 255. Negative = darker, positive = lighter
	$steps = max( - 255, min( 255, $steps ) );

	// Normalize into a six character long hex string
	$hex = str_replace( '#', '', $hex );
	if ( strlen( $hex ) == 3 ) {
		$hex = str_repeat( substr( $hex, 0, 1 ), 2 ) . str_repeat( substr( $hex, 1, 1 ), 2 ) . str_repeat( substr( $hex, 2, 1 ), 2 );
	}

	// Split into three parts: R, G and B
	$color_parts = str_split( $hex, 2 );
	$return      = '#';

	foreach ( $color_parts as $color ) {
		$color  = hexdec( $color ); // Convert to decimal
		$color  = max( 0, min( 255, $color + $steps ) ); // Adjust color
		$return .= str_pad( dechex( $color ), 2, '0', STR_PAD_LEFT ); // Make two char hex code
	}

	return $return;
}