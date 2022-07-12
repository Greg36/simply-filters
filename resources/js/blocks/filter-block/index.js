/* eslint-disable no-unused-vars */
/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';
import { registerBlockType } from '@wordpress/blocks';

/**
 * Internal dependencies
 */
import Block from './block';

/**
 * Filter group WordPress Block definition
 *
 * @since 1.0.0
 */
registerBlockType( 'simply-filters/filter-group', {
	title: __( 'SF Filter Group', 'simply-filters' ),
	description: __( 'Simply add product\'s price, category and attribute filters to WooCommerce.', 'simply-filters' ),
	icon: 'filter',
	category: 'widgets',

	example: {
		attributes: {
			isPreview: true,
		},
	},

	supports: {
		html: false,
	},

	attributes: {
		group_id: {
			type: 'integer',
			default: 0,
		},
		isSelectGroup: {
			type: 'boolean',
			default: true,
		},
		isPreview: {
			type: 'boolean',
			default: false,
		},
	},

	edit( props ) {
		return <Block {...props} />;
	},

	save() {
		return null;
	},
} );
