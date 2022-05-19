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
        foreach ( $options as $value => $label ) {
            printf( '<option value="%s">%s</option>',
                esc_attr( $value ),
                esc_html( $label )
            );
        }
        ?>

    </select>
</div>