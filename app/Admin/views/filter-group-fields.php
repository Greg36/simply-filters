<?php
/**
 * Main admin table for filter group
 *
 * @var $filters array
 * @var $locale string
 *
 * @since   1.0.0
 */

?>

<div class="sf-filters sf-tabs-target open" id="sf-filters" data-filter_group_id="<?php esc_attr_e( \Hybrid\app( 'prefix' ) . '-' . get_the_ID() ); ?>">
    <div class="sf-filters__wrap">

        <ul class="sf-filters__header">
            <li><?php _e( 'Enabled', $locale ); ?></li>
            <li><?php _e( 'Label', $locale ); ?></li>
            <li><?php _e( 'Type', $locale ); ?></li>
        </ul>

        <div class="sf-filters__list">

			<?php
			foreach ( $filters as $key => $filter ) {
				\SimplyFilters\TemplateLoader::render( 'filter-field', [
					'filter' => $filter,
					'order'  => $key + 1
				] );
			}
			?>

            <div class="sf-filters__no-items" style="<?php echo $filters ? 'display:none;' : ''; ?>">
				<?php _e( 'No filters. Click <strong>Add new filter</strong> button to add first one.', $locale ); ?>
            </div>

        </div>

        <div class="sf-filters__footer">
            <a href="#" class="sf-button sf-button__main sf-button__new-filter"><img src="<?php echo \SimplyFilters\get_svg( 'plus' ); ?>" alt="Add filter" aria-hidden="true"><?php _e( 'Add new filter', $locale ); ?></a>
        </div>

        <input type="hidden" id="sf-removed-fields" name="sf-removed-fields">

		<?php wp_nonce_field( 'sf-group-field', 'sf-group-field' ); ?>

    </div>
</div>