<?php

namespace SimplyFilters\Filters;

class FilterShortcode {


	private $group_id;

	public function __construct( $group_id ) {
		$this->group_id = $group_id;
	}

	/**
	 * @return string
	 */
	public function getShortcode() {
		return '';
	}
}