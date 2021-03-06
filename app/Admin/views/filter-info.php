<?php
/**
 * Info metabox about filter setup
 *
 * @var $locale string
 *
 * @since 1.0.0
 */
?>

<div class="sf-place">
    <h3 class="sf-place__heading"><?php esc_html_e( 'Setup filters in the shop:', $locale ) ?></h3>
    <p><?php echo wp_kses_post( __( 'After you prepare filters group it <strong>will not</strong> be automatically visible in your shop, you need to place this group somewhere.', $locale ) ) ?></p>
    <p>To place filters on your site you have three choices:</p>
    <ul class="sf-place__list">

        <!-- Blocks -->
		<?php if ( current_theme_supports( 'wp-block-styles' ) ) { ?>
            <li>
                <h4><?php esc_html_e( 'Block', $locale ); ?></h4>
                <p>
					<?php
					$shopID = get_option( 'woocommerce_shop_page_id' );
					printf( wp_kses_post( __( 'Add new <strong>SF Filter Group</strong> block to the WooCommerce <a href="%s" target="_blank">shop page</a>.', $locale ) ),
						esc_url( admin_url( "post.php?post={$shopID}&action=edit" ) )
					);
					?>
                </p>
            </li>
		<?php } ?>

        <!-- Widgets -->
		<?php if ( current_theme_supports( 'widgets' ) ) { ?>
            <li>
                <h4><?php esc_html_e( 'Widget', $locale ); ?></h4>
                <p>
					<?php printf( wp_kses_post( __( 'Add new <strong>SF Filter Group</strong> widget to shop sidebar in <a href="%s" target="_blank">widgets panel</a>.' ) ),
						esc_url( admin_url( 'widgets.php' ) )
					); ?>
                </p>
            </li>
		<?php } ?>

        <!-- Shortcode -->
        <li>
            <h4><?php esc_html_e( 'Shortcode', $locale ); ?></h4>
            <p><?php esc_html_e( 'Place this shortcode where you need filters to appear:', $locale ) ?></p>
			<?php
			printf( '<input type="text" readonly="readonly" onclick="this.select();" value="%s"/>',
				esc_attr( '[' . \Hybrid\app( 'shortcode_tag' ) . ' group_id="' . get_the_ID() . '"]' )
			)
			?>
        </li>

    </ul>
</div>