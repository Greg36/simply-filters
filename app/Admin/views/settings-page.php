<?php

/**
 * @var $settings \SimplyFilters\Admin\Settings
 * @var $locale string
 */
?>

<div class="wrap">

    <form method="post">
        <div class="sf-settings sf-global-settings" id="sf-settings">

            <div class="sf-settings__wrap">
                <table class="sf-options">

                    <tbody>
				    <?php $settings->render(); ?>
                    </tbody>

                </table>

                <input type="submit" name="sf-save-settings" id="sf-save-settings" class="sf-button sf-button__main sf-button__save-settings" value="<?php esc_attr_e( __( 'Save settings', $locale ) ); ?>">
            </div>
        </div>

	    <?php wp_nonce_field( 'sf-general-settings', 'sf-general-settings' ); ?>

    </form>

</div>