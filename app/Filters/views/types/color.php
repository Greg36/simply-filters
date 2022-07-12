<?php
/**
 * Color filter
 *
 * @var $id int
 * @var $key string
 * @var $options array
 * @var $values array
 * @var $settings array
 *
 * @since 1.0.0
 */

use function SimplyFilters\load_inline_svg;

?>
<div class="sf-color">
    <ul class="sf-color__list sf-option-list">
		<?php
		foreach ( $options as $index => $color ) {

			printf( '<li class="sf-color__item %s">',
				$settings['group']['more_show'] && intval( $settings['group']['more_count'] ) <= $index ? 'sf-option-more' : ''
			);

			// Input
			printf( '<input class="sf-color__input" type="checkbox" id="%s" name="%s" value="%s" data-query="%s" %s>',
				esc_attr( $id . '_' . $color['slug'] ),
				esc_attr( $key ),
				esc_attr( $color['slug'] ),
				esc_attr( $settings['query'] ),
				in_array( $color['slug'], $values ) ? 'checked' : '',

			);

			// Swatch
			$swatch = sprintf( '<div class="sf-color__swatch %s" style="background-color: %s;">%s</div>',
				esc_attr( $color['class'] ),
				sanitize_hex_color( $color['hex'] ),
				load_inline_svg( 'check.svg' )
			);

			// Label
			$label = esc_html( $color['label'] );
			$label .= \SimplyFilters\get_product_count( $settings['count'], $color );
			printf( '<label class="sf-color__label" for="%s">%s</label>',
				esc_attr( $id . '_' . $color['slug'] ),
				wp_kses_post( $swatch . $label )
			);

			echo '</li>';
		}
		?>
    </ul>

	<?php
	// Button to show more options
	\SimplyFilters\more_options_button( $settings['group'], count( $options ) );
	?>
</div>