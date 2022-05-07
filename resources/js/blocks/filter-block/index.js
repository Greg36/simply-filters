/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';
import { registerBlockType } from '@wordpress/blocks';

/**
 * Internal dependencies
 */
import Block from './block';
import ServerSideRender from "@wordpress/server-side-render";

import { Disabled, PanelBody } from '@wordpress/components';


registerBlockType( 'simply-filters/filter-group', {
	title: __( 'SF Filter Group xd', 'simply-filters' ),
	description: __( 'Description', 'simply-filters' ),
	icon: 'format-image',
	category: 'widgets',

	example: {
		attributes: {
			isPreview: true,
		},
	},

	supports: {
		html: false
	},

	attributes: {
		group_id: {
			type: 'integer',
			default: 0
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
	}
} );