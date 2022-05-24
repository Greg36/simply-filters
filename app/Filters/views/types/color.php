<?php
/**
 * @var $id int
 * @var $key string
 * @var $query string
 * @var $options array
 * @var $values array
 */

use function SimplyFilters\load_inline_svg;

?>
<div class="sf-color">
    <ul class="sf-color__list">
		<?php
		foreach ( $options as $color ) {

			echo '<li class="sf-color__item">';

			// Swatch
			printf( '<div class="sf-color__swatch %s" style="background-color: %s;">%s</div>',
				esc_attr( $color['class'] ),
				sanitize_hex_color( $color['hex'] ),
				load_inline_svg( 'check.svg' )
			);

			// Input
			printf( '<input type="checkbox" id="%1$s" name="%2$s" value="%3$s" data-query="%4$s" %5$s> <label for="%1$s">%6$s</label>',
				esc_attr( $id . '_' . $color['slug'] ),
				esc_attr( $key ),
				esc_attr( $color['slug'] ),
                esc_attr( $query ),
				in_array( $color['slug'], $values ) ? 'checked' : '',
				esc_html( $color['label'] )
			);

			echo '</li>';
		}
		?>
    </ul>
</div>