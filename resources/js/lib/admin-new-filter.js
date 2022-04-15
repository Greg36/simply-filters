import AdminFilters from "./admin-filters";
import { addLoader, removeLoader } from "./helpers";

export default class AdminNewFilter {

	constructor() {
		this.container = document.querySelector( '.sf-new' );
		this.newBtn = document.querySelector( '.sf-button__new-filter' );
		this.closeBtn = document.querySelector( '.sf-new__close' );
		this.wrap = document.querySelector( '.sf-new__wrap' );
	}


	init() {
		this.newBtn.addEventListener( 'click', ( e ) => {
			e.preventDefault();
			this.togglePopup();
		} );

		this.closeBtn.addEventListener( 'click', ( e ) => {
			e.preventDefault();
			this.togglePopup();
		} );

		// Handle new filter creation
		const newFilterBtn = document.querySelectorAll( '.select-filter' );
		newFilterBtn.forEach( ( btn ) => {
			btn.addEventListener( 'click', ( e ) => {
				e.preventDefault();
				addLoader( this.wrap );
				this.getNewFilter( e.target.dataset.type.trim() );
			} );
		} );
	}

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

	addNewFilter( html ) {

		// Insert new filter
		let last_filter = document.querySelector( '.sf-filters__list > div:last-of-type' );
		last_filter.insertAdjacentHTML( 'afterend', html );

		// Setup events for new filter
		const admin_filter = new AdminFilters();
		let new_row = document.querySelector( '.sf-filters__list > div:last-of-type' );
		admin_filter.setupRowEvents( new_row );

		// Open new filter
		admin_filter.toggleOptions( new_row );

		// Update filter numbers
		admin_filter.updateOrderNumbers();

		// Close the new filter popup
		this.togglePopup();
		removeLoader();
	}

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
}