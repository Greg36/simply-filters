<?php

namespace SimplyFilters\Filters;

use SimplyFilters\Admin\Settings;
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
	 * @var array All registered filters
	 */
	private $registry = [];

	/**
	 * @var
	 */
	private $settings;

	public function __construct( $post_id ) {

		$this->post_id = $post_id;

		$this->register_filters();
		$this->register_group_settings();
		$this->prepare_filters_data();
		$this->init_metaboxes();

		add_action( 'in_admin_header', [ $this, 'render_new_filter_popup' ] );
	}

	/**
	 * Register available filters
	 */
	private function register_filters() {
		// @todo move to Filter service provider?
		$this->registry['Checkbox'] = Types\CheckboxFilter::class;
		$this->registry['Radio']    = Types\RadioFilter::class;
		$this->registry['Select']   = Types\SelectFilter::class;
		$this->registry['Color']    = Types\ColorFilter::class;
		$this->registry['Rating']   = Types\RatingFilter::class;
		$this->registry['Slider']   = Types\SliderFilter::class;
	}

	/**
	 * Return array of all registerd filters
	 */
	private function get_registered_filters() {
		return $this->registry;
	}

	/**
	 * Query all filters in current group
	 */
	private function prepare_filters_data() {

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

	public function get_filters() {

		$filters = [];

		if ( ! empty( $this->filters ) ) {
			foreach ( $this->filters as $filter ) {
				$filters[] = FilterFactory::build( $filter );
			}
		}

		return $filters;
	}

	private function register_group_settings() {
		$locale = \Hybrid\app('locale');

		$settings = new Settings( $this->post_id, (array) maybe_unserialize( get_the_content() ) );

		$settings->add( 'elements', 'checkbox', [
			'name'        => __( 'Enable elements', $locale ),
			'description' => __( 'Elements that should be visible in this filter group', $locale ),
			'options' => [
				'title' => __( '<strong>Group title</strong> - show group title above filters', $locale ),
				'clear' => __( '<strong>Clear all button</strong> - reset options button to clear all selected values', $locale ),
				'empty' => __( '<strong>Empty options</strong> - display options without any products assigned to them', $locale ),
			]
		] );

		$settings->add( 'auto_submit', 'radio', [
			'name'        => __( 'Filtering start', $locale ),
			'description' => __( 'When should filtering of products start', $locale ),
			'options' => [
				'automatic' => __( '<strong>Automatically</strong> - when any of the filters are changed', $locale ),
				'onsubmit' => __( '<strong>On submit</strong> - when user presses the Filter button', $locale )
			]
		] );

		$this->settings = $settings;
	}

	/**
	 * Initialize the filter edit and group settings metabox
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
	}

	public function filters_metabox() {
		TemplateLoader::render( 'filter-tabs' );
		TemplateLoader::render( 'filter-group-fields', [
			'filters'  => $this->get_filters()
		] );
		TemplateLoader::render( 'filter-group-settings', [
			'settings' => $this->settings
		] );
	}

	public function render_new_filter_popup() {
		TemplateLoader::render( 'filter-new-popup', [
			'registry' => $this->get_registered_filters()
		] );
	}
}