<?php
/**
 * @var $id int
 * @var $key string
 * @var $values array
 */
?>
<div class="sf-rating">
    <ul class="sf-rating__list">
		<?php
		for ( $i = 5; $i >= 1; $i-- ) {
			printf( '<li class="sf-rating__item"><input type="checkbox" id="%1$s" name="%2$s" value="%3$s" %4$s> <label for="%1$s">%5$s</label></li>',
				esc_attr( $id . '_' . $i ),
				esc_attr( $key ),
				$i,
				in_array( $i, $values ) ? 'checked' : '',
				\SimplyFilters\get_stars( $i )
			);
		}
		?>
    </ul>
</div>