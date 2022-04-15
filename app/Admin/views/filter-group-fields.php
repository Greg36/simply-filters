<?php

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

<div class="sf-filters" data-filter_group_id="<?php esc_attr_e( \Hybrid\app('prefix') . get_the_ID() ); ?>">
	<div class="sf-filters__wrap">

        <ul class="sf-filters__header">
            <li><?php _e( 'Enabled', $locale ); ?></li>
            <li><?php _e( 'Label',   $locale ); ?></li>
            <li><?php _e( 'Type',    $locale ); ?></li>
        </ul>

        <div class="sf-filters__list">

            <?php if( $filters ) :

	            foreach ( $filters as $key => $filter ) {
                    \SimplyFilters\TemplateLoader::render( 'filter-field', [
                        'filter' => $filter,
                        'order'  => $key + 1
                    ] );
                }

            else : ?>

                <div class="sf-filters__no-items">
                    <?php _e( 'There are no filters yet.', $locale ); // @todo: better message ?>
                </div>

            <?php endif; ?>

        </div>

        <div class="sf-filters__footer">
            <a href="#" class="sf-button sf-button__main sf-button__new-filter"><img src="<?php echo \SimplyFilters\get_svg( 'plus' ); ?>" alt="Add filter" aria-hidden="true"><?php _e( 'Add new filter', $locale ); ?></a>
        </div>

    </div>
</div>