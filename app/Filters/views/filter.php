<?php
/**
 * Filter base
 *
 * @var $filter \SimplyFilters\Filters\Types\Filter
 * @var $settings array
 * @var $locale string
 *
 * @since 1.0.0
 */
?>

<?php
/**
 * Fires before filter base is rendered
 *
 * @param \SimplyFilters\Filters\Types\Filter  $filter Filter object
 */
do_action( 'sf-before-filter-render', $filter );
?>

<div class="sf-filter" data-type="<?php echo esc_attr( $filter->get_type() ); ?>" data-id="<?php echo esc_attr( $filter->get_id() ); ?>">

    <div class="sf-filter__heading">

        <span class="sf-filter__label"><?php echo esc_html( $filter->get_label() ); ?></span>

		<?php if ( $settings['collapse'] ) {
			$collapsed = $filter->is_filter_collapsed();
			printf( '<button class="sf-filter__collapse %s" aria-expanded="%s">%s<span class="screen-reader-text">%s</span></button>',
				esc_attr( $collapsed ? 'collapsed' : '' ),
				esc_attr( $collapsed ? 'true' : 'false' ),
				\SimplyFilters\load_inline_svg( 'collapse' ),
				esc_html__( 'Toggle filter visibility', $locale )
			);
		} ?>

    </div>

    <div class="sf-filter__filter <?php echo $settings['collapse'] && $filter->is_filter_collapsed() ? 'sf-filter--collapsed' : ''; ?>">

		<?php $filter->render(); ?>

    </div>

</div>