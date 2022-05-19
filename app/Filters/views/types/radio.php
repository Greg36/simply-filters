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
		foreach ( $options as $value => $label ) {
			printf( '<li class="sf-radio__item"><input type="radio" id="%1$s" name="%2$s" value="%3$s"> <label for="%1$s">%4$s</label></li>',
				esc_attr( $id . '_' . $value ),
				esc_attr( $key ),
				esc_attr( $value ),
				esc_html( $label )
			);
		}
		?>
    </ul>
</div>