<?php
/**
 * @var $id int
 * @var $key string
 * @var $options array
 * @var $values array
 *
 */
?>
<div class="sf-radio">
    <ul class="sf-radio__list">
		<?php
        if( empty( $values ) ) $values = ['no-filter'];
		foreach ( $options as $option ) {
			printf( '<li class="sf-radio__item"><input type="radio" id="%1$s" name="%2$s" value="%3$s" %4$s> <label for="%1$s">%5$s</label></li>',
				esc_attr( $id . '_' . $option['slug'] ),
				esc_attr( $key ),
				esc_attr( $option['slug'] ),
				in_array( $option['slug'], $values ) ? 'checked' : '',
				esc_html( $option['name'] )
			);
		}
		?>
    </ul>
</div>