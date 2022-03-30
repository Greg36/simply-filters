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

<div class="sf-filters">
	<div class="sf-filters__wrap">

        <ul class="sf-filters__header">
            <li><?php _e( 'Enabled', $locale ); ?></li>
            <li><?php _e( 'Label',   $locale ); ?></li>
            <li><?php _e( 'Type',    $locale ); ?></li>
        </ul>

        <div class="sf-filters__list">

            <?php if( $filters ) :

	            foreach ( $filters as $filter ) {
                    \SimplyFilters\TemplateLoader::render( 'filter-field', [
                            'filter' => $filter
                    ] );
                }

            else : ?>

                <div class="sf-filters__no-items">
                    <?php _e( 'There are no filters yet.', $locale ); // @todo: better message ?>
                </div>

            <?php endif; ?>

        </div>

    </div>
</div>
