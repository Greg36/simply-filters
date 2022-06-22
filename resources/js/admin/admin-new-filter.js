import { addLoader, removeLoader } from "../lib/helpers";
import { checkNoFilterLabel, updateOrderNumbers } from "./admin-filters-group";
import AdminFilter from "./admin-filter";

/**
 * Handle adding new filter via AJAX
 *
 * @since 1.0.0
 */
export default class AdminNewFilter {

	constructor() {
		this.container = document.querySelector( '.sf-new' );
		this.wrap = document.querySelector( '.sf-new__wrap' );
	}

	/**
	 * Setup events for new filter popup
	 */
	init() {

		// Open and close popup buttons
		document.querySelectorAll( '.sf-button__new-filter, .sf-new__close' ).forEach( button => {
			button.addEventListener( 'click', ( e ) => {
				e.preventDefault();
				this.togglePopup();
			} );
		} );

		// Handle new filter creation
		document.querySelectorAll( '.select-filter' ).forEach( ( btn ) => {
			btn.addEventListener( 'click', ( e ) => {
				e.preventDefault();
				addLoader( this.wrap );
				this.getNewFilter( e.target.dataset.type.trim() );
			} );
		} );
	}

	/**
	 * Toggle visibility of the popup
	 */
	togglePopup() {
		if ( this.container.classList.contains( 'open' ) ) {
			this.container.classList.remove( 'open' );
			setTimeout( () => {
				this.container.style.display = 'none';
			}, 300 );
		} else {
			this.container.style.display = 'block';
			setTimeout( () => {
				this.container.classList.add( 'open' );
			}, 0 );
		}
	}

	/**
	 * Make AJAX request to get new filter
	 */
	getNewFilter( type ) {
		fetch( sf_admin.ajax_url, {
			method: 'POST',
			body: new URLSearchParams( {
				action: 'sf/render_new_field',
				nonceAjax: sf_admin.ajax_nonce,
				type: type
			} ),
		} ).then( ( response ) => {
			return response.text();
		} ).then( ( text ) => {
			this.addNewFilter( text );
		} );
	}

	/**
	 * Insert new filter and initialize it
	 */
	addNewFilter( text ) {

		// Insert new filter
		const last_filter = document.querySelector( '.sf-filters__list > div:last-of-type' );
		last_filter.insertAdjacentHTML( 'afterend', text );

		// Setup new filter
		const filter = new AdminFilter( document.querySelector( '.sf-filters__list > div:last-of-type' ) );
		filter.save();

		// Open new filter
		filter.toggleOptions();

		// Update filter numbers
		updateOrderNumbers();

		// Close the new filter popup
		this.togglePopup();
		removeLoader();

		checkNoFilterLabel();
	}
}