<?php
/**
 * @var $options array
 * @var $key string
 */

use function SimplyFilters\load_inline_svg;

?>
<div class="sf-color">
	<ul class="sf-color__list">
		<?php
		foreach ( $options as $id => $color ) {

            echo '<li class="sf-color__item">';

                // Swatch
                printf( '<div class="sf-color__swatch %s" style="background-color: %s;">%s</div>',
                    esc_attr( $color['class'] ),
                    sanitize_hex_color( $color['hex'] ),
	                load_inline_svg( 'check.svg' )
                );

                // Input
                printf( '<input type="checkbox" name="%1$s" value="%2$s"> <label for="%1$s">%3$s</label>',
                    esc_attr( $key ),
                    esc_attr( $id ),
                    esc_html( $color['label'] )
                );

            echo '</li>';
		}
		?>
	</ul>
</div>