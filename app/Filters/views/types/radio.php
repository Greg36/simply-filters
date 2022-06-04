<?php
/**
 * @var $id int
 * @var $key string
 * @var $options array
 * @var $values array
 * @var $count array
 */
?>
<div class="sf-radio">
    <ul class="sf-radio__list">
		<?php
        if( empty( $values ) ) $values = ['no-filter'];
		foreach ( $options as $option ) {

			$label = esc_html( $option['name'] );
			if( $count !== false && $option['slug'] !== 'no-filter' ) {
				$label .= '<span class="sf-label-count">';
				$label .= isset( $count[ $option['id'] ] ) ? ' (' . intval( $count[ $option['id'] ] ) . ')' : ' (0)';
				$label .= '</span>';
			}

			printf( '<li class="sf-radio__item"><input class="sf-radio__input" type="radio" id="%1$s" name="%2$s" value="%3$s" %4$s> <label class="sf-radio__label" for="%1$s">%5$s</label></li>',
				esc_attr( $id . '_' . $option['slug'] ),
				esc_attr( $key ),
				esc_attr( $option['slug'] ),
				in_array( $option['slug'], $values ) ? 'checked' : '',
				$label
			);
		}
		?>
    </ul>
</div>