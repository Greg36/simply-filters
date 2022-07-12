<?php
/**
 * Filter group
 *
 * @var $group_id int
 * @var $filters array
 * @var $settings array
 * @var $locale string
 *
 * @since 1.0.0
 */
?>


<?php
/**
 * Fires before filter group is rendered
 */
do_action( 'sf-before-group-render' );
?>

<div class="sf-filter-group" data-action="<?php echo esc_attr( $settings['auto_submit'] ); ?>">

	<?php

	if ( $settings['elements']['clear'] ) {
		printf( '<button class="sf-filter-group__clear sf-button">%s</button>',
			esc_html__( 'Clear filters', $locale )
		);
	}

	if ( $settings['elements']['title'] ) {
		printf( '<div class="sf-filter-group__heading"><h3>%s</h3></div>',
			esc_html( get_the_title( $group_id ) )
		);
	}

	if ( $filters ) {
		foreach ( $filters as $filter ) {

			if ( ! $filter->is_enabled() ) {
				continue;
			}

			\SimplyFilters\TemplateLoader::render( 'filter', [
				'filter'   => $filter,
				'settings' => $settings
			], 'Filters'
			);
		}
	}

	if ( $settings['auto_submit'] === 'onsubmit' ) {
		printf( '<button class="sf-filter-group__submit sf-button">%s</button>',
			esc_html__( 'Apply filters', $locale )
		);
	}

	?>
</div>

<?php
/**
 * Fires after filter group is rendered
 */
do_action( 'sf-after-group-render' );
?>

