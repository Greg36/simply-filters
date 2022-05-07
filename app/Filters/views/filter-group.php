<?php
/**
 * @var $filters array
 */
?>

<div class="sf-filter-group">
	<?php
	if ( $filters ) {
		foreach ( $filters as $key => $filter ) {
			\SimplyFilters\TemplateLoader::render( 'filter', [
				'filter' => $filter,
				'order'  => $key + 1
			], 'Filters'
			);
		}
	} else {
		// @todo admin notice that there are no filters
	}
	?>
</div>

