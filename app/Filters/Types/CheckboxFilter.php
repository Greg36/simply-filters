<?php

namespace SimplyFilters\Filters\Types;

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
		'query',
		'count',
		'order_by',
		'order_type'
	];

	public function __construct() {
		$this->type        = 'Checkbox';
		$this->name        = __( 'Checkbox', $this->locale );
		$this->description = __( 'Choose one or many options', $this->locale );
	}

	/**
	 * Render the filter
	 */
	public function render() {
        $data = $this->get_render_data();

        if( $data ) {
	        TemplateLoader::render( 'types/checkbox', $data, 'Filters' );
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