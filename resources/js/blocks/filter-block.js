/**
 * WordPress dependencies
 */
// import { registerBlockType } from '@wordpress/blocks';

const { registerBlockType } = wp.blocks;

/**
 *
 */
registerBlockType( 'simply-filters/filter-group', {

	title: sf_filter_block.locale.block_title,
	description: sf_filter_block.locale.block_desc,
	icon: 'format-image',
	category: 'widgets',

	attributes: {},

	edit() {

	},

	save() {

	}
} );