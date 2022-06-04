<?php
/**
 * @var $id int
 * @var $key string
 * @var $query string
 * @var $options array
 * @var $values array
 * @var $count array
 */

use function SimplyFilters\load_inline_svg;

?>
<div class="sf-color">
    <ul class="sf-color__list">
		<?php
		foreach ( $options as $color ) {

			$label = esc_html( $color['label'] );
			if( $count !== false ) {
				$label .= '<span class="sf-label-count">';
				$label .= isset( $count[ $color['id'] ] ) ? ' (' . intval( $count[ $color['id'] ] ) . ')' : ' (0)';
				$label .= '</span>';
			}

			echo '<li class="sf-color__item">';

			// Input
			printf( '<input class="sf-color__input" type="checkbox" id="%1$s" name="%2$s" value="%3$s" data-query="%4$s" %5$s>',
				esc_attr( $id . '_' . $color['slug'] ),
				esc_attr( $key ),
				esc_attr( $color['slug'] ),
				esc_attr( $query ),
				in_array( $color['slug'], $values ) ? 'checked' : '',

			);

			// Swatch
			printf( '<div class="sf-color__swatch %s" style="background-color: %s;">%s</div>',
				esc_attr( $color['class'] ),
				sanitize_hex_color( $color['hex'] ),
				load_inline_svg( 'check.svg' )
			);

            // Label
            printf( '<label class="sf-color__label" for="%1$s">%2$s</label>',
	            esc_attr( $id . '_' . $color['slug'] ),
	            $label
            );

			echo '</li>';
		}
		?>
    </ul>
</div>