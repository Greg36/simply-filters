<?php
/**
 * @var $id int
 * @var $key string
 * @var $range array
 * @var $locale string
 * @var $values array
 */
?>
<div class="sf-slider">
    <div class="sf-slider__ui" style="display: none;"></div>
    <div class="sf-slider__inputs">
		<?php
		printf( '<input type="number" class="sf-slider__input sf-slider__input--min" id="%1$s" name="%2$s" value="%3$s" data-min="%4$s" min="%4$s" max="%5$s" placeholder="%6$s">',
			esc_attr( $id . '-min' ),
			esc_attr( $key . '-min' ),
			esc_attr( isset( $values['min'] ) && $values['min'] > $range['min'] ? $values['min'] : $range['min'] ),
            esc_attr( $range['min'] ),
            esc_attr( $range['max'] ),
			__( 'Min price', $locale )
		);

		printf( '<input type="number" class="sf-slider__input sf-slider__input--max" id="%1$s" name="%2$s" value="%3$s" data-max="%4$s" min="%5$s" max="%4$s" placeholder="%6$s">',
			esc_attr( $id . '-max' ),
			esc_attr( $key . '-max' ),
			esc_attr( isset( $values['max'] ) && $values['max'] < $range['max'] ? $values['max'] : $range['max'] ),
			esc_attr( $range['max'] ),
			esc_attr( $range['min'] ),
			__( 'Max price', $locale )
		);
		?>
    </div>
</div>