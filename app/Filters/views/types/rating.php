<?php
/**
 * @var $key string
 */
?>
<div class="sf-rating">
    <ul class="sf-rating__list">
		<?php
		for ( $i = 5; $i >= 1; $i-- ) {
			printf( '<li class="sf-rating__item"><input type="checkbox" name="%1$s" value="%2$s"> <label for="%1$s">%3$s</label></li>',
				esc_attr( $key ),
				$i,
				\SimplyFilters\get_stars( $i )
			);
		}
		?>
    </ul>
</div>