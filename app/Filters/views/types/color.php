<?php
/**
 * @var $id int
 * @var $key string
 * @var $options array
 */

use function SimplyFilters\load_inline_svg;

?>
<div class="sf-color">
    <ul class="sf-color__list">
		<?php
		foreach ( $options as $value => $color ) {

			echo '<li class="sf-color__item">';

			// Swatch
			printf( '<div class="sf-color__swatch %s" style="background-color: %s;">%s</div>',
				esc_attr( $color['class'] ),
				sanitize_hex_color( $color['hex'] ),
				load_inline_svg( 'check.svg' )
			);

			// Input
			printf( '<input type="checkbox" id="%1$s" name="%2$s" value="%3$s"> <label for="%1$s">%4$s</label>',
				esc_attr( $id . '_' . $value ),
				esc_attr( $key ),
				esc_attr( $value ),
				esc_html( $color['label'] )
			);

			echo '</li>';
		}
		?>
    </ul>
</div>