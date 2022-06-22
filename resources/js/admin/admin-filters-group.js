import { __ } from '@wordpress/i18n';
import AdminFilter from "./admin-filter";
import AdminNewFilter from "./admin-new-filter";
import { invalidInputNotice, addAdminFormNotice } from "../lib/helpers";

export { initFiltersGroup, updateOrderNumbers, checkNoFilterLabel };

/**
 * Initialize group filter and settings
 *
 * @since 1.0.0
 */
function initFiltersGroup() {

	// Initialize all filters
	document.querySelectorAll( '.sf-filter' ).forEach( ( current ) => {
		new AdminFilter( current );
	} );

	// Setup group settings
	setupGroupSettings();

	// Setup color inputs for group settings
	setupColorInputs();

	// Make filter rows sortable
	makeRowsSortable();

	// Verify inputs before form submit
	document.querySelector( '#post' ).addEventListener( 'submit', prepareSubmitData );

	// Setup new filter popup
	const new_filter_popup = new AdminNewFilter();
	new_filter_popup.init();
}

/**
 * Setup events related to filter group settings
 */
function setupGroupSettings() {

	const group_settings = document.querySelector( '.sf-settings' );

	if ( group_settings ) {
		const group_id = group_settings.dataset.filter_group_id,
			more_toggle = group_settings.querySelector( '#' + group_id + '-' +
				'more_show' ),
			more_count = group_settings.querySelector( '#' + group_id + '-more_count' ).closest( '.sf-option' );

		// Hide more count setting initially
		if ( !more_toggle.checked ) more_count.style.display = 'none';

		more_toggle.addEventListener( 'change', ( e ) => {
			more_count.style.display = e.target.checked ? '' : 'none';
		} );
	}
}

/**
 * Setup WP Color Picker for all color inputs
 */
function setupColorInputs() {
	jQuery( '.sf-settings .sf-color__field' ).wpColorPicker( {
		defaultColor: false,
		hide: true,
		palettes: true,
	} );

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

	if ( document.querySelectorAll( '.sf-filter' ).length ) {
		noFilter.style.display = 'none';
	} else {
		noFilter.style.display = 'block';
	}
}

/**
 * Prepare data before submitting
 */
function prepareSubmitData( e ) {
	const duplicates = findDuplicatedValues();

	if ( duplicates ) {
		// Invalidate submit if required values are not unique
		e.preventDefault();
		addAdminFormNotice( duplicates + ' ' + __( 'values are not unique.', 'simply-filters' ), 'error' );
		return;
	}

	removeUnmodifiedFilters();
}

/**
 * Check if all values labeled as unique are unique between all filters settings
 *
 * @returns {number}
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

		const values = uniques[key].map( el => el.value );
		const duplicates = uniques[key].filter( el => values.filter( val => val === el.value ).length > 1 );

		// If there are duplicates display notice
		duplicates.forEach( ( input ) => {
			found_duplicates++;
			invalidInputNotice( __( 'Value must be unique across all filters in the group', 'simply-filters' ), input );

			// Open settings
			if ( !input.closest( '.sf-filter' ).classList.contains( 'open' ) ) {
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

		// Skip open filters
		if ( current.classList.contains( 'open' ) ) return;

		// Remove all fields that have not been changed
		if ( !current.hasAttribute( 'data-save' ) || current.dataset.save !== 'true' ) {

			// Save checked toggle
			const enabled = current.querySelector( '[name$="[enabled]"]' );
			if ( enabled.checked ) enabled.parentElement.classList.add( 'checked' );

			current.querySelectorAll( `[name^="${sf_admin.prefix}"]` ).forEach( input => {
				input.remove();
			} );
		}
	} );
}

