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
		'sources',
		'all_option',
		'count',
		'order_by',
		'order_type'
	];

	public function __construct() {
		$this->type        = 'Radio';
		$this->name        = __( 'Radio', $this->locale );
		$this->description = __( 'Select onyly one option', $this->locale );
	}

	/**
	 * Render the filter
	 */
	public function render() {
		$data = $this->get_render_data();

		if( $data ) {

            // Add all items option
			array_unshift( $data['options'], [
				'slug' => 'no-filter',
				'name' => $this->get_data( 'all_option' )
			] );

			TemplateLoader::render( 'types/radio', $data, 'Filters' );
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