<?php

/**
 * @var $settings \SimplyFilters\Admin\Settings
 * /

/**
 * @todo add info
 *
 * @link       https://gregn.pl
 * @since      1.0.0
 *
 * @package    SimplyFilters
 * @subpackage SimplyFilters/public/views
 */

?>

<div class="sf-settings sf-tabs-target" id="sf-settings" data-filter_group_id="<?php esc_attr_e( \Hybrid\app('prefix') . '-' . get_the_ID() ); ?>">

    <div class="sf-settings__wrap">
        <table class="sf-options">

            <tbody>
		    	<?php $settings->render(); ?>
            </tbody>

        </table>
    </div>
</div>
