<?php

namespace SimplyFilters\Filters\Types;

use SimplyFilters\TemplateLoader;

class RadioFilter extends Filter {

	/**
	 * Array of supported settings
	 *
	 * @var array
	 */
	protected $supports = [
		'label',
		'sources'
	];

	public function __construct() {
		$this->type        = 'Radio';
		$this->name        = __( 'Radio', $this->locale );
		$this->description = __( 'Select onyly one option', $this->locale );
	}

	public function render() {
		$options = $this->get_current_source_options();

		if ( $options ) {
			TemplateLoader::render( 'types/radio', [
				'id'      => $this->get_id(),
				'key'     => $this->get_current_source_key(),
				'options' => $options
			],
				'Filters'
			);
		}
	}

	protected function filter_preview() {
		?>
        <div class="sf-checkbox sf-radio">
            <ul>
                <li>
                    <div class="sf-checkbox__check"></div>
                    <div class="sf-checkbox__label"><?php _e( 'Brand new', $this->locale ) ?></div>
                </li>
                <li>
                    <div class="sf-checkbox__check sf-checkbox__check--checked"></div>
                    <div class="sf-checkbox__label"><?php _e( 'Used', $this->locale ) ?></div>
                </li>
                <li>
                    <div class="sf-checkbox__check"></div>
                    <div class="sf-checkbox__label"><?php _e( 'Damaged', $this->locale ) ?></div>
                </li>
            </ul>
        </div>
		<?php
	}
}