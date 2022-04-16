<?php
/**
 * @var $filter \SimplyFilters\Filters\Types\Filter
 * @var $order string
 * @var $locale string
 */
?>



<div class="sf-filter"
     data-filter_id="<?php esc_attr_e( \Hybrid\app( 'prefix' ) . '-' . $filter->get_id() ); ?>"
     data-filter_type="<?php esc_attr_e( $filter->get_type() ); ?>"
>

    <div class="sf-filter__row sf-row">

        <div class="sf-row__order">
			<?php esc_html_e( $order ); ?>
        </div>

        <div class="sf-row__enabled">
            <label class="sf-switch">
                <input type="checkbox" <?php checked( $filter->is_enabled() ); ?> >
                <span class="sf-switch__slider"></span>
            </label>
        </div>

        <div class="sf-row__label">
			<?php esc_html_e( $filter->get_label() ); ?>
        </div>

        <div class="sf-row__type">
			<?php esc_html_e( $filter->get_name() ); ?>
        </div>

        <ul class="sf-row__actions">
            <li><a href="#" class="edit-filter" title="<?php _e( 'Edit filter', $locale ) ?>"><?php _e( 'Edit', $locale ); ?></a></li>
            <li><a href="#" class="duplicate-filter" title="<?php _e( 'Duplicate filter', $locale ) ?>"><?php _e( 'Duplicate', $locale ); ?></a></li>
            <li><a href="#" class="remove-filter" title="<?php _e( 'Remove filter', $locale ) ?>"><?php _e( 'Remove', $locale ); ?></a></li>
        </ul>

    </div>

    <div class="sf-filter__options">
        <table class="sf-options">

            <tbody>

			<?php $filter->render_settings(); ?>

            <tr class="sf-options__footer">
                <td colspan="2">
                    <a href="#" class="sf-button sf-button__main sf-close"><?php _e( 'Close filter', $locale ); ?></a>
                </td>
            </tr>

            </tbody>

        </table>
    </div>
</div>