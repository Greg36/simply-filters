<?php
/**
 * @var $id int
 * @var $key string
 * @var $options array
 */
?>
<div class="sf-select">

    <select class="sf-select__input" id="<?php esc_attr_e( $id ); ?>" name="<?php esc_attr_e( $key ); ?>">

        <?php
        if( empty( $values ) ) $values = ['no-filter'];
        foreach ( $options as $option ) {
            printf( '<option value="%s" %s>%s</option>',
                esc_attr( $option['slug'] ),
	            in_array( $option['slug'], $values ) ? 'selected' : '',
                esc_html( $option['name'] )
            );
        }
        ?>

    </select>
</div>