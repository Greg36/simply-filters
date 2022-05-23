/**
 * Primary admin panel script.
 *
 * @package   SimplyFilters
 */

import { initFiltersGroup } from "./admin/admin-filters-group";

document.addEventListener( 'DOMContentLoaded', () => {

	if( sf_admin.current_screen.is_page_post ) {
		initFiltersGroup();
		enableTabsToggle( document.querySelector( '.sf-tabs' ) );
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