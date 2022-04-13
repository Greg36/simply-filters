<?php

namespace SimplyFilters\Filters\Types;

use SimplyFilters\Admin\Controls\ToggleControl;

class SelectFilter extends Filter {

	protected $supports = [
		'label',
		'url-label',
		'sources'
	];

	protected function load_filter_settings() {

	}
}