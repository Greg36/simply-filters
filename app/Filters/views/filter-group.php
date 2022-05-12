<?php
/**
 * @var $filters array
 * @var $settings array
 */

echo '<pre>';
print_r( $settings );
echo '</pre>';
die();
?>

<div class="sf-filter-group">
	<?php
	if ( $filters ) {
		foreach ( $filters as $key => $filter ) {
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

