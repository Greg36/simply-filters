export default class FilterActions {

	constructor() {
		this.url = new URL( location.href );
		this.filters = [];

		const filters = document.querySelectorAll( '.sf-filter' );
		if ( filters ) {
			filters.forEach( ( filter ) => {
				this.initFilter( filter );
			} );
		}
	}

	initFilter( filter ) {
		let data = {};
		data.type = filter.dataset.type;
		data.inputs = filter.querySelectorAll( 'input, select' );

		this.filters.push( data );
		this.setupEvents( data );
	}

	/**
	 * Setup events for input fields in filter
	 *
	 * @param filter
	 */
	setupEvents( filter ) {
		filter.inputs.forEach( ( input ) => {
			input.addEventListener( 'change', ( e ) => {

				const param = {
					key: e.target.name,
					value: e.target.value,
					delimiter: e.target.dataset.query === 'and' ? ' ' : '|',
					input: e.target
				};

				// Handle URL change
				if( filter.type === 'Slider' ) {
					this.priceURLParam( param );
				} else if( filter.type === 'Radio' || filter.type === 'Select' ) {
					if( param.value === 'no-filter' ) {
						this.removeURLParam( param );
					} else {
						this.replaceURLParam( param );
					}
				} else if( e.target.checked ) {
					this.addURLParam( param );
				} else {
					this.removeURLParam( param );
				}
			} );
		} );
	}

	/**
	 * Replace param in URL and update history
	 *
	 * @param param
	 */
	replaceURLParam( param ) {
		const href = this.url.href;

		this.url.searchParams.set( param.key, param.value );

		// Update URL when it has changed
		if( href !== this.url.href ) {
			this.updateURL();
		}
	}

	/**
	 * Remove param from URL and update history
	 *
	 * @param param
	 */
	removeURLParam( param ) {
		const href = this.url.href;

		if ( this.url.searchParams.has( param.key ) ) {
			let values = this.url.searchParams.get( param.key ).split( param.delimiter );

			if( values.indexOf( param.value ) >= 0 ) {

				// Key with single value
				if( values.length === 1 ) {
					this.url.searchParams.delete( param.key );
				}

				// Multiple values
				if( values.length > 1 ) {
					values = values.filter( (ele) => { return ele !== param.value } );
					this.url.searchParams.set( param.key, values.join( param.delimiter ) );
				}
			}
		}

		// Update URL when it has changed
		if( href !== this.url.href ) {
			this.updateURL();
		}
	}

	/**
	 * Add param to URL and update history
	 *
	 * @param param
	 */
	addURLParam( param ) {
		const href = this.url.href;

		if ( this.url.searchParams.has( param.key ) ) {
			// @todo what if delimiter is different than one in param?
			let values = this.url.searchParams.get( param.key ).split( param.delimiter );

			if ( values.indexOf( param.value ) < 0 ) {
				let value = values.join( param.delimiter );
				if ( value !== '' ) value += param.delimiter;
				this.url.searchParams.set( param.key, value + param.value );
			}
		} else {
			this.url.searchParams.set( param.key, param.value );
		}

		// Update URL when it has changed
		if( href !== this.url.href ) {
			this.updateURL();
		}
	}

	priceURLParam( param ) {

		let price = {};
		if( param.key === 'price-min' ) {
			price.min = parseInt( param.value );
			price.max = parseInt( param.input.nextElementSibling.value );
		} else {
			price.min = parseInt( param.input.previousElementSibling.value );
			price.max = parseInt( param.value );
		}

		param.key = 'price';
		param.value = `${price.min}_${price.max}`;
		this.replaceURLParam( param );
	}

	updateURL() {
		let url = this.url.origin + this.url.pathname + decodeURIComponent( this.url.search );
		window.history.pushState( {}, '', url );
	}
}