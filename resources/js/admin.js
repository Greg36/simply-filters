/**
 * Primary admin panel script.
 *
 * @package   SimplyFilters
 */

import AdminFilters from './lib/admin-filters.js';


document.addEventListener( 'DOMContentLoaded', () => {
	var adminFilters = new AdminFilters();
	adminFilters.init();
});