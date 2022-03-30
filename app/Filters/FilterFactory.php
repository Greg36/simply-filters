<?php

namespace SimplyFilters\Filters;

class FilterFactory {

	/**
	 *  Instantiate new filter based on its type
	 *
	 * @param \WP_Post $filter
	 */
	public static function build( \WP_Post $post ) {

		$filter = false;
		$data   = static::unserialize_content( $post );
		$class  = "SimplyFilters\\Types\\{$data['type']}";

		if ( class_exists( $class ) ) {
			$filter = new $class;
			$filter->initialize( $data );
		}

		return $filter;
	}

	/**
	 * Universalize post data and parse post values
	 *
	 * @param \WP_Post $post
	 *
	 * @return array|false Filter content array
	 */
	private static function unserialize_content( \WP_Post $post ) {

		// Return early if incorrect post type
		if ( $post->post_type !== \Hybrid\app( 'item_post_type' ) ) {
			return false;
		}

		$filter = (array) maybe_unserialize( $post->post_content );

		$filter['ID']         = $post->ID;
		$filter['key']        = $post->post_name;
		$filter['label']      = $post->post_title;
		$filter['menu_order'] = $post->menu_order;
		$filter['group']      = $post->post_parent;

		return $filter;
	}
}