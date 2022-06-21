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
		$this->name        = __( 'Select', $this->locale );
		$this->description = __( 'Select only one from list', $this->locale );
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
                <li><?php _e( 'Same day delivery', $this->locale ); ?></li>
                <li class="sf-select__selected"><?php _e( 'Same day delivery', $this->locale ); ?></li>
                <li><?php _e( 'Premium shipping', $this->locale ); ?></li>
                <li><?php _e( 'Standard shipping', $this->locale ); ?></li>
            </ul>
        </div>
		<?php
	}
}