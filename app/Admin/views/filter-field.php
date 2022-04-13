<?php
/**
 * @var $filter \SimplyFilters\Filters\Types\Filter
 * @var $order string
 * @var $locale string
 */
?>

<div class="sf-filter" data-filter_id="<?php esc_attr_e( \Hybrid\app('prefix') . $filter->get_id() ); ?>">

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
            <?php esc_html_e( $filter->get_type() ); ?>
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

            <?php

            $filter->render_settings();

            ob_start();
            ?>

            <tr class="sf-option">
                <td>
                    <label for="x1">Filter label</label>
                    <p>Name of the filter that will be displayed above it.</p>
                </td>
                <td>
                    <input type="text" name="x1" id="x1">
                </td>
            </tr>
            <tr class="sf-option">
                <td>
                    <label for="x2">Source</label>
                    <p>Categories, tags and attributes created for products. If you want to filter by i.e. clothing size you need to create an attribute first - learn more.</p>
                </td>
                <td>
                    <select name="x2" id="x2">
                        <option value="1">Attribute</option>
                        <option value="2">Attribute</option>
                        <option value="3">Attribute</option>
                    </select>
                </td>
            </tr>
            <tr class="sf-option">
                <td>
                    <label for="x3">Search relation</label>
                    <p>How to display results when selecting more than 1 option.</p>
                </td>
                <td>
                    <ul class="sf-input-list">
                        <li>
                            <input type="radio" id="huey" name="drone" value="huey"
                                   checked>
                            <label for="huey">AND - Product needs to match all selected options to be shown</label>
                        </li>
                        <li>
                            <input type="radio" id="dewey" name="drone" value="dewey">
                            <label for="dewey">OR - Product needs to match any of selected options to be shown </label>
                        </li>
                    </ul>
                </td>
            </tr>

            <tr class="sf-option">
                <td>
                    <label for="x3">URL label</label>
                    <p>This label will be used in URL when filter is applied. Use only lowercase letters, numbers and hyphens.</p>
                </td>
                <td>
                    <ul class="sf-input-list">
                        <li>
                            <input type="checkbox" id="test1" name="test1" value="test1"
                                   checked>
                            <label for="test1">Setting</label>
                        </li>
                        <li>
                            <input type="checkbox" id="test3" name="test3" value="test1">
                            <label for="test3">Options</label>
                        </li>
                    </ul>
                </td>
            </tr>

            <?php
            ob_get_clean();
            ?>

            <tr class="sf-options__footer">
                <td colspan="2">
                    <a href="#" class="sf-button sf-button__main sf-close"><?php _e( 'Close filter', $locale ); ?></a>
                </td>
            </tr>

            </tbody>
        </table>
    </div>
</div>