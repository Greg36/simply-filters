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
		$group = new FilterGroup( $this->group_id );

		ob_start();
		$group->render();

		return ob_get_clean();
	}
}