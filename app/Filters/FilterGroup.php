<?php

namespace SimplyFilters\Filters;

use SimplyFilters\Admin\GroupSettings;
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

	/**
	 * @var GroupSettings
	 */
	private $settings;

	public function __construct( $post_id ) {

		$this->post_id = $post_id;
		$this->settings = new GroupSettings( $post_id );

		$this->query_filters_data();
	}

	/**
	 * Query all filters in current group
	 *
	 * @return void
	 */
	private function query_filters_data() {

		// Check filters data for current group
		if ( $this->post_id !== false && empty( $this->filters ) ) {

			// Query filters
			$filters = get_posts(
				array(
					'posts_per_page'   => - 1,
					'post_type'        => \Hybrid\app( 'item_post_type' ),
					'orderby'          => 'menu_order',
					'order'            => 'ASC',
					'suppress_filters' => true,
					'post_parent'      => $this->post_id,
					'post_status'      => array( 'publish', 'trash' ),
				)
			);

			$this->filters = $filters;
		}
	}

	/**
	 * Build all filters
	 *
	 * @return array
	 */
	public function get_filters() {

		$filters = [];

		if ( ! empty( $this->filters ) ) {
			foreach ( $this->filters as $filter ) {
				$filters[] = FilterFactory::build( $filter );
			}
		}

		return $filters;
	}

	public function get_settings() {
		$settings = new GroupSettings( $this->post_id );
		return $settings->get_data();
	}

	/**
	 * Render front-end markup of filter group
	 *
	 * @return void
	 */
	public function render() {
		TemplateLoader::render( 'filter-group', [
			'group_id' => $this->post_id,
			'filters' => $this->get_filters(),
			'settings' => $this->get_settings(),
		],
			'Filters'
		);
	}
}