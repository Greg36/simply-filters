<?php
/**
 * Window opened as popup with selection of all filters
 *
 * @var $registry array List of all available filters
 * @var $locale string
 *
 * @since 1.0.0
 */

?>

<div class="sf-new">
    <div class="sf-new__bg"></div>
    <div class="sf-new__wrap">

        <div class="sf-new__header">
            <h2 class="sf-new__title"><?php esc_html_e( 'Select filter type', $locale ); ?></h2>
            <a href="#" class="sf-new__close"><span class="screen-reader-text"><?php esc_html_e( 'Close popup', $locale ) ?></span></a>
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