import FilterUrl from "./lib/filter-url";
import { debounce } from "./lib/helpers";

document.addEventListener( 'DOMContentLoaded', () => {

	(function () {

		/**
		 *  Trigger callback after given wait without concurrent event triggers
		 */


		/**
		 * Debounce price change and change the URL
		 */
		const updatePrice = debounce( ( data ) => {
			const url = new FilterUrl();
			url.update( 'replace', {
				key: 'price',
				value: `${data.min}_${data.max}`
			} );
		}, 300 );

		/**
		 * Update jQuery slider with values from input field and call update
		 *
		 * @param ev
		 * @param data
		 */
		const updateSlider = ( ev, data ) => {
			jQuery( data.slider ).slider( 'values', [data.min, data.max] );

			updatePrice( data );
		};

		/**
		 * Check if value is in range and correct it if needed
		 * @param input
		 */
		const validateRange = ( input, min, max ) => {
			if ( parseInt( input.value ) > parseInt( input.max ) ) {
				input.value = input.max;
			} else if ( parseInt( input.value ) < parseInt( input.min ) ) {
				input.value = input.min;
			}

			if( parseInt( min.value ) > parseInt( max.value ) ) {
				min.value = max.value;
			}

			if( parseInt( max.value ) < parseInt( min.value ) ) {
				max.value = min.value;
			}
		}

		let sliders = document.querySelectorAll( '.sf-slider' );
		sliders.forEach( ( slider ) => {

			const ui = slider.querySelector( '.sf-slider__ui' ),
				min = slider.querySelector( '.sf-slider__input--min' ),
				max = slider.querySelector( '.sf-slider__input--max' );

			ui.style.display = 'block';

			// Init slider
			jQuery( ui ).slider( {
				range: true,
				min: parseInt( min.dataset.min ),
				max: parseInt( max.dataset.max ),
				values: [min.value, max.value],
				slide: function ( event, ui ) {
					let inputs = event.target.nextElementSibling.children;
					inputs[0].value = ui.values[0];
					inputs[1].value = ui.values[1];
					updatePrice( {
						min: min.value,
						max: max.value
					} )
				}
			} );


			// Hook events for both text fields
			[min, max].forEach( ( input ) => {
				input.addEventListener( 'input', ( event ) => {
					debounce( () => {
						validateRange( event.target, min, max );
						updateSlider( event, {
							slider: ui,
							min: min.value,
							max: max.value
						} );
					}, 500 )();

				} );
			} );

		} );

	})();

} );
