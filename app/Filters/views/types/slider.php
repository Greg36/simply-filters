<?php
/**
 * Slider filter
 *
 * @var $id int
 * @var $key string
 * @var $range array
 * @var $locale string
 * @var $values array
 * @var $inputs bool
 *
 * @since 1.0.0
 */
?>
<div class="sf-slider" id="slider-<?php echo esc_attr( $id ); ?>">
    <div class="sf-slider__ui" style="display: none;"></div>
    <div class="sf-slider__inputs">
		<?php

		$min = $range['min'] > 0 ? $range['min'] - 1 : 1;
		$max = $range['max'];

		$from = isset( $values['min'] ) ? \SimplyFilters\limit_value_to_range( $values['min'], $min, $max ) : $min;
		$to   = isset( $values['max'] ) ? \SimplyFilters\limit_value_to_range( $values['max'], $min, $max ) : $max;

		// From price
		printf( '<input type="number" class="sf-slider__input sf-slider__input--min" id="%1$s" name="%2$s" value="%3$s" data-min="%4$s" min="%4$s" max="%5$s" placeholder="%6$s">',
			esc_attr( $id . '-min' ),
			esc_attr( $key . '-min' ),
			esc_attr( $from ),
			esc_attr( $min ),
			esc_attr( $max ),
			esc_html__( 'Min price', $locale )
		);

		// To price
		printf( '<input type="number" class="sf-slider__input sf-slider__input--max" id="%1$s" name="%2$s" value="%3$s" data-max="%4$s" min="%5$s" max="%4$s" placeholder="%6$s">',
			esc_attr( $id . '-max' ),
			esc_attr( $key . '-max' ),
			esc_attr( $to ),
			esc_attr( $max ),
			esc_attr( $min ),
			esc_html__( 'Max price', $locale )
		);
		?>
    </div>
</div>