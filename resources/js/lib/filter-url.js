/**
 * Handle URL parameters changes
 *
 * @since 1.0.0
 */
export default class FilterUrl {
	constructor( url = '' ) {
		if ( ! url ) {
			url = location.href;
		}

		if ( window.hasOwnProperty( 'sf_filter_url' ) ) {
			this.url = sf_filter_url;
		} else {
			this.url = new URL( url );
			window.sf_filter_url = this.url;
		}
	}

	/**
	 * Perform action on URL parameters
	 *
	 * @param {string} action
	 * @param {Object} param
	 */
	update( action, param ) {
		const href = this.url.href;

		switch ( action ) {
			case 'replace':
				this.replace( param );
				break;
			case 'remove':
				this.remove( param );
				break;
			case 'add':
				this.add( param );
				break;
			case 'price':
				this.price( param );
				break;
			case 'clear':
				this.clear( param );
		}

		// Update URL when it has changed
		if ( href !== this.url.href ) {
			const url = this.getUpdatedURL();

			if ( param.group.dataset.action === 'automatic' ) {
				window.history.pushState( {}, '', url );
				window.dispatchEvent( new Event( 'sf-filter-products' ) );
			} else {
				window.history.replaceState( {}, '', url );
			}
		}
	}

	/**
	 * Replace param in URL
	 *
	 * @param {Object} param
	 */
	replace( param ) {
		this.url.searchParams.set( param.key, param.value );
	}

	/**
	 * Remove param by key in URL
	 *
	 * @param {Object} param
	 */
	clear( param ) {
		this.url.searchParams.delete( param.key );
	}

	/**
	 * Remove param from URL
	 *
	 * @param {Object} param
	 */
	remove( param ) {
		if ( this.url.searchParams.has( param.key ) ) {
			let values = this.url.searchParams.get( param.key ).split( param.delimiter );

			if ( values.indexOf( param.value ) >= 0 ) {
				// Key with single value
				if ( values.length === 1 ) {
					this.url.searchParams.delete( param.key );
				}

				// Multiple values
				if ( values.length > 1 ) {
					values = values.filter( ( ele ) => {
						return ele !== param.value;
					} );
					this.url.searchParams.set( param.key, values.join( param.delimiter ) );
				}
			}
		}
	}

	/**
	 * Add param to URL
	 *
	 * @param {Object} param
	 */
	add( param ) {
		if ( this.url.searchParams.has( param.key ) ) {
			const values = this.url.searchParams.get( param.key ).split( param.delimiter );

			if ( values.indexOf( param.value ) < 0 ) {
				let value = values.join( param.delimiter );
				if ( value !== '' ) {
					value += param.delimiter;
				}
				this.url.searchParams.set( param.key, value + param.value );
			}
		} else {
			this.url.searchParams.set( param.key, param.value );
		}
	}

	/**
	 * Change price range in the URL
	 *
	 * @param {Object} param
	 */
	price( param ) {
		const price = {};
		if ( param.key === 'price-min' ) {
			price.min = parseInt( param.value );
			price.max = parseInt( param.input.nextElementSibling.value );
		} else {
			price.min = parseInt( param.input.previousElementSibling.value );
			price.max = parseInt( param.value );
		}

		param.key = 'price';
		param.value = `${ price.min }_${ price.max }`;
		this.replace( param );
	}

	/**
	 * Get full URL with params
	 */
	getUpdatedURL() {
		return this.url.origin + this.url.pathname + decodeURIComponent( this.url.search );
	}
}
