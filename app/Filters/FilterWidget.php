<?php

namespace SimplyFilters\Filters;

class FilterWidget extends \WC_Widget {


	public function __construct() {

		$this->widget_id          = 'sf-filter-widget';
		$this->widget_cssclass    = 'sf-filter-widget';
		$this->widget_name        = __( 'SF Filter Group', \Hybrid\app( 'locale' ) );
		$this->widget_description = __( 'lorem ipsum dolor sit amet', \Hybrid\app( 'locale' ) );

		parent::__construct();
	}

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

			$group = new FilterGroup( $instance['group_id'] );
			$group->render();
		}

	}

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

	public function form( $instance ) {

		$this->load_settings();

		parent::form( $instance );
	}

	public function update( $new_instance, $old_instance ) {

		$this->load_settings();

		return parent::update( $new_instance, $old_instance );
	}
}