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
		'label',
		'url-label'
	];

	public function __construct() {
		$this->type        = 'Slider';
		$this->name        = __( 'Slider', $this->locale );
		$this->description = __( 'Choose price range', $this->locale );
	}

	public function render() {
		TemplateLoader::render( 'types/slider', [
			'price' => $this->get_price_range(),
			'key'   => $this->get_data( 'url-label' )
		],
			'Filters'
		);
	}

	/**
     * Get the max and min price for queried products.
     * Search builds upon the main query to preserve all query vars.
     *
	 * @return array
	 */
	private function get_price_range() {
		$query = clone \WC_Query::get_main_query();

        // @todo: Possible option for price filter: limit the range to selected fitlers or leave it

		$query->set( 'posts_per_page', 1 );
		$query->set( 'meta_key', '_price' );
		$query->set( 'orderby', 'meta_value_num' );
		$query->set( 'no_found_rows', false );
		$query->set( 'fields', 'ids' );

        // Query the product and get its price
		$query->set( 'order', 'ASC' );
		$min = get_post_meta( $query->get_posts()[0], '_price', true );

        // For the max price just reverse the order
		$query->set( 'order', 'DESC' );
		$max = get_post_meta( $query->get_posts()[0], '_price', true );

        return [
			'min' => $min,
			'max' => $max
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