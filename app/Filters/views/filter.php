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

<div class="sf-filter" data-type="<?php esc_attr_e( $filter->get_type() ); ?>" data-id="<?php esc_attr_e( $filter->get_id() ); ?>">

    <div class="sf-filter__heading">

        <span class="sf-filter__label"><?php esc_html_e( $filter->get_label() ); ?></span>

		<?php if ( $settings['collapse'] ) {
			$collapsed = $filter->is_filter_collapsed();
			printf( '<button class="sf-filter__collapse %s" aria-expanded="%s">%s<span class="screen-reader-text">%s</span></button>',
				$collapsed ? 'collapsed' : '',
				$collapsed ? 'true' : 'false',
				\SimplyFilters\load_inline_svg( 'collapse' ),
				__( 'Toggle filter visibility', $locale )
			);
		} ?>

    </div>

    <div class="sf-filter__filter <?php echo $settings['collapse'] && $filter->is_filter_collapsed() ? 'sf-filter--collapsed' : ''; ?>">

		<?php $filter->render(); ?>

    </div>

</div>