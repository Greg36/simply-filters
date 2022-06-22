/**
 * Color settings field
 *
 * @since 1.0.0
 */
export default class ColorControl {

	constructor( adminFilter ) {
		this.filter = adminFilter;
		this.$selector = jQuery( adminFilter.filter.querySelectorAll( '.sf-color__field' ) );
	}

	/**
	 * Initialize color picker and events
	 */
	init() {
		this.$selector.wpColorPicker( {
			defaultColor: false,
			hide: true,
			palettes: true,
			change: this.updateColor.bind( this, this.filter ),
			clear: this.updateColor.bind( this, this.filter )
		} );

		this.$selector.each( ( num, ele ) => {
			const ev = { target: ele };
			this.updateColor( this.filter, ev );
		} );

	}

	/**
	 * Update color of all elements related to color picker
	 */
	updateColor( filter, event ) {
		let colorInput = jQuery( event.target ),
			color = colorInput.hasClass( 'wp-picker-clear' ) ? '' : colorInput.val(),
			swatches = colorInput.parents( '.sf-color__row' ).find( '.sf-color__swatch' );

		swatches.each( ( i, swatch ) => {
			this.updateSwatch( jQuery( swatch ), color );
		} );

		filter.save();
	}

	/**
	 * Update color swatch and checkmark
	 */
	updateSwatch( $ele, color ) {
		$ele.css( 'backgroundColor', color );

		// For selected swatch update the checkmark color
		if ( $ele.hasClass( 'sf-color__swatch--selected' ) ) {
			this.changeColorWithContrast( $ele.find( 'svg path' ), color, 'fill' );
		}
	}

	/**
	 * Change property of an element to either black or white based on base
	 * color luminance
	 */
	changeColorWithContrast( $ele, color, param ) {
		let L = this.calculateLuminance( color ),
			fill = '#000000';

		if ( L < 0.179 ) fill = '#ffffff';

		$ele.css( param, fill );
	}


	/**
	 * Calculate relative luminance of color based on WCAG definition
	 *
	 * @link https://www.w3.org/WAI/GL/wiki/Relative_luminance
	 * @returns {number} Relative luminance value
	 */
	calculateLuminance( color ) {
		if ( typeof color !== 'object' ) color = this.hexToRgb( color );

		color.r /= 255;
		color.g /= 255;
		color.b /= 255;

		let R = (color.r <= 0.03928) ? color.r / 12.92 : Math.pow( (color.r + 0.055) / 1.055, 2.4 );
		let G = (color.g <= 0.03928) ? color.g / 12.92 : Math.pow( (color.g + 0.055) / 1.055, 2.4 );
		let B = (color.b <= 0.03928) ? color.b / 12.92 : Math.pow( (color.b + 0.055) / 1.055, 2.4 );

		// For the sRGB colorspace, the relative luminance of a color is defined as:
		return 0.2126 * R + 0.7152 * G + 0.0722 * B;
	}

	/**
	 * Convert hex color value to RGB object
	 *
	 * @param hex Color string
	 * @returns {{r: number, b: number, g: number}|null}
	 */
	hexToRgb( hex ) {
		let result = /^#?([a-f\d]{2})([a-f\d]{2})([a-f\d]{2})$/i.exec( hex );
		return result ? {
			r: parseInt( result[1], 16 ),
			g: parseInt( result[2], 16 ),
			b: parseInt( result[3], 16 )
		} : null;
	}

}
