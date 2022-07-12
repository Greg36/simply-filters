import { initFiltersGroup } from './admin/admin-filters-group';

/**
 * Primary admin panel script.
 *
 * @since 1.0.0
 */
document.addEventListener( 'DOMContentLoaded', () => {
	// Post edit screen
	if ( sf_admin.current_screen.is_page_post ) {
		initFiltersGroup();
		enableTabsToggle( document.querySelector( '.sf-tabs' ) );
	}

	// General settings screen
	if ( sf_admin.current_screen.is_page_settings ) {
		// Initialize color settings
		jQuery( '.sf-settings .sf-color__field' ).wpColorPicker( {
			hide: true,
			palettes: true,
		} );

		// Collapse selector options on toggle
		const changeSelectors = document.querySelector( '#sf-setting-options-change_selectors' );
		const selectors = document.querySelector( '#sf-setting-options-selectors_product' ).closest( '.sf-option' );

		if ( ! changeSelectors.checked ) {
			selectors.style.display = 'none';
		}
		changeSelectors.addEventListener( 'change', ( e ) => {
			selectors.style.display = e.target.checked ? '' : 'none';
		} );
	}
} );

/**
 * Handle tab navigation on filter group page
 *
 * @param {Object} tabs
 */
function enableTabsToggle( tabs ) {
	const allTargets = document.querySelectorAll( '.sf-tabs-target' );

	tabs.querySelectorAll( 'a' ).forEach( ( link ) => {
		link.addEventListener( 'click', ( e ) => {
			e.preventDefault();

			// Swap the tab
			const target = document.querySelector( link.getAttribute( 'href' ) );
			allTargets.forEach( ( ele ) => {
				if ( ele !== target ) {
					ele.classList.remove( 'open' );
				}
			} );
			target.classList.add( 'open' );

			// Change active tab link
			tabs.querySelector( '.sf-tabs__link.active' ).classList.remove( 'active' );
			link.classList.add( 'active' );
		} );
	} );
}
