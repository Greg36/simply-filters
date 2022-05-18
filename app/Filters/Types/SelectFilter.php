<?php

namespace SimplyFilters\Filters\Types;

use SimplyFilters\Admin\Controls\ToggleControl;
use SimplyFilters\TemplateLoader;

class SelectFilter extends Filter {

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
		$this->type        = 'Select';
		$this->name        = __( 'Select', $this->locale );
		$this->description = __( 'Select only one from list', $this->locale );
	}

	public function render() {
		$options = $this->get_current_source_options();

		if( $options ) {
			TemplateLoader::render( 'types/select', [
				'options' => $options,
				'key' => $this->get_data( 'url-label' )
			],
				'Filters'
			);
		}
	}

	protected function filter_preview() {
		?>
        <div class="sf-select">
            <ul>
                <li><?php _e( 'Same day delivery', $this->locale ); ?></li>
                <li class="sf-select__selected"><?php _e( 'Same day delivery', $this->locale ); ?></li>
                <li><?php _e( 'Premium shipping', $this->locale ); ?></li>
                <li><?php _e( 'Standard shipping', $this->locale ); ?></li>
            </ul>
        </div>
		<?php
	}
}