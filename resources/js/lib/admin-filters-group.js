/**
 * Filters edit screen
 *
 * @package   SimplyFilters
 */

import AdminFilter from "./admin-filter";
import AdminNewFilter from "./admin-new-filter";

export { initFiltersGroup, updateOrderNumbers };

function initFiltersGroup() {

	// Setup all filters
	document.querySelectorAll( '.sf-filter' ).forEach( ( current ) => {
		new AdminFilter( current );
	} );

	// Make filter rows sortable
	makeRowsSortable();

	// Before form submit
	document.querySelector( '#post' ).addEventListener( 'submit', prepareSubmitData );

	// Setup new filter popup
	new AdminNewFilter().init();
}

/**
 * Enable rows to be sortable with jQuery UI Sortable
 */
function makeRowsSortable() {
	jQuery( '.sf-filters__list' ).sortable( {
		handle: '.sf-row__order',
		stop: () => {
			updateOrderNumbers()
		}
	} );
}

/**
 * Update filter order numbers in UI and in attribute
 */
function updateOrderNumbers() {
	const numbers = document.querySelectorAll( '.sf-row__order' );
	numbers.forEach( ( current, index ) => {
		current.innerText = index + 1;
		current.closest( '.sf-filter' ).dispatchEvent( new CustomEvent( 'orderChanged', { detail: index } ) );
	} );
}

/**
 * Prepare data before submitting
 */
function prepareSubmitData() {
	document.querySelectorAll( '.sf-filter' ).forEach( ( current ) => {

		// Remove all fields that have not been changed
		if ( ! current.hasAttribute( 'data-save' ) || current.dataset.save !== 'true' ) {
			current.querySelectorAll( `[name^="${sf_admin.prefix}"]` ).forEach( input => {
				input.remove();
			} );
		}
	} );
}