<?php

namespace SimplyFilters\Filters;

use SimplyFilters\Contracts\FilterInterface;

class Filter implements FilterInterface {
	// render all common fields

	/**
	 * @var array Filter's data
	 */
	protected $data;

	public function initialize( $data ) {
		$this->data = $data;
	}

	public function debug() {
		var_dump( $this->data );
	}
}