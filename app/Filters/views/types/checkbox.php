<?php
/**
 * @var $id int
 * @var $key string
 * @var $options array
 * @var $values array
 * @var $query string
 * @var $count array
 */
?>
<div class="sf-checkbox">
    <ul class="sf-checkbox__list">
		<?php
		foreach ( $options as $option ) {

            $label = esc_html( $option['name'] );
            if( $count !== false ) {
                $label .= '<span class="sf-label-count">';
	            $label .= isset( $count[ $option['id'] ] ) ? ' (' . intval( $count[ $option['id'] ] ) . ')' : ' (0)';
	            $label .= '</span>';
            }

			printf( '<li class="sf-checkbox__check"><input class="sf-checkbox__input" type="checkbox" id="%1$s" name="%2$s" value="%3$s" data-query="%4$s" %5$s> <label class="sf-checkbox__label" for="%1$s">%6$s</label></li>',
				esc_attr( $id . '_' . $option['slug'] ),
				esc_attr( $key ),
				esc_attr( $option['slug'] ),
                esc_attr( $query ),
                in_array( $option['slug'], $values ) ? 'checked' : '',
				$label
			);
		}
		?>
    </ul>
</div>