import FilterUrl from "./filter-url";

export default class FilterActions {

	constructor() {
		this.url = new FilterUrl();
		this.filters = [];

		this.initFilters();

		window.addEventListener( 'sf-filter-products', () => {
			this.filterProducts();
		} );
	}

	initFilters() {
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
		data.group = filter.closest( '.sf-filter-group' );

		this.filters.push( data );
		this.setupFilterEvents( data );
	}

	/**
	 * Setup events for input fields in filter
	 *
	 * @param filter
	 */
	setupFilterEvents( filter ) {

		// On input change
		filter.inputs.forEach( ( input ) => {
			input.addEventListener( 'change', ( e ) => {

				const param = {
					key: e.target.name,
					value: e.target.value,
					delimiter: e.target.dataset.query === 'and' ? ' ' : '|',
					input: e.target,
					group: filter.group
				};

				let action = '';

				// Handle URL change
				if ( filter.type === 'Slider' ) {
					action = 'price';
				} else if ( filter.type === 'Radio' || filter.type === 'Select' ) {
					action = param.value === 'no-filter' ? 'clear' : 'replace';
				} else if ( e.target.checked ) {
					action = 'add';
				} else {
					action = 'remove';
				}

				this.url.update( action, param );
			} );
		} );


	}

	filterProducts() {
		const xhr = new XMLHttpRequest();
		xhr.onload = () => {
			if (xhr.readyState === 4) {
				if (xhr.status === 200) {
					this.updatePage( xhr.responseXML );
				} else {
					this.updateError( xhr );
				}
			}
		}
		xhr.open( 'GET', location.href );
		xhr.responseType = 'document';
		xhr.send();
	}

	updatePage( content ) {
		// @todo: use here user-entered selectors?

		// Products
		document.querySelector( 'ul.products' ).innerHTML = content.querySelector( 'ul.products' ).innerHTML;

		// Pagination
		// document.querySelector( 'nav.woocommerce-pagination' ).innerHTML = content.querySelector( 'nav.woocommerce-pagination' ).innerHTML;

		// Result count
		document.querySelector( 'p.woocommerce-result-count' ).innerHTML = content.querySelector( 'p.woocommerce-result-count' ).innerHTML;

	}

	updateError( request ) {

	}

}