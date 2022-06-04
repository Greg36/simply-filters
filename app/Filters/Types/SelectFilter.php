<?php

namespace SimplyFilters\Filters\Types;

use SimplyFilters\TemplateLoader;

class SelectFilter extends Filter {

	/**
	 * Array of supported settings
	 *
	 * @var array
	 */
	protected $supports = [
		'label',
		'sources',
		'all_option',
		'count'
	];

	public function __construct() {
		$this->type        = 'Select';
		$this->name        = __( 'Select', $this->locale );
		$this->description = __( 'Select only one from list', $this->locale );
	}

	public function render() {
		$options = $this->get_current_source_options();

		if ( $options ) {

			array_unshift( $options, [
				'slug' => 'no-filter',
				'name' => $this->get_data( 'all_option' )
			] );

			TemplateLoader::render( 'types/select', [
				'id'      => $this->get_id(),
				'key'     => $this->get_current_source_key(),
				'options' => $options,
				'values'  => $this->get_selected_values(),
				'count'   => $this->get_product_counts_in_terms( $options )
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