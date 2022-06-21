<?php

namespace SimplyFilters\Filters\Types;

use SimplyFilters\TemplateLoader;
use function SimplyFilters\get_stars;

/**
 * Rating filter
 *
 * @since 1.0.0
 */
class RatingFilter extends Filter {

	/**
	 * @var array Array of supported settings
	 */
	protected $supports = [
		'label',
		'count'
	];

	public function __construct() {
		$this->type        = 'Rating';
		$this->name        = __( 'Rating', $this->locale );
		$this->description = __( 'Choose product rating', $this->locale );
	}

	/**
	 * Render the filter
	 */
	public function render() {
		if ( wc_review_ratings_enabled() ) {
			$options = $this->get_selected_values();

			TemplateLoader::render( 'types/rating', [
				'id'     => $this->get_id(),
				'key'    => _x( 'rating', 'slug', $this->locale ),
				'values' => $options,
				'count'  => $this->get_product_counts_by_rating()
			],
				'Filters'
			);
		}
	}

	/**
	 * Override source taxonomy
	 *
	 * @return string
	 */
	protected function get_current_source_taxonomy() {
		return 'rating';
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
                    <div class="sf-rating">
						<?php echo get_stars( 4 ); ?>
                    </div>
                </li>
                <li>
                    <div class="sf-checkbox__check"></div>
                    <div class="sf-rating">
						<?php echo get_stars( 3 ); ?>
                    </div>
                </li>
                <li>
                    <div class="sf-checkbox__check sf-checkbox__check--checked"></div>
                    <div class="sf-rating">
						<?php echo get_stars( 2 ); ?>
                    </div>
                </li>
                <li>
                    <div class="sf-checkbox__check sf-checkbox__check--checked"></div>
                    <div class="sf-rating">
						<?php echo get_stars( 1 ); ?>
                    </div>
                </li>
            </ul>
        </div>
		<?php
	}

	/**
	 * Get products counts for 5 rating taxonomies
	 *
	 * @return array
	 */
	protected function get_product_counts_by_rating() {
		$visibility_terms = wc_get_product_visibility_term_ids();

		$term_ids = [];
		for ( $i = 1; $i <= 5; $i ++ ) {
			$term_ids[] = $visibility_terms[ 'rated-' . $i ];
		}

		// Query count  by term IDs
		$products_count = $this->get_product_counts_in_terms( $term_ids, 'rating' );

		// Match term IDs back to rating value
		$rating_count = [];
		foreach ( $products_count as $id => $count ) {
			$term_id                  = str_replace( 'rated-', '', array_search( $id, $visibility_terms ) );
			$rating_count[ $term_id ] = $count;
		}

		return $rating_count;
	}
}