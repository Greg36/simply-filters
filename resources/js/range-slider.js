import FilterUrl from './lib/filter-url';
import { debounce } from './lib/helpers';

/**
 * Update price and the URL with debounce
 */
const updatePrice = debounce( ( data ) => {
	const url = new FilterUrl();
	url.update( 'replace', {
		key: 'price',
		value: `${ data.min }_${ data.max }`,
		group: data.group,
	} );
}, 100 );

/**
 * Update jQuery slider with values from input field and call update
 *
 * @param {Event}  ev
 * @param {Object} data
 */
const updateSlider = ( ev, data ) => {
	jQuery( data.slider ).slider( 'values', [ data.min, data.max ] );

	updatePrice( data );
};

/**
 * Check if value is in range and correct it if needed
 *
 * @param {Object} input
 * @param {Object} min
 * @param {Object} max
 */
const validateRange = ( input, min, max ) => {
	if ( parseInt( input.value ) > parseInt( input.max ) ) {
		input.value = input.max;
	} else if ( parseInt( input.value ) < parseInt( input.min ) ) {
		input.value = input.min;
	}

	if ( parseInt( min.value ) > parseInt( max.value ) ) {
		min.value = max.value;
	}

	if ( parseInt( max.value ) < parseInt( min.value ) ) {
		max.value = min.value;
	}
};

/**
 * Format price based on site's currency settings
 *
 * @param {string} price
 */
// eslint-disable-next-line no-unused-vars
const formatPrice = ( price ) => {
	const symbol = sf_filters.currency;
	switch ( sf_filters.price_format ) {
		case 'left' :
			return '' + symbol + price;
		case 'right' :
			return '' + price + symbol;
		case 'left_space' :
			return '' + symbol + '&nbsp;' + price;
		case 'right_space' :
			return '' + price + '&nbsp;' + symbol;
	}
	return price;
};

/**
 * Setup price sliders
 *
 * @since 1.0.0
 */
export const setupSliders = () => {
	const sliders = document.querySelectorAll( '.sf-slider' );
	sliders.forEach( ( slider ) => {
		const sliderUi = slider.querySelector( '.sf-slider__ui' ),
			min = slider.querySelector( '.sf-slider__input--min' ),
			max = slider.querySelector( '.sf-slider__input--max' ),
			group = slider.closest( '.sf-filter-group' );

		sliderUi.style.display = 'block';

		// Init slider
		jQuery( sliderUi ).slider( {
			range: true,
			min: parseInt( min.dataset.min ),
			max: parseInt( max.dataset.max ),
			values: [ min.value, max.value ],
			slide( event, ui ) {
				if ( ui.values[ 1 ] - ui.values[ 0 ] < 1 ) {
					return false;
				}

				const inputs = event.target.nextElementSibling.children;
				inputs[ 0 ].value = ui.values[ 0 ];
				inputs[ 1 ].value = ui.values[ 1 ];
			},
			stop() {
				updatePrice( {
					min: min.value,
					max: max.value,
					group,
				} );
			},
		} );

		// Hook events for both text fields
		[ min, max ].forEach( ( input ) => {
			input.addEventListener( 'input', ( event ) => {
				debounce( () => {
					validateRange( event.target, min, max );
					updateSlider( event, {
						slider: sliderUi,
						min: min.value,
						max: max.value,
					} );
				}, 500 )();
			} );
		} );
	} );
};
