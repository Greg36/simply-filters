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

	public function render() {
		// @todo: Possible option for selected tags or categories - make the links clickable to go to that category
		$options = $this->get_current_source_options();
		$count   = $this->get_product_counts_in_terms( $options );

		if ( $options ) {
			// @todo: instead of going with long list of options figure out a way to pass them all automatically
			TemplateLoader::render( 'types/checkbox', [
				'id'       => $this->get_id(),
				'key'      => $this->get_current_source_key(),
				'options'  => $this->order_options( $options, $count ),
				'values'   => $this->get_selected_values(),
				'settings' => [
					'group' => $this->get_group_settings(),
					'query' => $this->get_data( 'query', 'or' ),
					'count' => $count,
				]
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