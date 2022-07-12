<?php
/**
 * Rating filter
 *
 * @var $id int
 * @var $key string
 * @var $values array
 * @var $locale string
 * @var $count array
 *
 * @since 1.0.0
 */
?>
<div class="sf-rating">
    <ul class="sf-rating__list">
		<?php
		for ( $rating = 5; $rating >= 1; $rating-- ) {

            echo '<li class="sf-rating__item">';

            // Input
            printf( '<input class="sf-rating__input" type="checkbox" id="%s" name="%s" value="%s" %s>',
	            esc_attr( $id . '_' . $rating ),
	            esc_attr( $key ),
	            $rating,
	            in_array( $rating, $values ) ? 'checked' : '',
            );

            // Label
			$rating_count = '';
			if( $count !== false ) {
				$rating_count .= '<span class="sf-label-count">&nbsp;';
				$rating_count .= isset( $count[ $rating ] ) ? '(' . intval( $count[ $rating ] ) . ')' : '(0)';
				$rating_count .= '</span>';
			}
			printf( '<label class="sf-rating__label" for="%s"><div class="sf-rating__stars" aria-hidden="true">%s</div><span class="screen-reader-text">%s</span></label>',
	            esc_attr( $id . '_' . $rating ),
	            wp_kses_post( \SimplyFilters\get_stars( $rating ) . $rating_count ),
	            esc_html__( 'Rating:', $locale ) . ' ' . intval( $rating )
            );

            echo '</li>';
		}
		?>
    </ul>
</div>