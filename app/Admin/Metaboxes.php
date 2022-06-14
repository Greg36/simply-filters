<?php

namespace SimplyFilters\Admin;

use SimplyFilters\Filters\FilterGroup;
use SimplyFilters\TemplateLoader;

class Metaboxes {

	private $group_id;

	public function __construct( $group_id ) {
		$this->group_id = $group_id;
	}

	/**
	 * Initialize the filter edit, group settings and new filter metaboxes
	 *
	 * @since   1.0.0
	 */
	public function init_metaboxes() {

		add_meta_box( 'sf-filter-group-fields',
			__( 'Edit Filters', \Hybrid\app( 'locale' ) ),
			[ $this, 'filters_metabox' ],
			'sf_filter_group',
			'normal',
			'high'
		);

		add_meta_box( 'sf-filter-place',
			__( 'Setup filters', \Hybrid\app( 'locale' ) ), // @todo change this label
			[ $this, 'info_metabox' ],
			'sf_filter_group',
			'side',
			'low'
		);

		add_action( 'in_admin_header', [ $this, 'render_new_filter_popup' ] );
	}

	public function filters_metabox() {

		$filter_group = new FilterGroup( $this->group_id );

		TemplateLoader::render( 'filter-tabs' );

		TemplateLoader::render( 'filter-group-fields', [
			'filters' => $filter_group->get_filters()
		] );

		TemplateLoader::render( 'filter-group-settings', [
			'settings' => $filter_group->get_settings()
		] );
	}

	public function info_metabox() {
		TemplateLoader::render( 'filter-info' );
	}

	public function render_new_filter_popup() {
		TemplateLoader::render( 'filter-new-popup', [
			'registry' => \Hybrid\app( 'filter_registry' )
		] );
	}
}