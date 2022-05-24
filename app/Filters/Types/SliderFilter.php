<?php

namespace SimplyFilters\Filters\Types;


use SimplyFilters\TemplateLoader;

class SliderFilter extends Filter {

	/**
	 * Array of supported settings
	 *
	 * @var array
	 */
	protected $supports = [
		'label'
    ];

	public function __construct() {
		$this->type        = 'Slider';
		$this->name        = __( 'Slider', $this->locale );
		$this->description = __( 'Choose price range', $this->locale );
	}

	public function render() {
		TemplateLoader::render( 'types/slider', [
			'id'    => $this->get_id(),
			'key'   => _x( 'price', 'slug', $this->locale ),
			'price' => $this->get_price_range()
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
        if( $price_query ) {
	        $args['where'] = str_replace( $price_query, '', $args['where'] );
        }

		$join = $args['join'];
        $where = $args['where'];

        $sql = "
            SELECT MIN( min_price ) as min, MAX( max_price ) as max
            FROM {$wpdb->wc_product_meta_lookup}
            WHERE product_id IN (
                SELECT ID FROM gjiw_posts
                $join
                WHERE 1=1
                $where
            )
        ";

        $price = $wpdb->get_row( $sql, ARRAY_A );

		return [
			'min' => intval( $price['min'] ),
			'max' => intval( $price['max'] )
		];
	}

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