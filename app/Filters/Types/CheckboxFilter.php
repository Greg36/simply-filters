<?php

namespace SimplyFilters\Filters\Types;

use SimplyFilters\Admin\Controls\ToggleControl;
use SimplyFilters\TemplateLoader;

class CheckboxFilter extends Filter {

	/**
	 * Array of supported settings
	 *
	 * @var array
	 */
	protected $supports = [
		'label',
		'sources',
		'query'
	];

	public function __construct() {
		$this->type        = 'Checkbox';
		$this->name        = __( 'Checkbox', $this->locale );
		$this->description = __( 'Choose one or many options', $this->locale );
	}

	public function render() {
		// @todo: Possible option for selected tags or categories - make the links clickable to go to that category
		$options = $this->get_current_source_options();

		if ( $options ) {
			TemplateLoader::render( 'types/checkbox', [
				'id'      => $this->get_id(),
				'key'     => $this->get_current_source_key(),
				'options' => $options,
				'query'   => $this->get_data( 'query', 'or' )
			],
				'Filters'
			);
		}
	}

	protected function filter_preview() {
		?>
        <div class="sf-checkbox">
            <ul>
                <li>
                    <div class="sf-checkbox__check sf-checkbox__check--checked"></div>
                    <div class="sf-checkbox__label"><?php _e( 'Synthetic', $this->locale ) ?></div>
                </li>
                <li>
                    <div class="sf-checkbox__check"></div>
                    <div class="sf-checkbox__label"><?php _e( 'Linen', $this->locale ) ?></div>
                </li>
                <li>
                    <div class="sf-checkbox__check sf-checkbox__check--checked"></div>
                    <div class="sf-checkbox__label"><?php _e( 'Wool', $this->locale ) ?></div>
                </li>
                <li>
                    <div class="sf-checkbox__check sf-checkbox__check--checked"></div>
                    <div class="sf-checkbox__label"><?php _e( 'Cotton', $this->locale ) ?></div>
                </li>
            </ul>
        </div>
		<?php
	}
}