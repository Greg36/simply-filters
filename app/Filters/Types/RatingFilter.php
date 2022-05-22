<?php

namespace SimplyFilters\Filters\Types;

use SimplyFilters\TemplateLoader;
use function SimplyFilters\get_stars;

class RatingFilter extends Filter {

	/**
	 * Array of supported settings
	 *
	 * @var array
	 */
	protected $supports = [
		'label'
	];

	public function __construct() {
		$this->type        = 'Rating';
		$this->name        = __( 'Rating', $this->locale );
		$this->description = __( 'Choose product rating', $this->locale );
	}

	public function render() {
		if ( wc_review_ratings_enabled() ) {
			TemplateLoader::render( 'types/rating', [
				'id'  => $this->get_id(),
				'key' => _x( 'rating', 'slug', $this->locale )
			],
				'Filters'
			);
		} else {
			// @todo: info to admin about reviews not being enabled?
		}
	}

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
}