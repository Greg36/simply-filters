<?php

/**
 * @var $settings \SimplyFilters\Admin\Settings
 */

?>

<div class="sf-settings sf-tabs-target" id="sf-settings" data-filter_group_id="<?php esc_attr_e( \Hybrid\app('prefix') . '-' . get_the_ID() ); ?>">

    <div class="sf-settings__wrap">
        <table class="sf-options">

            <tbody>
		    	<?php $settings->render(); ?>
            </tbody>

        </table>

        <div class="sf-settings__notice">
            To change filter appearance go to <a href="<?php echo esc_url( add_query_arg( 'page', Hybrid\app( 'plugin_name' ), get_admin_url() . 'options-general.php' ) ) ?>">global settings</a>
        </div>
    </div>
</div>
