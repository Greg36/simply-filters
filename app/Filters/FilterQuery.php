<?php

namespace SimplyFilters\Filters;

class FilterQuery {


	/**
	 * @var \WP_Query
	 */
	private $query;

	public function __construct( \WP_Query $query ) {
		$this->query = $query;
	}

	public function init() {

		// Bail early if query has already been filtered
		if ( $this->query->get( 'sf-filters-applied', false ) ) {
			return;
		}

		// filtering should be all done based on a URL
		//

	}
}