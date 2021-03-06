<?php
/**
 * Tab navigation for filter group metaboxes
 *
 * @since 1.0.0
 */
?>

<div class="sf-tabs">
    <ul class="sf-tabs__list">
        <li class="sf-tabs__tab">
            <a href="#sf-filters" class="sf-tabs__link active">
				<?php echo \SimplyFilters\load_inline_svg( 'settings-filter' ); ?>
                <span><?php esc_html_e( 'Edit filters', \Hybrid\app( 'locale' ) ); ?></span>
            </a>
        </li>
        <li class="sf-tabs__tab">
            <a href="#sf-settings" class="sf-tabs__link">
				<?php echo \SimplyFilters\load_inline_svg( 'settings-group' ); ?>
                <span><?php esc_html_e( 'Group settings', \Hybrid\app( 'locale' ) ); ?></span>
            </a>
        </li>
    </ul>
</div>
