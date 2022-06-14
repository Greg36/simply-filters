<?php

/**
 * @var $settings \SimplyFilters\Admin\Settings
 * @var $locale string
 */
?>

<div class="wrap">

    <div class="sf-settings sf-global-settings" id="sf-settings">

        <h3 class="sf-settings__heading"><?php _e( 'Filters appearance', $locale ) ?></h3>

        <div class="sf-settings__wrap">
            <table class="sf-options">

                <tbody>
                <?php $settings->render(); ?>
                </tbody>

            </table>
        </div>
    </div>

</div>