<?php
/**
 * @var $id int
 * @var $key string
 * @var $values array
 * @var $locale string
 */
?>
<div class="sf-rating">
    <ul class="sf-rating__list">
		<?php
		for ( $rating = 5; $rating >= 1; $rating-- ) {
            // @todo: split this into separate printfs
			printf( '<li class="sf-rating__item"><input class="sf-rating__input" type="checkbox" id="%1$s" name="%2$s" value="%3$s" %4$s> <label class="sf-rating__label" for="%1$s"><div class="sf-rating__stars" aria-hidden="true">%5$s</div> <span class="screen-reader-text">%6$s</span></label></li>',
				esc_attr( $id . '_' . $rating ),
				esc_attr( $key ),
				$rating,
				in_array( $rating, $values ) ? 'checked' : '',
				\SimplyFilters\get_stars( $rating ),
                __( 'Rating:', $locale ) . ' ' . $rating
			);
		}
		?>
    </ul>
</div>