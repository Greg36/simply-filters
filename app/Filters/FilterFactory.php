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
		$name   = ucfirst( $data['type'] ) . 'Filter';
		$class  = "SimplyFilters\\Filters\\Types\\{$name}";

		if ( class_exists( $class ) ) {
			$filter = new $class;
			$filter->initialize( $data ); // @todo I could remove that and pass $class( $data )
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

		$filter['id']         = $post->ID;
		$filter['key']        = $post->post_name;
		$filter['label']      = $post->post_title;
		$filter['menu_order'] = $post->menu_order;

		return $filter;
	}
}