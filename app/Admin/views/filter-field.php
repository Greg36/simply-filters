<?php
/**
 * Admin filter settings row
 *
 * @var $filter \SimplyFilters\Filters\Types\Filter
 * @var $order string
 * @var $locale string
 *
 * @since 1.0.0
 */
?>

<div class="sf-filter"
     data-filter_id="<?php echo esc_attr( \Hybrid\app( 'prefix' ) . '-' . $filter->get_id() ); ?>"
     data-filter_type="<?php echo esc_attr( $filter->get_type() ); ?>"
>

    <div class="sf-filter__row sf-row">

        <div class="sf-row__order">
			<?php echo esc_html( $order ); ?>
        </div>

        <div class="sf-row__enabled">
			<?php $filter->enabled_switch(); ?>
        </div>

        <div class="sf-row__label">
			<?php echo wp_kses_post( $filter->get_label() ); ?>
        </div>

        <div class="sf-row__type">
			<?php echo wp_kses_post( $filter->get_name() ); ?>
        </div>

        <ul class="sf-row__actions">
            <li><a href="#" class="edit-filter" title="<?php esc_attr_e( 'Edit filter', $locale ) ?>"><?php esc_html_e( 'Edit', $locale ); ?></a></li>
            <li><a href="#" class="duplicate-filter" title="<?php esc_attr_e( 'Duplicate filter', $locale ) ?>"><?php esc_html_e( 'Duplicate', $locale ); ?></a></li>
            <li><a href="#" class="remove-filter" title="<?php esc_attr_e( 'Remove filter', $locale ) ?>"><?php esc_html_e( 'Remove', $locale ); ?></a></li>
        </ul>

    </div>

    <div class="sf-filter__options">

		<?php
		/**
		 * Fires before admin filter options are rendered
		 *
		 * @param \SimplyFilters\Filters\Types\Filter  $filter Filter object
		 */
        do_action( 'sf_admin_before_filter_options', $filter );
        ?>

        <table class="sf-options">
            <tbody>

			<?php $filter->render_setting_fields(); ?>

            <tr class="sf-options__footer">
                <td colspan="2">
                    <a href="#" class="sf-button sf-button__main sf-close"><?php esc_html_e( 'Close filter', $locale ); ?></a>
                </td>
            </tr>

            </tbody>
        </table>

		<?php
		/**
		 * Fires after admin filter options are rendered
		 *
		 * @param \SimplyFilters\Filters\Types\Filter  $filter Filter object
		 */
        do_action( 'sf_admin_after_filter_options', $filter );
        ?>

    </div>

	<?php $filter->render_meta_fields(); ?>

</div>