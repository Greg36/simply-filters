/**
 * Filters edit screen
 *
 * @package   SimplyFilters
 */

import ColorControl from './admin-color';

export default class AdminFilters {

	init() {
		const colorControl = new ColorControl( '.sf-color__field' );
		colorControl.init();
	}
}

