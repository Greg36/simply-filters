<?php

namespace SimplyFilters\Filters\Types;

use SimplyFilters\Admin\Controls\ToggleControl;

class CheckboxFilter extends Filter {

	protected $supports = [
		'label',
		'url-label',
		'sources',
		'query'
	];

	protected function load_filter_settings() {

	}
}