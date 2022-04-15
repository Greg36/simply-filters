<?php

namespace SimplyFilters\Filters\Types;


class SliderFilter extends Filter {

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
		$this->type        = 'Slider';
		$this->name        = __( 'Slider', $this->locale );
		$this->description = __( 'Choose price range', $this->locale );
	}

	protected function load_filter_settings() {

	}

	protected function filter_preview() {
		?>
        <div class="sf-slider">
            <div class="sf-slider__wrap">
                <div class="sf-slider__handler">
                    <div class="sf-slider__bar"></div>
                    <span class="sf-slider__left"></span>
                    <span class="sf-slider__right"></span>
                </div>

                <div class="sf-slider__values">
                    <div><?php echo wc_price( 75, [ 'decimals' => false ] ); ?></div>
                    <div><?php echo wc_price( 1674, [ 'decimals' => false ] ); ?></div>
                </div>
            </div>
        </div>
		<?php
	}
}