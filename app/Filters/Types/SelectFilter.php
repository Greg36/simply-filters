<?php

namespace SimplyFilters\Filters\Types;

use SimplyFilters\TemplateLoader;

/**
 * Select filter
 *
 * @since 1.0.0
 */
class SelectFilter extends Filter {

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
		$this->type        = 'Select';
		$this->name        = esc_html__( 'Select', $this->locale );
		$this->description = esc_html__( 'Select only one from list', $this->locale );
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
			 * Select filter data before render
			 *
			 * @param array $data Filter options, settings and values
			 */
			$data = apply_filters( 'sf-select-render-data', $data );

			TemplateLoader::render( 'types/select', $data, 'Filters' );
		}
	}

	/**
	 * Render filter preview for new filter screen
	 */
	protected function filter_preview() {
		?>
        <div class="sf-select">
            <ul>
                <li><?php esc_html_e( 'Show all products', $this->locale ); ?></li>
                <li class="sf-select__selected"><?php esc_html_e( 'Available', $this->locale ); ?></li>
                <li><?php esc_html_e( 'Sold out', $this->locale ); ?></li>
                <li><?php esc_html_e( 'On preorder', $this->locale ); ?></li>
            </ul>
        </div>
		<?php
	}
}