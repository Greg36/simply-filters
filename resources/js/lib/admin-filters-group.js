/**
 * Filters edit screen
 *
 * @package   SimplyFilters
 */

import AdminFilter from "./admin-filter";
import AdminNewFilter from "./admin-new-filter";
import { invalidInputNotice, addFormNotice } from "./helpers";

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
function prepareSubmitData( e ) {

	// Invalidate submit if required values are not unique
	const duplicates = findDuplicatedValues();
	if( duplicates ) {
		e.preventDefault();
		addFormNotice( duplicates + ' ' + sf_admin.locale.unique_notice, 'error' );
		return;
	}

	removeUnmodifiedFilters();
}

/**
 * Check if all values labeled as unique are so between all filters
 */
function findDuplicatedValues() {
	const inputs = document.getElementById( 'post' ).querySelectorAll( '[data-unique]' );

	let uniques = {},
		found_duplicates = 0;

	// For each unique label create a new array and push the input into it
	inputs.forEach( ( current ) => {
		(uniques[current.dataset.unique] || (uniques[current.dataset.unique] = [])).push( current );
	} );

	// Check if values in group are all unique
	Object.keys( uniques ).forEach( ( key ) => {

		const values = uniques[key].map(el => el.value);
		const duplicates = uniques[key].filter(el => values.filter(val => val === el.value).length > 1);

		// If there are duplicates display notice
		duplicates.forEach( ( input ) => {
			found_duplicates++;
			invalidInputNotice( sf_admin.locale.unique_field, input );

			// Open settings
			if( ! input.closest( '.sf-filter' ).classList.contains( 'open' ) ) {
				input.closest( '.sf-filter' ).querySelector( 'a.edit-filter' ).click();
			}
		} );
	} );

	return found_duplicates;
}

function validateRequiredFields() {

}

/**
 * Remove all input fields of filters that have not been modified
 */
function removeUnmodifiedFilters() {
	document.querySelectorAll( '.sf-filter' ).forEach( ( current ) => {

		// Remove all fields that have not been changed
		if ( !current.hasAttribute( 'data-save' ) || current.dataset.save !== 'true' ) {
			current.querySelectorAll( `[name^="${sf_admin.prefix}"]` ).forEach( input => {
				input.remove();
			} );
		}
	} );
}