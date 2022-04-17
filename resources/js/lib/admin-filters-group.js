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
	const rows = document.querySelectorAll( '.sf-filter' );
	rows.forEach( ( current ) => {
		new AdminFilter( current );
	} );

	makeRowsSortable();

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
 * Update numbers at the start of the row to be in order
 */
function updateOrderNumbers() {
	const numbers = document.querySelectorAll( '.sf-row__order' );
	numbers.forEach( ( current, index ) => {
		current.innerText = index + 1;
	} );
}