<?php

namespace SimplyFilters\Filters\Types;

use SimplyFilters\TemplateLoader;

/**
 * Slider filter
 *
 * @since 1.0.0
 */
class SliderFilter extends Filter {

	/**
	 * @var array Array of supported settings
	 */
	protected $supports = [
		'label'
	];

	public function __construct() {
		$this->type        = 'Slider';
		$this->name        = __( 'Slider', $this->locale );
		$this->description = __( 'Choose price range', $this->locale );

		// Set flag for slider script to be enqueued
		\Hybrid\app()->instance( 'enqueue-slider', true );
	}

	/**
	 * Render the filter
	 */
	public function render() {
		TemplateLoader::render( 'types/slider', [
			'id'     => $this->get_id(),
			'key'    => _x( 'price', 'slug', $this->locale ),
			'range'  => $this->get_price_range(),
			'values' => $this->get_selected_values(),
		],
			'Filters'
		);
	}

	/**
	 * Get the max and min price for queried products
	 * Search builds upon the main query args to be
	 * limited only to filtered products
	 *
	 * @return array
	 */
	private function get_price_range() {
		global $wpdb;

		$args = \Hybrid\app( 'filtered-query-args' );

		// Remove price query part to have full range on the slider
		$price_query = \Hybrid\app( 'filtered-query-price' );
		if ( $price_query ) {
			$args['where'] = str_replace( $price_query, '', $args['where'] );
		}

		$join  = $args['join'];
		$where = $args['where'];

		$sql = "
            SELECT MIN( min_price ) as min, MAX( max_price ) as max
            FROM {$wpdb->wc_product_meta_lookup}
            WHERE product_id IN (
                SELECT ID FROM {$wpdb->posts}
                $join
                WHERE 1=1
                $where
            )
        ";

		$price = $wpdb->get_row( $sql, ARRAY_A );
		$min   = $price['min'];
		$max   = $price['max'];

		// Adjust price range if taxes are enabled, price includes tax on front-end and product prices are entered without tax
		if ( wc_tax_enabled() && 'incl' === get_option( 'woocommerce_tax_display_shop' ) && ! wc_prices_include_tax() ) {
			$tax_class = apply_filters( 'woocommerce_price_filter_widget_tax_class', '' );
			$tax_rates = \WC_Tax::get_rates( $tax_class );

			if ( $tax_rates ) {
				$min += \WC_Tax::get_tax_total( \WC_Tax::calc_tax( $min, $tax_rates ) );
				$max += \WC_Tax::get_tax_total( \WC_Tax::calc_tax( $max, $tax_rates ) );
			}
		}

		return [
			'min' => ! is_null( $min ) ? intval( floor( $min ) ) : 0,
			'max' => ! is_null( $min ) ? intval( ceil( $max ) ) : 100
		];
	}

	/**
	 * Override source taxonomy
	 *
	 * @return string
	 */
	protected function get_current_source_taxonomy() {
		return '_price';
	}

	/**
	 * Render filter preview for new filter screen
	 */
	protected function filter_preview() {
		?>
        <div class="sf-slider">
            <div class="sf-slider__wrap">
                <div class="sf-slider__handler">
                    <div class="sf-slider__bar"></div>
                    <span class="sf-slider__left"></span>
                    <span class="sf-slider__right"></span>
                </div>

                <div class="sf-slider__values">
                    <div><?php echo wc_price( 75, [ 'decimals' => false ] ); ?></div>
                    <div><?php echo wc_price( 1674, [ 'decimals' => false ] ); ?></div>
                </div>
            </div>
        </div>
		<?php
	}
}