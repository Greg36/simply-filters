<?php

namespace SimplyFilters\Filters\Types;

use SimplyFilters\Admin\Controls\ToggleControl;

class RatingFilter extends Filter {

	protected $supports = [
		'label',
		'url-label'
	];

	protected function load_filter_settings() {

	}
}