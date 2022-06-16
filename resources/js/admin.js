/**
 * Primary admin panel script.
 *
 * @package   SimplyFilters
 */

import { initFiltersGroup } from "./admin/admin-filters-group";

document.addEventListener( 'DOMContentLoaded', () => {

	// Post edit screen
	if( sf_admin.current_screen.is_page_post ) {
		initFiltersGroup();
		enableTabsToggle( document.querySelector( '.sf-tabs' ) );
	}

	// General settings screen
	if( sf_admin.current_screen.is_page_settings ) {

		// Initialize color settings
		jQuery( '.sf-settings .sf-color__field' ).wpColorPicker({
			hide: true,
			palettes: true,
		} );


		// Collapse selector options on toggle
		const change_selectors = document.querySelector( '#sf-setting-options-change_selectors' );
		const selectors = document.querySelector( '#sf-setting-options-selectors_product' ).closest( '.sf-option' );

		if( ! change_selectors.checked ) selectors.style.display = 'none';
		change_selectors.addEventListener( 'change', ( e ) => {
			selectors.style.display = e.target.checked ? '' : 'none';
		} );

	}

});

function enableTabsToggle( tabs ) {
	const all_targets = document.querySelectorAll( '.sf-tabs-target' );

	tabs.querySelectorAll( 'a' ).forEach( ( link ) => {
		link.addEventListener( 'click', (e) => {
			e.preventDefault();

			// Swap the tab
			const target = document.querySelector( link.getAttribute( 'href' ) );
			all_targets.forEach( (ele) => {
				if( ele !== target ) ele.classList.remove( 'open' );
			} );
			target.classList.add( 'open' );

			// Change active tab link
			tabs.querySelector( '.sf-tabs__link.active' ).classList.remove( 'active' );
			link.classList.add( 'active' );
		} );
	} );
}