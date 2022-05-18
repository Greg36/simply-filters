<?php
/**
 * @var $options array
 * @var $key string
 */
?>
<div class="sf-select">

    <select name="<?php esc_attr_e( $key ); ?>">

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