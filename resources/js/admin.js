/**
 * Primary admin panel script.
 *
 * @package   SimplyFilters
 */

import { initFiltersGroup } from "./lib/admin-filters-group";

document.addEventListener( 'DOMContentLoaded', () => {

	if( sf_admin.current_screen.is_page_post ) {
		initFiltersGroup();
	}

});