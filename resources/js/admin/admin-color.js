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
			clear: this.updateColor.bind( this, this.filter ),
		} );

		this.$selector.each( ( num, ele ) => {
			const ev = { target: ele };
			this.updateColor( this.filter, ev );
		} );
	}

	/**
	 * Update color of all elements related to color picker
	 *
	 * @param {Object} filter
	 * @param {Event}  event
	 */
	updateColor( filter, event ) {
		const colorInput = jQuery( event.target ),
			color = colorInput.hasClass( 'wp-picker-clear' ) ? '' : colorInput.val(),
			swatches = colorInput.parents( '.sf-color__row' ).find( '.sf-color__swatch' );

		swatches.each( ( i, swatch ) => {
			this.updateSwatch( jQuery( swatch ), color );
		} );

		filter.save();
	}

	/**
	 * Update color swatch and checkmark
	 *
	 * @param {Object} $ele
	 * @param {string} color
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
	 *
	 * @param {Object} $ele
	 * @param {string} color
	 * @param {string} param
	 */
	changeColorWithContrast( $ele, color, param ) {
		const L = this.calculateLuminance( color );
		let fill = '#000000';

		if ( L < 0.179 ) {
			fill = '#ffffff';
		}

		$ele.css( param, fill );
	}

	/**
	 * Calculate relative luminance of color based on WCAG definition
	 * {@link https://www.w3.org/WAI/GL/wiki/Relative_luminance}
	 *
	 * @param {string} color
	 * @return {number} Relative luminance value
	 */
	calculateLuminance( color ) {
		if ( typeof color !== 'object' ) {
			color = this.hexToRgb( color );
		}

		color = {
			r: color.r / 255,
			g: color.g / 255,
			b: color.b / 255,
		};

		const R = ( color.r <= 0.03928 ) ? color.r / 12.92 : Math.pow( ( color.r + 0.055 ) / 1.055, 2.4 );
		const G = ( color.g <= 0.03928 ) ? color.g / 12.92 : Math.pow( ( color.g + 0.055 ) / 1.055, 2.4 );
		const B = ( color.b <= 0.03928 ) ? color.b / 12.92 : Math.pow( ( color.b + 0.055 ) / 1.055, 2.4 );

		// For the sRGB colorspace, the relative luminance of a color is defined as:
		return ( 0.2126 * R ) + ( 0.7152 * G ) + ( 0.0722 * B );
	}

	/**
	 * Convert hex color value to RGB object
	 *
	 * @param {string} hex Color string
	 * @return {{r: number, b: number, g: number}|null} Color in RGB
	 */
	hexToRgb( hex ) {
		const result = /^#?([a-f\d]{2})([a-f\d]{2})([a-f\d]{2})$/i.exec( hex );
		return result ? {
			r: parseInt( result[ 1 ], 16 ),
			g: parseInt( result[ 2 ], 16 ),
			b: parseInt( result[ 3 ], 16 ),
		} : null;
	}
}
