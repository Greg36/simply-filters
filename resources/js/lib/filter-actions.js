import FilterUrl from './filter-url';
import { addLoader, getCookie, removeLoader, setCookie } from './helpers';
import { setupSliders } from '../range-slider';
import { __ } from '@wordpress/i18n';

/**
 * Initialize front-end functionality
 *
 * @since 1.0.0
 */
export default class FilterActions {
	constructor() {
		this.url = new FilterUrl();
		this.filters = [];

		this.initFilters();
		this.setupMoreButtons();
		this.setupCollapseButtons();
		this.setupSubmitButtons();
		this.setupClearButtons();
		setupSliders();

		window.addEventListener( 'sf-filter-products', () => {
			this.filterProducts();
		} );
	}

	/**
	 * Initialize filters
	 */
	initFilters() {
		const filters = document.querySelectorAll( '.sf-filter' );
		if ( filters ) {
			filters.forEach( ( filter ) => {
				this.initFilter( filter );
			} );
		}
	}

	/**
	 * Initialize filter events
	 *
	 * @param {Object} filter
	 */
	initFilter( filter ) {
		const data = {};
		data.type = filter.dataset.type;
		data.inputs = filter.querySelectorAll( 'input, select' );
		data.group = filter.closest( '.sf-filter-group' );

		this.filters.push( data );
		this.setupFilterEvents( data );
	}

