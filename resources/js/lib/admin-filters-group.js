/**
 * Filters edit screen
 *
 * @package   SimplyFilters
 */

import { __ } from '@wordpress/i18n';
import AdminFilter from "./admin-filter";
import AdminNewFilter from "./admin-new-filter";
import { invalidInputNotice, addFormNotice } from "./helpers";

export { initFiltersGroup, updateOrderNumbers, checkNoFilterLabel };

function initFiltersGroup() {

	// Setup all filters
	document.querySelectorAll( '.sf-filter' ).forEach( ( current ) => {
		new AdminFilter( current );
	} );

	// Setup color inputs for group settings
	jQuery( '.sf-settings .sf-color__field' ).wpColorPicker({
		defaultColor: false,
		hide: true,
		palettes: true,
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
 * Check if "there are no filters" label should be displayed
 */
function checkNoFilterLabel() {
	const noFilter = document.querySelector( '.sf-filters__no-items' );

	if( document.querySelectorAll( '.sf-filter' ).length ) {
		noFilter.style.display = 'none';
	} else {
		noFilter.style.display = 'block';
	}
}

/**
 * Prepare data before submitting
 */
function prepareSubmitData( e ) {

	// Invalidate submit if required values are not unique
	const duplicates = findDuplicatedValues();
	if( duplicates ) {
		e.preventDefault();
		addFormNotice( duplicates + ' ' + __( 'values are not unique.', 'simply-filters' ), 'error' );
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
			invalidInputNotice( __( 'Value must be unique across all filters in the group', 'simply-filters' ), input );

			// Open settings
			if( ! input.closest( '.sf-filter' ).classList.contains( 'open' ) ) {
				input.closest( '.sf-filter' ).querySelector( 'a.edit-filter' ).click();
			}
		} );
	} );

	return found_duplicates;
}

/**
 * Remove all input fields of filters that have not been modified
 */
function removeUnmodifiedFilters() {
	document.querySelectorAll( '.sf-filter' ).forEach( ( current ) => {

		// Remove all fields that have not been changed
		if ( !current.hasAttribute( 'data-save' ) || current.dataset.save !== 'true' ) {

			// Save checked toggle
			const enabled = current.querySelector( '[name$="[enabled]"]' );
			if( enabled.checked ) enabled.parentElement.classList.add( 'checked' );

			current.querySelectorAll( `[name^="${sf_admin.prefix}"]` ).forEach( input => {
				input.remove();
			} );
		}
	} );
}