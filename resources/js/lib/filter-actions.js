import FilterUrl from "./filter-url";
import { addLoader, getCookie, removeLoader, setCookie } from "./helpers";
import { setupSliders } from "../range-slider";

export default class FilterActions {

	constructor() {
		this.url = new FilterUrl();
		this.filters = [];

		this.initFilters();
		this.setupMoreButtons();
		this.setupCollapseButtons();
		this.setupSubmitButtons();
		setupSliders();

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

		fetch( location.href, {
			method: 'GET',
		} ).then( ( response ) => {
			return response.text();
		} ).then( ( html ) => {

			// Setup new document
			const content = document.implementation.createHTMLDocument( document.title );
			content.documentElement.innerHTML = html;

			this.updatePageFragments( content );
			this.updateOptionCounters( content );
			this.updateRangeSliders( content );
		} );

		addLoader( document.body );
	}

	updatePageFragments( content ) {

		// @todo: use here user-entered selectors?
		const selectors = [
			'.woocommerce-pagination',
			'.woocommerce-breadcrumb',
			'.products',
			'.woocommerce-result-count',
			'.woocommerce-ordering',
			'.woocommerce-products-header__title',
		];
		selectors.forEach( ( selector ) => {
			const home = document.querySelectorAll( selector );
			const ext = content.querySelectorAll( selector );

			// Both elements are present in both trees in equal quantity
			if ( home.length > 0 && home.length === ext.length ) {
				home.forEach( ( ele, index ) => {
					ele.replaceWith( ext[index] );
				} );
				return;
			}

			// Content is present only on new version
			if ( home.length === 0 && ext.length > 0 ) {
				ext.forEach( ( ele ) => {
					let location = this.getRelativeDOMPosition( ext, ele );
					if ( location.selector ) {
						let root = document.querySelector( location.selector );

						// Traverse children according to queried order
						while ( location.index.length > 1 ) {
							root = root.children[location.index.shift()];
						}

						// Insert on specified index
						root.insertBefore( ele, root.children[location.index.shift()] );
					}
				} );
				return;
			}

			// Content is present only on present content
			if ( home.length > 0 && ext.length === 0 ) {
				home.forEach( ( ele ) => {
					ele.remove();
				} );
			}
		} );

		removeLoader();
	}

	/**
	 * Find option count values in filters and replace them with data from new content
	 *
	 * @param content
	 */
	updateOptionCounters( content ) {
		const options = document.querySelectorAll( '.sf-filter .sf-label-count' );
		const replace = content.querySelectorAll( '.sf-filter .sf-label-count' );

		let values = {};
		replace.forEach( ( label ) => {
			values[label.parentNode.getAttribute( 'for' )] = label.innerHTML.trim();
		} );

		options.forEach( ( label ) => {
			let label_id = label.parentNode.getAttribute( 'for' );
			if ( values.hasOwnProperty( label_id ) ) label.innerHTML = ' ' + values[label_id];
		} );
	}

	updateRangeSliders( content ) {
		const sliders = document.querySelectorAll( '.sf-filter .sf-slider' );
		let updated = false;

		sliders.forEach( ( slider ) => {
			const update = content.getElementById( slider.id );
			if ( update ) {
				slider.replaceWith( update );
				updated = true;
			}
		} );

		if ( updated ) {
			setupSliders();
		}
	}

	/**
	 * Step up te DOM tree and find the closest element with unique ID
	 * saving children index on the path
	 *
	 * @param doc
	 * @param ele
	 * @param location
	 */
	getRelativeDOMPosition( doc, ele, location = [] ) {
		location.push( Array.from( ele.parentNode.children ).indexOf( ele ) )

		if ( ele.parentElement.id ) {
			let parent = document.querySelectorAll( '#' + ele.parentNode.id );
			if ( parent.length === 1 ) return {
				selector: '#' + ele.parentNode.id,
				index: location.reverse(),
			}
		} else {
			return this.getRelativeDOMPosition( doc, ele.parentElement, location );
		}
	}

