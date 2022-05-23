<?php
/**
 * @var $group_id int
 * @var $filters array
 * @var $settings array
 */
?>

<div class="sf-filter-group">
    
    <?php

    if( $settings['elements']['title'] ) {
        printf( '<div class="sf-filter-group__heading"><h3>%s</h3></div>',
            esc_html( get_the_title( $group_id ) )
        );
    }

	if ( $filters ) {
		foreach ( $filters as $filter ) {

            if( ! $filter->is_enabled() ) continue;

            \SimplyFilters\TemplateLoader::render( 'filter', [
				'filter' => $filter,
				'settings'  => $settings
			], 'Filters'
			);
		}
	} else {
		// @todo admin notice that there are no filters
	}

	?>
</div>

