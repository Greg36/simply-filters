<?php

namespace SimplyFilters\Filters\Types;

use SimplyFilters\TemplateLoader;

/**
 * Checkbox filter
 *
 * @since 1.0.0
 */
class CheckboxFilter extends Filter {

	/**
	 * @var array Array of supported settings
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
		$this->name        = esc_html__( 'Checkbox', $this->locale );
		$this->description = esc_html__( 'Choose one or many options', $this->locale );
	}

	/**
	 * Render the filter
	 */
	public function render() {

        $data = $this->get_render_data();

		/**
		 * Checkbox filter data before render
		 *
		 * @param array $data Filter options, settings and values
		 */
		$data = apply_filters( 'sf-checkbox-render-data', $data );

		if ( $data ) {
			TemplateLoader::render( 'types/checkbox', $data, 'Filters' );
		}
	}

	/**
	 * Render filter preview for new filter screen
	 */
	protected function filter_preview() {
		?>
        <div class="sf-checkbox">
            <ul>
                <li>
                    <div class="sf-checkbox__check sf-checkbox__check--checked"></div>
                    <div class="sf-checkbox__label"><?php esc_html_e( 'Synthetic', $this->locale ) ?></div>
                </li>
                <li>
                    <div class="sf-checkbox__check"></div>
                    <div class="sf-checkbox__label"><?php esc_html_e( 'Linen', $this->locale ) ?></div>
                </li>
                <li>
                    <div class="sf-checkbox__check sf-checkbox__check--checked"></div>
                    <div class="sf-checkbox__label"><?php esc_html_e( 'Wool', $this->locale ) ?></div>
                </li>
                <li>
                    <div class="sf-checkbox__check sf-checkbox__check--checked"></div>
                    <div class="sf-checkbox__label"><?php esc_html_e( 'Cotton', $this->locale ) ?></div>
                </li>
            </ul>
        </div>
		<?php
	}
}