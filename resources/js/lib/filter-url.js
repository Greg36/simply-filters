export default class FilterUrl {
	
	constructor() {
		this.url = new URL( location.href );
	}
	
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
		if( href !== this.url.href ) {
			this.pushToHistory();
		}
	}

	/**
	 * Replace param in URL
	 *
	 * @param param
	 */
	replace( param ) {
		this.url.searchParams.set( param.key, param.value );
	}

	/**
	 * Remove param by key in URL
	 *
	 * @param param
	 */
	clear( param ) {
		this.url.searchParams.delete( param.key );
	}

	/**
	 * Remove param from URL
	 *
	 * @param param
	 */
	remove( param ) {
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
	}

	/**
	 * Add param to URL
	 *
	 * @param param
	 */
	add( param ) {
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
	}

	price( param ) {

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

	pushToHistory() {
		let url = this.url.origin + this.url.pathname + decodeURIComponent( this.url.search );
		window.history.pushState( {}, '', url );
	}
}