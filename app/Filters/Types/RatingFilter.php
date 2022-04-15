<?php

namespace SimplyFilters\Filters\Types;

use SimplyFilters\Admin\Controls\ToggleControl;

class RatingFilter extends Filter {

	/**
	 * Array of supported settings
	 *
	 * @var array
	 */
	protected $supports = [
		'label',
		'url-label'
	];


	public function __construct() {
		$this->type        = 'Rating';
		$this->name        = __( 'Rating', $this->locale );
		$this->description = __( 'Choose product rating', $this->locale );
	}

	protected function load_filter_settings() {

	}

	protected function filter_preview() {
		?>
        <div class="sf-checkbox">
            <ul>
                <li>
                    <div class="sf-checkbox__check sf-checkbox__check--checked"></div>
                    <div class="sf-rating">
                        <span class="sf-star sf-star--full"></span>
                        <span class="sf-star sf-star--full"></span>
                        <span class="sf-star sf-star--full"></span>
                        <span class="sf-star sf-star--full"></span>
                        <span class="sf-star"></span>
                    </div>
                </li>
                <li>
                    <div class="sf-checkbox__check"></div>
                    <div class="sf-rating">
                        <span class="sf-star sf-star--full"></span>
                        <span class="sf-star sf-star--full"></span>
                        <span class="sf-star sf-star--full"></span>
                        <span class="sf-star sf-star--half"></span>
                        <span class="sf-star"></span>
                    </div>
                </li>
                <li>
                    <div class="sf-checkbox__check sf-checkbox__check--checked"></div>
                    <div class="sf-rating">
                        <span class="sf-star sf-star--full"></span>
                        <span class="sf-star sf-star--full"></span>
                        <span class="sf-star sf-star--full"></span>
                        <span class="sf-star"></span>
                        <span class="sf-star"></span>
                    </div>
                </li>
                <li>
                    <div class="sf-checkbox__check sf-checkbox__check--checked"></div>
                    <div class="sf-rating">
                        <span class="sf-star sf-star--full"></span>
                        <span class="sf-star sf-star--full"></span>
                        <span class="sf-star"></span>
                        <span class="sf-star"></span>
                        <span class="sf-star"></span>
                    </div>
                </li>
            </ul>
        </div>
		<?php
	}
}