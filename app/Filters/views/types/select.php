<?php
/**
 * @var $id int
 * @var $key string
 * @var $options array
 */
?>
<div class="sf-select">

    <select id="<?php esc_attr_e( $id ); ?>" name="<?php esc_attr_e( $key ); ?>">

        <?php
        foreach ( $options as $option ) {
            printf( '<option value="%s">%s</option>',
                esc_attr( $option['slug'] ),
                esc_html( $option['name'] )
            );
        }
        ?>

    </select>
</div>