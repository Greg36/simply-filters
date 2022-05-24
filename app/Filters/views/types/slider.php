<?php
/**
 * @var $id int
 * @var $key string
 * @var $price array
 * @var $locale string
 * @var $values array
 */
?>
<div class="sf-slider">
    <div class="sf-slider__slider" style="display: none;"></div>
    <div class="sf-slider__inputs">
		<?php
		printf( '<input type="text" class="sf-slider__input sf-slider__input--min" id="%1$s" name="%2$s" value="%3$s" data-min="%3$s" placeholder="%4$s">',
			esc_attr( $id . '-min' ),
			esc_attr( $key . '-min' ),
			esc_attr( $values['min'] ),
			__( 'Min price', $locale )
		);

		printf( '<input type="text" class="sf-slider__input sf-slider__input--max" id="%1$s" name="%2$s" value="%3$s" data-min="%3$s" placeholder="%4$s">',
			esc_attr( $id . '-max' ),
			esc_attr( $key . '-max' ),
			esc_attr( $values['max'] ),
			__( 'Max price', $locale )
		);
		?>
    </div>
</div>