<?php
/**
 * @var $id int
 * @var $key string
 * @var $options array
 * @var $query string
 */
?>
<div class="sf-checkbox">
    <ul class="sf-checkbox__list">
		<?php
		foreach ( $options as $option ) {
			printf( '<li class="sf-checkbox__item"><input type="checkbox" id="%1$s" name="%2$s" value="%3$s" data-query="%4$s"> <label for="%1$s">%5$s</label></li>',
				esc_attr( $id . '_' . $option['slug'] ),
				esc_attr( $key ),
				esc_attr( $option['slug'] ),
                esc_attr( $query ),
				esc_html( $option['name'] )
			);
		}
		?>
    </ul>
</div>