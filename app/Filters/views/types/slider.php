<?php
/**
 * @var $price array
 * @var $key string
 * @var $locale string
 */
?>
<div class="sf-slider">
    <div class="sf-slider__slider" style="display: none;"></div>
    <div class="sf-slider__inputs">
		<?php
		printf( '<input type="text" class="sf-slider__input sf-slider__input--min" name="%1$s" value="%2$s" data-min="%2$s" placeholder="%3$s">',
			esc_attr( $key ),
            esc_attr( $price['min'] ),
            __( 'Min price', $locale )
        );

		printf( '<input type="text" class="sf-slider__input sf-slider__input--max" name="%1$s" value="%2$s" data-min="%2$s" placeholder="%3$s">',
			esc_attr( $key ),
			esc_attr( $price['max'] ),
			__( 'Max price', $locale )
		);
		?>
    </div>
</div>