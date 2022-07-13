<?php

namespace SimplyFilters\Filters;

/**
 * Shortcode to render filter group
 *
 * @since 1.0.0
 */
class FilterShortcode {

	/**
	 * @var int Filter group post ID
	 */
	private $group_id;

	public function __construct( $group_id ) {
		$this->group_id = $group_id;
	}

	/**
	 * Instantiate new filter group and render it
	 *
	 * @return string
	 */
	public function getShortcode() {

		$group = get_post( $this->group_id );

		ob_start();

		if( $group instanceof \WP_Post && $group->post_status === 'publish' ) {
			$group = new FilterGroup( $group );
			$group->render();
		}

		return ob_get_clean();
	}
}