	/**
	 * Setup events for input fields in the filter
	 *
	 * @param {Object} filter
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
					group: filter.group,
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

	/**
	 * Make AJAX GET request with filtered URL
	 * replace page elements with updated content
	 * and re-initiate necessary events
	 */
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
			// this.updateOptionCounters( content );
			this.updateFilterOptions( content );
			this.updateSelectOptions( content );
			this.updateRangeSliders( content );
		} );

		addLoader( document.body );
	}

	/**
	 * Replace page fragments with new content
	 *
	 * @param {Object} content
	 */
	updatePageFragments( content ) {
		let selectors = [
			'.products',
			'.woocommerce-pagination',
			'.woocommerce-breadcrumb',
			'.woocommerce-result-count',
			'.woocommerce-ordering',
			'.woocommerce-products-header__title',
		];

		// Get user entered selectors if they have been change
		if ( sf_filters.selectors ) {
			selectors = Object.values( sf_filters.selectors ).filter( ( selector ) => selector !== '' );
		}

		selectors.forEach( ( selector ) => {
			const home = document.querySelectorAll( selector );
			const ext = content.querySelectorAll( selector );

			// Both elements are present in both trees in equal quantity
			if ( home.length > 0 && home.length === ext.length ) {
				home.forEach( ( ele, index ) => {
					ele.replaceWith( ext[ index ] );
				} );
				return;
			}

			// Content is present only on new version
			if ( home.length === 0 && ext.length > 0 ) {
				ext.forEach( ( ele ) => {
					// Find relative path of the content in current document
					// to place it in correct location
					// debugger;

					const location = this.getRelativeDOMPosition( ext, ele );
					if ( location.selector ) {
						let root = document.querySelector( location.selector );

						// Traverse children according to queried order
						while ( location.index.length > 1 ) {
							const move = root.children[ location.index.shift() ];
							if ( move ) {
								root = move;
							}
						}

						// Insert on specified index
						root.insertBefore( ele, root.children[ location.index.shift() ] );
					}
				} );
				return;
			}

			// Content is present only on current content
			if ( home.length > 0 && ext.length === 0 ) {
				home.forEach( ( ele ) => {
					ele.remove();
				} );
			}
		} );

		removeLoader();
	}

	/**
	 * Update list of options in filters
	 *
	 * @param {Object} content
	 */
	updateFilterOptions( content ) {
		// Get new filters
		const update = {};
		content.querySelectorAll( '.sf-filter' ).forEach( ( filter ) => {
			update[ filter.dataset.id ] = filter;
		} );

		document.querySelectorAll( '.sf-filter' ).forEach( ( filter ) => {
			if ( update.hasOwnProperty( filter.dataset.id ) ) {
				let list = filter.querySelector( '.sf-option-list' );
				let newContent = update[ filter.dataset.id ].querySelector( '.sf-option-list' );
				if ( list && newContent ) {
					list = list.closest( 'div' );
					newContent = newContent.closest( 'div' );

					// Keep list opened if it was open
					if ( list.querySelector( '.sf-more-btn--open' ) ) {
						newContent.querySelector( '.sf-more-btn' ).classList.add( 'sf-more-btn--open' );
					}

					// Replace updated content
					list.replaceWith( newContent );
					this.initFilter( filter );
				}
			}
		} );

		// Reinstate more buttons
		this.setupMoreButtons();
	}

	updateSelectOptions( content ) {
		const select = document.querySelectorAll( '.sf-filter .sf-select__input' );
		const replace = content.querySelectorAll( '.sf-filter .sf-select__input' );

		const values = {};
		replace.forEach( ( ele ) => {
			values[ ele.getAttribute( 'id' ) ] = ele.innerHTML;
		} );

		select.forEach( ( ele ) => {
			if ( values.hasOwnProperty( ele.getAttribute( 'id' ) ) ) {
				ele.innerHTML = values[ ele.getAttribute( 'id' ) ];
			}
		} );
	}

	/**
	 * Find option count values in filters and replace them with data from new content
	 *
	 * @param {Object} content
	 */
	updateOptionCounters( content ) {
		const options = document.querySelectorAll( '.sf-filter .sf-label-count' );
		const replace = content.querySelectorAll( '.sf-filter .sf-label-count' );

		const values = {};
		replace.forEach( ( label ) => {
			values[ label.parentNode.getAttribute( 'for' ) ] = label.innerHTML.trim();
		} );

		options.forEach( ( label ) => {
			const label_id = label.parentNode.getAttribute( 'for' );
			if ( values.hasOwnProperty( label_id ) ) {
				label.innerHTML = ' ' + values[ label_id ];
			}
		} );
	}

	/**
	 * Replace and re-initiate range slider filter
	 *
	 * @param {Object} content
	 */
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
	 * Traverse DOM tree up from given element finding node with unique ID
	 * saving relative path to initial element
	 *
	 * @param {Object} doc
	 * @param {Object} ele
	 * @param {Array}  location
	 */
	getRelativeDOMPosition( doc, ele, location = [] ) {
		location.push( Array.from( ele.parentNode.children ).indexOf( ele ) );

		if ( ele.parentElement.id ) {
			const parent = document.querySelectorAll( '#' + ele.parentNode.id );
			if ( parent.length === 1 ) {
				return {
					selector: '#' + ele.parentNode.id,
					index: location.reverse(),
				};
			}
		} else {
			return this.getRelativeDOMPosition( doc, ele.parentElement, location );
		}
	}

	/**
	 * Setup events for show more options button
	 */
	setupMoreButtons() {
		document.querySelectorAll( '.sf-filter .sf-more-btn' ).forEach( ( button ) => {
			const filter = button.closest( '.sf-filter' );
			const options = filter.querySelectorAll( '.sf-option-more' );
			const label = button.innerHTML;

			const list = filter.querySelector( '.sf-option-list' );
			const initial_height = list.offsetHeight;

			button.addEventListener( 'click', () => {
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
					button.innerHTML = __( 'Show less', 'simply-filters' );
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

			// Open list initially
			if ( button.classList.contains( 'sf-more-btn--open' ) ) {
				button.innerHTML = __( 'Show less', 'simply-filters' );
				button.ariaExpanded = true;
				options.forEach( ( option ) => {
					option.classList.remove( 'sf-option-more' );
				} );
				list.style.height = '';
			}
		} );
	}

	/**
	 * Setup events for collapse filter button
	 */
	setupCollapseButtons() {
		document.querySelectorAll( '.sf-filter .sf-filter__collapse' ).forEach( ( button ) => {
			const filter = button.closest( '.sf-filter' );
			const options = filter.querySelector( '.sf-filter__filter' );

			button.addEventListener( 'click', () => {
				if ( options.classList.contains( 'sf-filter--collapsed' ) ) {
					// Open options
					button.classList.remove( 'collapsed' );
					options.classList.remove( 'sf-filter--collapsed' );
					options.style.overflow = 'hidden';

					const target_height = options.offsetHeight;
					options.style.height = '0px';

					setTimeout( () => {
						options.style.height = target_height + 'px';
					}, 0 );
					setTimeout( () => {
						options.style.height = '';
						options.style.overflow = '';
					}, 200 );

					this.updateCollapseCookie( filter.dataset.id, false );
				} else {
					// Close options
					button.classList.add( 'collapsed' );
					options.style.height = options.offsetHeight + 'px';
					options.style.overflow = 'hidden';

					setTimeout( () => {
						options.style.height = '0px';
					}, 0 );
					setTimeout( () => {
						options.style.height = '';
						options.classList.add( 'sf-filter--collapsed' );
						options.style.overflow = '';
					}, 200 );

					this.updateCollapseCookie( filter.dataset.id, true );
				}
			} );
		} );
	}

	/**
	 * Update filter collapse cookie
	 *
	 * @param {number} id
	 * @param {string} value
	 */
	updateCollapseCookie( id, value ) {
		const cookie_name = 'sf-filters-collapsed';
		const cookie = getCookie( cookie_name );

		if ( typeof cookie === 'undefined' ) {
			if ( value ) {
				setCookie( cookie_name, id, 7 );
			}
		} else {
			let cookies = cookie.split( '|' );
			if ( ! cookies.includes( id ) && value ) {
				// Add id to cookie
				cookies.push( id );
				setCookie( cookie_name, cookies.join( '|' ), 7 );
			} else if ( cookies.includes( id ) && ! value ) {
				// Remove id from cookie
				cookies = cookies.filter( ( item ) => item !== id );
				setCookie( cookie_name, cookies.join( '|' ), 7 );
			}
		}
	}

	/**
	 * Setup events for submit button
	 */
	setupSubmitButtons() {
		document.querySelectorAll( '.sf-filter-group__submit' ).forEach( ( submit ) => {
			submit.addEventListener( 'click', () => {
				const url = this.url.getUpdatedURL();
				const event = new Event( 'sf-filter-products' );
				window.dispatchEvent( event );
				window.history.pushState( {}, '', url );
			} );
		} );
	}

	/**
	 * Setup events for clear all button
	 */
	setupClearButtons() {
		// Clear filters on click
		document.querySelectorAll( '.sf-filter-group__clear' ).forEach( ( clear ) => {
			clear.addEventListener( 'click', () => {
				// Checkbox
				document.querySelectorAll( '.sf-filter input[type="checkbox"]' ).forEach( ( input ) => input.checked = false );

				// Radio
				document.querySelectorAll( '.sf-filter .sf-radio__list > li:first-of-type input[type="radio"]' ).forEach( ( input ) => input.checked = true );

				// Select
				document.querySelectorAll( '.sf-filter .sf-select__input' ).forEach( ( select ) => select.selectIndex = 0 );

				// Apply filter
				window.history.pushState( {}, '', this.url.url.origin + this.url.url.pathname );
				window.dispatchEvent( new Event( 'sf-filter-products' ) );
			} );
		} );
	}
}
