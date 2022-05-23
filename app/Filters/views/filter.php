<?php
/**
 * @var $filter \SimplyFilters\Filters\Types\Filter
 */
?>

<div class="sf-filter" data-type="<?php esc_attr_e( $filter->get_type() ); ?>">
    <div class="sf-filter__heading">
        <span class="sf-filter__label"><?php esc_html_e( $filter->get_label() ); ?></span>
    </div>
	<div class="sf-filter__filter">
		<?php $filter->render(); ?>
    </div>
</div>