<?php
/**
 * @var $id int
 * @var $key string
 * @var $options array
 * @var $values array
 * @var $settings array
 */
?>
<div class="sf-checkbox">
    <ul class="sf-checkbox__list sf-option-list">
		<?php
		foreach ( $options as $key => $option ) {

			printf( '<li class="sf-checkbox__check %s">',
				$settings['group']['more_show'] && intval( $settings['group']['more_count'] ) <= $key ? 'sf-option-more' : ''
			);

			// Input
			printf( '<input class="sf-checkbox__input" type="checkbox" id="%s" name="%s" value="%s" data-query="%s" %s>',
				esc_attr( $id . '_' . $option['slug'] ),
				esc_attr( $key ),
				esc_attr( $option['slug'] ),
				esc_attr( $settings['query'] ),
				in_array( $option['slug'], $values ) ? 'checked' : '',
			);

			// Label
			$label = esc_html( $option['name'] );
			$label .= \SimplyFilters\get_product_count( $settings['count'], $option );
			printf( '<label class="sf-checkbox__label" for="%s">%s</label>',
				esc_attr( $id . '_' . $option['slug'] ),
				$label
			);

			echo '</li>';
		}
		?>
    </ul>

	<?php
	// Button to show more options
    echo \SimplyFilters\get_more_options_button( $settings['group'], count( $options ) );
	?>
</div>