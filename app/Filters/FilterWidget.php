<?php

namespace SimplyFilters\Filters;

/**
 * Widget to render filter group
 *
 * @since 1.0.0
 */
class FilterWidget extends \WC_Widget {

	public function __construct() {
		$this->widget_id          = 'sf-filter-widget';
		$this->widget_cssclass    = 'sf-filter-widget';
		$this->widget_name        = __( 'SF Filter Group', \Hybrid\app( 'locale' ) );
		$this->widget_description = __( 'Simply add product\'s price, category and attribute filters to WooCommerce.', \Hybrid\app( 'locale' ) );

		parent::__construct();
	}

	/**
	 * Render the widget
	 *
	 * @param array $args Display arguments
	 * @param array $instance Settings for the particular instance of the widget
	 */
	public function widget( $args, $instance ) {

		$title = apply_filters( 'widget_title', isset( $instance['title'] ) ? $instance['title'] : '', $instance, $this->id_base );

		if ( isset( $instance['group_id'] ) && ! is_null( $instance['group_id'] ) ) {
			$attributes = array(
				'before_html' => $args['before_widget'],
				'after_html'  => $args['after_widget'],
			);

			if ( $title && isset( $instance['needToShowTitle'] ) && $instance['needToShowTitle'] ) {
				$attributes['before_html'] .= $args['before_title'] . $title . $args['after_title'];
			}

			echo $attributes['before_html'];

			$group = new FilterGroup( $instance['group_id'] );
			$group->render();

			echo $attributes['after_html'];
		}
	}

	/**
	 * Load widget admin settings
	 */
	public function load_settings() {

		$locale  = \Hybrid\app( 'locale' );
		$filters = [ '' => __( 'Not selected', $locale ) ];

		$filter_groups = get_posts( [
			'posts_per_page' => - 1,
			'post_type'      => \Hybrid\app( 'group_post_type' ),
		] );

		foreach ( $filter_groups as $group ) {
			$filters[ $group->ID ] = $group->post_title;
		}

		$this->settings = array(
			'group_id'    => array(
				'type'    => 'select',
				'std'     => '',
				'label'   => __( 'Filter group', $locale ),
				'options' => $filters,
			)
		);
	}

	/**
	 * Outputs the settings update form
	 *
	 * @param array $instance
	 */
	public function form( $instance ) {

		$this->load_settings();

		parent::form( $instance );
	}

	/**
	 * Update instance of widget
	 *
	 * @param array $new_instance
	 * @param array $old_instance
	 *
	 * @return array
	 */
	public function update( $new_instance, $old_instance ) {

		$this->load_settings();

		return parent::update( $new_instance, $old_instance );
	}
}