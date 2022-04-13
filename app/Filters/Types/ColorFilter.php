<?php

namespace SimplyFilters\Filters\Types;

use SimplyFilters\Admin\Controls\ColorControl;

class ColorFilter extends Filter {

	protected $supports = [
		'label',
		'url-label',
		'sources'
	];

	/**
	 * Exclude stock status from default sources
	 */
	protected function set_sources() {
		$sources = $this->get_default_sources();
		unset( $sources['stock_status'] );

		$this->sources = $sources;
	}

	/**
	 * Load setting specific control // @todo  better doc here
	 */
	protected function load_filter_settings() {

		// Remove stock status from sources options
		unset( $this->sources['stock_status'] );

		$this->add_setting( 'product_cat', new ColorControl( [
			'name'        => __( 'Select color', $this->locale ),
			'description' => __( 'For each term assign color from the color pallet', $this->locale ),
			'options'     => $this->get_current_source_options()
		] ) );
	}
}