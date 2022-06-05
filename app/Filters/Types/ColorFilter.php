<?php

namespace SimplyFilters\Filters\Types;

use SimplyFilters\Admin\Controls\ColorControl;
use SimplyFilters\TemplateLoader;

class ColorFilter extends Filter {

	/**
	 * Array of supported settings
	 *
	 * @var array
	 */
	protected $supports = [
		'label',
		'sources',
		'query',
		'count'
	];

	public function __construct() {
		$this->type        = 'Color';
		$this->name        = __( 'Color', $this->locale );
		$this->description = __( 'Choose one or many colors', $this->locale );
	}

	/**
	 * Exclude stock status from default sources
	 */
	protected function set_sources() {
		$sources = $this->get_default_sources();
		unset( $sources['stock_status'] );

		$this->sources = $sources;
	}

	/**
	 * Load filter's settings
	 */
	protected function load_settings() {

		parent::load_settings();

		$this->settings->add( 'color', 'color', [
			'name'        => __( 'Select color', $this->locale ),
			'description' => __( 'For each term assign color from the color pallet', $this->locale ),
			'options'     => $this->get_current_source_options()
		] );
	}

	public function render() {
		$options = $this->get_current_source_options();

		if ( $options ) {
			TemplateLoader::render( 'types/color', [
				'id'       => $this->get_id(),
				'key'      => $this->get_current_source_key(),
				'options'  => $this->prepare_colors_data( $options ),
				'values'   => $this->get_selected_values(),
				'settings' => [
					'group' => $this->get_group_settings(),
					'query' => $this->get_data( 'query', 'or' ),
					'count' => $this->get_product_counts_in_terms( $options )
				]
			],
				'Filters'
			);
		}
	}


	protected function filter_preview() {
		?>
        <div class="sf-checkbox sf-color-preview">
            <ul>
                <li>
                    <div class="sf-checkbox__check sf-checkbox__check--checked" style="background-color: #393939;"></div>
                    <div class="sf-checkbox__label"><?php _e( 'Black', $this->locale ) ?></div>
                </li>
                <li>
                    <div class="sf-checkbox__check" style="background-color: #BD2046;"></div>
                    <div class="sf-checkbox__label"><?php _e( 'Maroon', $this->locale ) ?></div>
                </li>
                <li>
                    <div class="sf-checkbox__check sf-checkbox__check--checked" style="background-color: #4DAE44;"></div>
                    <div class="sf-checkbox__label"><?php _e( 'Green', $this->locale ) ?></div>
                </li>
            </ul>
        </div>
		<?php
	}

	private function prepare_colors_data( $options ) {

		if ( empty( $options ) ) {
			return [];
		}

		$colors = [];
		foreach ( $options as $term ) {
			$hex = get_term_meta( $term['id'], \Hybrid\app( 'term-color-key' ), true );

			$colors[] = [
				'slug'  => $term['slug'],
				'label' => $term['name'],
				'hex'   => $hex,
				'class' => $this->check_color_luminance( $hex ),
				'id'    => $term['id']
			];
		}

		return $colors;
	}

	/**
	 * If color has insufficient luminance add class
	 *
	 * @param $hex
	 *
	 * @return string
	 */
	private function check_color_luminance( $hex ) {
		$luminance = \SimplyFilters\calculateLuminance( $hex );

		return $luminance < 0.179 ? 'sf-color__swatch--contrast' : '';
	}
}