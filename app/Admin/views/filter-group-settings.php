<?php
/**
 * Admin setting fields for filter group
 *
 * @var $settings \SimplyFilters\Admin\Settings
 * @var $locale string
 *
 * @since 1.0.0
 */

?>

<div class="sf-settings sf-tabs-target" id="sf-settings" data-filter_group_id="<?php esc_attr_e( \Hybrid\app( 'prefix' ) . '-' . get_the_ID() ); ?>">

    <div class="sf-settings__wrap">
        <table class="sf-options">

            <tbody>
			<?php $settings->render(); ?>
            </tbody>

        </table>

        <div class="sf-settings__notice">
            <?php
            printf( __( 'To change filter appearance go to <a href="%s">global settings</a>', $locale ),
	            esc_url( add_query_arg( 'page', Hybrid\app( 'plugin_name' ), get_admin_url() . 'options-general.php' ) )
            );
            ?>
        </div>
    </div>
</div>
