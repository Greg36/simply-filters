<?php

namespace SimplyFilters\Admin;

use SimplyFilters\Filters\FilterGroup;
use SimplyFilters\TemplateLoader;

/**
 * Setup and display admin metaboxes
 *
 * @since 1.0.0
 */
class Metaboxes {

	private $group_id;

	public function __construct( $group_id ) {
		$this->group_id = $group_id;
	}

	/**
	 * Initialize the group edit, info and new filter metaboxes
	 */
	public function init_metaboxes() {

		// Group fields
		add_meta_box( 'sf-filter-group-fields',
			esc_html__( 'Edit Filters', \Hybrid\app( 'locale' ) ),
			[ $this, 'filter_group_metabox' ],
			'sf_filter_group',
			'normal',
			'high'
		);

		// Setup info
		add_meta_box( 'sf-filter-place',
			esc_html__( 'Setup filters', \Hybrid\app( 'locale' ) ),
			[ $this, 'info_metabox' ],
			'sf_filter_group',
			'side',
			'low'
		);

		// New filter popup
		add_action( 'in_admin_header', [ $this, 'render_new_filter_popup' ] );
	}

	/**
	 * Render main filter group metabox
	 */
	public function filter_group_metabox() {

		$filter_group = new FilterGroup( get_post( $this->group_id ) );

		// Top tab navigation
		TemplateLoader::render( 'filter-tabs' );

		// Filter settings
		TemplateLoader::render( 'filter-group-fields', [
			'filters' => $filter_group->get_filters()
		] );

		// Group settings
		TemplateLoader::render( 'filter-group-settings', [
			'settings' => $filter_group->get_settings()
		] );
	}

	/**
	 * Render setup info
	 */
	public function info_metabox() {
		TemplateLoader::render( 'filter-info' );
	}

	/**
	 * Render new filter popup
	 */
	public function render_new_filter_popup() {
		TemplateLoader::render( 'filter-new-popup', [
			'registry' => \Hybrid\app( 'filter_registry' )
		] );
	}
}