/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';
import { registerBlockType } from '@wordpress/blocks';

/**
 * Local dependencies
 */


registerBlockType( 'simply-filters/filter-group', {
	title: __( 'SF Filter Group xdd', 'simply-filters' ),
	description: __( 'Description', 'simply-filters' ),
	icon: 'format-image',
	category: 'widgets',

	attributes: {
		group_id: {
			type: 'integer'
		}
	},

	edit() {
		return <div>something else</div>
	},

	save() {

	}
} );