	/**
	 * Set events for show more options button
	 */
	setupMoreButtons() {
		document.querySelectorAll( '.sf-filter .sf-more-btn' ).forEach( ( button ) => {
			const filter = button.closest( '.sf-filter' );
			const options = filter.querySelectorAll( '.sf-option-more' );
			const label = button.innerHTML;

			const list = filter.querySelector( '.sf-option-list' );
			const initial_height = list.offsetHeight;

			button.addEventListener( 'click', ( e ) => {

				if ( button.classList.contains( 'sf-more-btn--open' ) ) {

					// Close list
					list.style.height = list.offsetHeight + 'px';
					button.innerHTML = label;

					setTimeout( () => {
						list.style.height = initial_height + 'px';
					}, 0 );
					setTimeout( () => {
						list.style.height = '';
						button.ariaExpanded = false;
						options.forEach( ( option ) => {
							option.classList.add( 'sf-option-more' );
						} );
					}, 200 );

				} else {
					// Open list
					button.innerHTML = sf_filters.locale.show_less;
					button.ariaExpanded = true;
					options.forEach( ( option ) => {
						option.classList.remove( 'sf-option-more' );
					} );

					const target_height = list.offsetHeight;
					list.style.height = initial_height + 'px';

					setTimeout( () => {
						list.style.height = target_height + 'px';
					}, 0 );
					setTimeout( () => {
						list.style.height = '';
					}, 200 );
				}

				button.classList.toggle( 'sf-more-btn--open' );
			} );
		} );
	}

	setupCollapseButtons() {
		document.querySelectorAll( '.sf-filter .sf-filter__collapse' ).forEach( ( button ) => {
			const filter = button.closest( '.sf-filter' );
			const options = filter.querySelector( '.sf-filter__filter' );

			button.addEventListener( 'click', ( e ) => {
				if ( options.classList.contains( 'sf-filter--collapsed' ) ) {

					// Open options
					button.classList.remove( 'collapsed' );
					options.classList.remove( 'sf-filter--collapsed' );

					const target_height = options.offsetHeight;
					options.style.height = '0px';

					setTimeout( () => {
						options.style.height = target_height + 'px';
					}, 0 );
					setTimeout( () => {
						options.style.height = '';
					}, 200 );

					this.updateCollapseCookie( filter.dataset.id, false );

				} else {

					// Close options
					button.classList.add( 'collapsed' );
					options.style.height = options.offsetHeight + 'px';

					setTimeout( () => {
						options.style.height = '0px';
					}, 0 );
					setTimeout( () => {
						options.style.height = '';
						options.classList.add( 'sf-filter--collapsed' );
					}, 200 );

					this.updateCollapseCookie( filter.dataset.id, true );
				}
			} );
		} );
	}

	/**
	 * Update filter collapse cookie
	 *
	 * @param id
	 * @param value
	 */
	updateCollapseCookie( id, value ) {
		const cookie_name = 'sf-filters-collapsed';
		let cookie = getCookie( cookie_name );

		if ( typeof cookie === 'undefined' ) {
			if ( value ) setCookie( cookie_name, id, 7 );
		} else {
			let cookies = cookie.split( '|' );
			if( ! cookies.includes( id ) && value ) {

				// Add id to cookie
				cookies.push( id );
				setCookie( cookie_name, cookies.join( '|' ), 7 );
			} else if( cookies.includes( id ) && ! value ) {

				// Remove id from cookie
				cookies = cookies.filter( item => item !== id );
				setCookie( cookie_name, cookies.join( '|' ), 7 );
			}
		}
	}

	/**
	 * Setup events for submit button
	 */
	setupSubmitButtons() {
		document.querySelectorAll( '.sf-filter-group__submit' ).forEach( ( submit ) => {
			submit.addEventListener( 'click', (e) => {
				const url = this.url.getUpdatedURL();
				const event = new Event( 'sf-filter-products' );
				window.dispatchEvent( event );
				window.history.pushState( {}, '', url );
			} );
		} );
	}

}