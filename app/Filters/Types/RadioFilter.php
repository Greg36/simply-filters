<?php

namespace SimplyFilters\Filters\Types;

use SimplyFilters\TemplateLoader;

/**
 * Radio filter
 *
 * @since 1.0.0
 */
class RadioFilter extends Filter {

	/**
	 * @var array Array of supported settings
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
		$this->name        = esc_html__( 'Radio', $this->locale );
		$this->description = esc_html__( 'Select only one option', $this->locale );
	}

	/**
	 * Render the filter
	 */
	public function render() {
		$data = $this->get_render_data();

		if ( $data ) {

			// Add all items option
			array_unshift( $data['options'], [
				'slug' => 'no-filter',
				'name' => $this->get_data( 'all_option' )
			] );

			/**
			 * Radio filter data before render
			 *
			 * @param array $data Filter options, settings and values
			 */
			$data = apply_filters( 'sf-radio-render-data', $data );

			TemplateLoader::render( 'types/radio', $data, 'Filters' );
		}
	}

	/**
	 * Render filter preview for new filter screen
	 */
	protected function filter_preview() {
		?>
        <div class="sf-checkbox sf-radio">
            <ul>
                <li>
                    <div class="sf-checkbox__check"></div>
                    <div class="sf-checkbox__label"><?php esc_html_e( 'Brand new', $this->locale ) ?></div>
                </li>
                <li>
                    <div class="sf-checkbox__check sf-checkbox__check--checked"></div>
                    <div class="sf-checkbox__label"><?php esc_html_e( 'Used', $this->locale ) ?></div>
                </li>
                <li>
                    <div class="sf-checkbox__check"></div>
                    <div class="sf-checkbox__label"><?php esc_html_e( 'Damaged', $this->locale ) ?></div>
                </li>
            </ul>
        </div>
		<?php
	}
}