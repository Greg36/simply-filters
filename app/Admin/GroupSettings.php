<?php

namespace SimplyFilters\Admin;

use SimplyFilters\Filters\FilterGroup;
use SimplyFilters\TemplateLoader;

class GroupSettings {

	private $group_id;

	/**
	 * @var Settings
	 */
	private $settings;

	public function __construct( $group_id ) {
		$this->group_id = $group_id;
		$this->register_group_settings();
	}

	public function init_admin() {
		$this->filter_group = new FilterGroup( $this->group_id );

		$this->init_metaboxes();
		add_action( 'in_admin_header', [ $this, 'render_new_filter_popup' ] );
	}

	private function register_group_settings() {
		$locale = \Hybrid\app( 'locale' );

		$settings = new Settings( $this->group_id, (array) maybe_unserialize( get_post_field( 'post_content', $this->group_id ) ) );

		$settings->add( 'elements', 'checkbox', [
			'name'        => __( 'Enable elements', $locale ),
			'description' => __( 'Elements that should be visible in this filter group', $locale ),
			'options'     => [
				'title' => __( '<strong>Group title</strong> - show group title above filters', $locale ),
				'clear' => __( '<strong>Clear all button</strong> - reset options button to clear all selected values', $locale ),
				'empty' => __( '<strong>Empty options</strong> - display options without any products assigned to them', $locale ),
			]
		] );

		$settings->add( 'auto_submit', 'radio', [
			'name'        => __( 'Filtering start', $locale ),
			'description' => __( 'When should filtering of products start', $locale ),
			'options'     => [
				'automatic' => __( '<strong>Automatically</strong> - when any of the filters are changed', $locale ),
				'onsubmit'  => __( '<strong>On submit</strong> - when user presses the Filter button', $locale )
			]
		] );

		$settings->add( 'more_show', 'toggle', [
			'name' => __( 'More options button' , $locale ),
			'description' => __( 'Enable to limit how many options are shown at once before "Show more" button appears' )
		] );

		$settings->add( 'more_count', 'number', [
			'name' => __( 'Number of options to show', $locale ),
			'description' => __( 'How many options should be displayed initially', $locale ),
			'default' => 5
		] );
		
		$settings->add( 'collapse', 'toggle', [
			'name' => __( 'Collapse filter button', $locale ),
			'description' => __( 'Display arrow that will allow to collapse filter to only its title. <strong style="color: #b15252;">NOTICE:</strong> this will save a cookie to remember collapsed filters.', $locale )
		]);

		$this->settings = $settings;
	}

	/**
	 * Get array with all the settings data
	 *
	 * @return array
	 */
	public function get_data() {
		return $this->settings->get_data();
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

		add_meta_box( 'sf-filter-place',
			__( 'Setup filters', \Hybrid\app( 'locale' ) ), // @todo change this label
			[ $this, 'place_metabox' ],
			'sf_filter_group',
			'side',
			'low'
		);
	}

	public function filters_metabox() {
		TemplateLoader::render( 'filter-tabs' );
		TemplateLoader::render( 'filter-group-fields', [
			'filters' => $this->filter_group->get_filters()
		] );
		TemplateLoader::render( 'filter-group-settings', [
			'settings' => $this->settings
		] );
	}

	public function place_metabox() {
		TemplateLoader::render( 'filter-group-place' );
	}

	public function render_new_filter_popup() {
		TemplateLoader::render( 'filter-new-popup', [
			'registry' => \Hybrid\app( 'filter_registry' )
		] );
	}
}