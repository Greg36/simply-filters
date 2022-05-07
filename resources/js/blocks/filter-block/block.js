/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';
import { Component } from '@wordpress/element';
import { BlockControls } from '@wordpress/block-editor';
import ServerSideRender from '@wordpress/server-side-render';
import {
	Button,
	Disabled,
	Placeholder,
	ToolbarGroup,
	withSpokenMessages,
	SelectControl
} from '@wordpress/components';
import { withSelect } from '@wordpress/data';
import { compose } from '@wordpress/compose';

class FilterBlock extends Component {

	renderGroupSelection() {
		const { attributes, debouncedSpeak, setAttributes } = this.props;

		let groups = [{
			label: __( 'Choose group', 'simply-filters' ),
			value: 0,
			disabled: true
		}];

		if ( this.props.groups ) {
			this.props.groups.forEach( ( post ) => {
				groups.push( {
					label: post.title.rendered,
					value: post.id
				} );
			} );
		}

		const onChange = () => {
			setAttributes( { isSelectGroup: false } );
			debouncedSpeak(
				__(
					'Showing filter group preview.',
					'simply-filters'
				)
			);
		};

		return (
			<Placeholder
				icon='format-image'
				label={__( 'SF Filter Group', 'simply-filters' )}
				className="sf-block__placeholder"
			>
				<SelectControl
					label={__(
						'Select a filters group:',
						'simply-filters'
					)}
					value={attributes.group_id}
					options={groups}
					onChange={( id = 0 ) => {
						setAttributes( { group_id: parseInt( id ) } );
					}}
				/>
				<Button isPrimary onClick={onChange} disabled={!attributes.group_id}>
					{__( 'Done', 'woocommerce' )}
				</Button>

			</Placeholder>
		);

	}

	render() {
		const { attributes, name, setAttributes } = this.props;
		const { isSelectGroup } = attributes;

		if ( attributes.isPreview ) return '';

		return (
			<>
				<BlockControls>
					<ToolbarGroup
						controls={[
							{
								icon: 'edit',
								title: __(
									'Change filter group',
									'simply-filters'
								),
								onClick: () =>
									setAttributes( { isSelectGroup: !isSelectGroup } ),
								isActive: isSelectGroup,
							},
						]}
					/>
				</BlockControls>

				{isSelectGroup ? (
					this.renderGroupSelection()
				) : (
					<Disabled>
						<ServerSideRender
							block={name}
							attributes={attributes}
						/>
					</Disabled>
				)}
			</>
		);
	}
}

export default compose( [
	withSpokenMessages,
	withSelect( ( select ) => {
		return {
			groups: select( 'core' ).getEntityRecords( 'postType', 'sf_filter_group', { per_page: -1 } )
		}
	} ),
] )( FilterBlock );