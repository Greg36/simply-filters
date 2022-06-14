<?php

namespace SimplyFilters\Filters;

use SimplyFilters\Admin\Settings;
use SimplyFilters\TemplateLoader;

class FilterGroup {

	/**
	 * @var number|string ID of the filter group post
	 */
	private $group_id;

	/**
	 * @var array All filters in the group
	 */
	private $filters = [];

	/**
	 * @var Settings
	 */
	private $settings;

	public function __construct( $group_id ) {
		$this->group_id  = $group_id;
		$this->settings = new Settings( $group_id, (array) maybe_unserialize( get_post_field( 'post_content', $group_id ) ) );
		$this->register_group_settings();
		$this->query_filters_data();
	}

	/**
	 * Register all settings for the group
	 */
	private function register_group_settings() {
		$locale = \Hybrid\app( 'locale' );

		$this->settings->add( 'elements', 'checkbox', [
			'name'        => __( 'Enable elements', $locale ),
			'description' => __( 'Elements that should be visible in this filter group', $locale ),
			'options'     => [
				'title' => __( '<strong>Group title</strong> - show group title above filters', $locale ),
				'clear' => __( '<strong>Clear all button</strong> - reset options button to clear all selected values', $locale ),
			]
		] );

		$this->settings->add( 'auto_submit', 'radio', [
			'name'        => __( 'Filtering start', $locale ),
			'description' => __( 'When should filtering of products start', $locale ),
			'options'     => [
				'automatic' => __( '<strong>Automatically</strong> - when any of the filters are changed', $locale ),
				'onsubmit'  => __( '<strong>On submit</strong> - when user presses the Filter button', $locale )
			]
		] );

		$this->settings->add( 'more_show', 'toggle', [
			'name' => __( 'More options button' , $locale ),
			'description' => __( 'Enable to limit how many options are shown at once before "Show more" button appears' )
		] );

		$this->settings->add( 'more_count', 'number', [
			'name' => __( 'Number of options to show', $locale ),
			'description' => __( 'How many options should be displayed initially', $locale ),
			'default' => 5
		] );

		$this->settings->add( 'collapse', 'toggle', [
			'name' => __( 'Collapse filter button', $locale ),
			'description' => __( 'Display arrow that will allow to collapse filter to only its title. <strong style="color: #b15252;">NOTICE:</strong> this will save a cookie to remember collapsed filters.', $locale )
		]);
	}

	/**
	 * Get all group settings
	 *
	 * @return Settings
	 */
	public function get_settings() {
		return $this->settings;
	}

	/**
	 * Query all filters in current group
	 *
	 * @return void
	 */
	private function query_filters_data() {

		// Check filters data for current group
		if ( $this->group_id !== false && empty( $this->filters ) ) {

			// Query filters
			$filters = get_posts(
				array(
					'posts_per_page'   => - 1,
					'post_type'        => \Hybrid\app( 'item_post_type' ),
					'orderby'          => 'menu_order',
					'order'            => 'ASC',
					'suppress_filters' => true,
					'post_parent'      => $this->group_id,
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
				$filter_object = FilterFactory::build( $filter );
				$filter_object->set_group( $this );
				$filters[] = $filter_object;
			}
		}

		return $filters;
	}

	/**
	 * Render front-end markup of filter group
	 *
	 * @return void
	 */
	public function render() {

		// @todo: better handle page without woocommerce query
//		if( \WC_Query::get_main_query() === null ) return;

		TemplateLoader::render( 'filter-group', [
			'group_id' => $this->group_id,
			'filters'  => $this->get_filters(),
			'settings' => $this->get_settings()->get_data(),
		],
			'Filters'
		);
	}
}