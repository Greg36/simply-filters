<?php

namespace SimplyFilters\Admin;

use SimplyFilters\Filters\FilterFactory;
use SimplyFilters\TemplateLoader;

class FilterGroup {

	/**
	 * @var number|string ID of the filter group post
	 */
	private $post_id;

	/**
	 * @var array All filters in the group
	 */
	private $filters = [];

	public function __construct( $post_id ) {

		$this->post_id = $post_id;

		$this->prepare_filters_data();
		$this->init_metaboxes();
	}

	/**
	 * Query all filters in current group
	 */
	private function prepare_filters_data() {

		// Check filters data for current group
		if( $this->post_id !== false && empty( $this->filters ) ) {

			// Query filters
			$filters = get_posts(
				array(
					'posts_per_page'         => -1,
					'post_type'              => \Hybrid\app( 'item_post_type' ),
					'orderby'                => 'menu_order', // @todo this needs to be the order in with they are displayed when editing the group
					'order'                  => 'ASC',
					'suppress_filters'       => true,
					'post_parent'            => $this->post_id,
					'post_status'            => array( 'publish', 'trash' ),
				)
			);

			$this->filters = $filters;
		}
	}


	public function get_filters() {

		$filters = [];

		if( !empty( $this->filters ) ) {
			foreach ( $this->filters as $filter ) {
				$filters[] = FilterFactory::build( $filter );
			}
		}

		return $filters;
	}

	/**
	 * Initialize the filter edit and group settings metaboxes
	 *
	 * @since   1.0.0
	 */
	public function init_metaboxes() {

		// Edit filters
		add_meta_box( 'sf-filter-group-fields',
			__( 'Edit Filters', \Hybrid\app( 'locale' ) ),
			[ $this, 'filters_metabox' ],
			'sf_filter_group',
			'normal',
			'high'
		);

		// Group settings
		add_meta_box( 'sf-filter-group-settings',
			__( 'Group Settings', \Hybrid\app( 'locale' ) ),
			[ $this, 'group_metabox' ],
			'sf_filter_group',
			'normal',
			'high'
		);
	}

	public function filters_metabox() {
		TemplateLoader::render( 'filter-group-fields', [
			'filters' => $this->get_filters()
		] );
	}

	public function group_metabox() {
		TemplateLoader::render( 'filter-group-settings' );
	}


}