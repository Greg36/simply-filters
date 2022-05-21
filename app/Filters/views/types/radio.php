<?php
/**
 * @var $id int
 * @var $key string
 * @var $options array
 */
?>
<div class="sf-radio">
    <ul class="sf-radio__list">
		<?php
		foreach ( $options as $option ) {
			printf( '<li class="sf-radio__item"><input type="radio" id="%1$s" name="%2$s" value="%3$s"> <label for="%1$s">%4$s</label></li>',
				esc_attr( $id . '_' . $option['slug'] ),
				esc_attr( $key ),
				esc_attr( $option['slug'] ),
				esc_html( $option['name'] )
			);
		}
		?>
    </ul>
</div>