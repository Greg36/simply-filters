<?php
/**
 * @var $id int
 * @var $key string
 * @var $options array
 * @var $settings array
 */
?>
<div class="sf-select">

    <select class="sf-select__input" id="<?php esc_attr_e( $id ); ?>" name="<?php esc_attr_e( $key ); ?>">

        <?php
        if( empty( $values ) ) $values = ['no-filter'];
        foreach ( $options as $option ) {

	        $label = esc_html( $option['name'] );
	        if( $settings[ 'count' ] !== false && $option['slug'] !== 'no-filter' ) {
		        $label .= '<span class="sf-label-count">';
		        $label .= isset( $settings[ 'count' ][ $option['id'] ] ) ? ' (' . intval( $settings[ 'count' ][ $option['id'] ] ) . ')' : ' (0)';
		        $label .= '</span>';
	        }

            printf( '<option value="%s" %s>%s</option>',
                esc_attr( $option['slug'] ),
	            in_array( $option['slug'], $values ) ? 'selected' : '',
	            $label
            );
        }
        ?>

    </select>
</div>