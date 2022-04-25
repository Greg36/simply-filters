<?php

namespace SimplyFilters\Filters\Types;

use SimplyFilters\Admin\Controls\ColorControl;

class ColorFilter extends Filter {

	/**
	 * Array of supported settings
	 *
	 * @var array
	 */
	protected $supports = [
		'label',
		'url-label',
		'sources'
	];

	public function __construct() {
		$this->type        = 'Color';
		$this->name        = __( 'Color', $this->locale );
		$this->description = __( 'Choose one or many colors', $this->locale );
	}

	/**
	 * Exclude stock status from default sources
	 */
	protected function set_sources() {
		$sources = $this->get_default_sources();
		unset( $sources['stock_status'] );

		$this->sources = $sources;
	}

	/**
	 * Load filter's settings
	 */
	protected function load_settings() {

        parent::load_settings();

		$this->settings->add( 'color', 'color', [
			'name'        => __( 'Select color', $this->locale ),
			'description' => __( 'For each term assign color from the color pallet', $this->locale ),
			'options'     => $this->get_current_source_options()
		] );
	}

	protected function filter_preview() {
		?>
        <div class="sf-checkbox sf-color-preview">
            <ul>
                <li>
                    <div class="sf-checkbox__check sf-checkbox__check--checked" style="background-color: #393939;"></div>
                    <div class="sf-checkbox__label"><?php _e( 'Black', $this->locale ) ?></div>
                </li>
                <li>
                    <div class="sf-checkbox__check" style="background-color: #BD2046;"></div>
                    <div class="sf-checkbox__label"><?php _e( 'Maroon', $this->locale ) ?></div>
                </li>
                <li>
                    <div class="sf-checkbox__check sf-checkbox__check--checked" style="background-color: #4DAE44;"></div>
                    <div class="sf-checkbox__label"><?php _e( 'Green', $this->locale ) ?></div>
                </li>
            </ul>
        </div>
		<?php
	}
}