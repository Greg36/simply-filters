<?php

namespace SimplyFilters\Filters;

class PostType {

	private $group_post_type = 'sf_filter_group';

	private $item_post_type = 'sf_filter_item';

	public function init() {
		add_action( 'init', [ $this, 'register_post_types' ] );
	}

	public function register_post_types() {

		register_post_type(
			$this->get_group_post_type(),
			array(
				'public'              => false,
				'has_archive'         => false,
				'publicaly_queryable' => false,
				'show_in_menu'        => 'woocommerce',
				'show_in_admin_bar'   => false,
				'show_ui'             => true,
				'hierarchical'        => false,
				'supports'            => array(
					'author',
				),
				'labels'              => array(
					'name'          => __( 'Filters', 'simply-filters' ),
					'singular_name' => __( 'Filter', 'simply-filters' ),
				),
			)
		);

		register_post_type(
			$this->get_item_post_type(),
			array(
				'public'       => false,
				'hierarchical' => false,
				'supports'     => array(),
				'labels'       => array(
					'name' => __( 'Filter Item', 'simply-filters' ),
				),
			)
		);
	}

	/**
	 * @return string
	 */
	public function get_group_post_type() {
		return $this->group_post_type;
	}

	/**
	 * @return string
	 */
	public function get_item_post_type() {
		return $this->item_post_type;
	}
}