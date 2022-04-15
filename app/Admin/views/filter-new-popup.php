<?php

/**
 * @var $locale string
 * @var $registry array
 */

?>

<div class="sf-new">
    <div class="sf-new__bg"></div>
	<div class="sf-new__wrap">
        <div class="sf-new__header">
            <h2 class="sf-new__title"><?php _e( 'Select filter type', $locale ); ?></h2>
            <a href="#" class="sf-new__close"><span class="screen-reader-text"><?php _e( 'Close popup', $locale ) ?></span></a>
        </div>

        <div class="sf-new__list">
			<?php
			foreach ( $registry as $filter ) {
				/**
				 * @var $filter \SimplyFilters\Filters\Types\Filter
				 */
				$filter = new $filter;
				$filter->render_new_filter_preview();
			}
			?>
        </div>
    </div>
</div>