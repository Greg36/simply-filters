<?php
/**
 * @var $options array
 * @var $key string
 */
?>
<div class="sf-radio">
	<ul class="sf-radio__list">
		<?php
		foreach ( $options as $value => $label ) {
			printf( '<li class="sf-radio__item"><input type="radio" name="%1$s" value="%2$s"> <label for="%1$s">%3$s</label></li>',
				esc_attr( $key ),
                esc_attr( $value ),
                esc_html( $label )
			);
		}
		?>
	</ul>
</div>