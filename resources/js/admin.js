/**
 * Primary admin panel script.
 *
 * @package   SimplyFilters
 */

import AdminFilters from './lib/admin-filters.js';
import AdminNewFilter from "./lib/admin-new-filter";


document.addEventListener( 'DOMContentLoaded', () => {
	let adminFilters = new AdminFilters();
	let adminNewFilter = new AdminNewFilter();

	adminFilters.init();
	adminNewFilter.init();
});