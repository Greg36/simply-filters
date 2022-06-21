<?php
/**
 * Top navigation for plugin's pages
 *
 * @since   1.0.0
 */

global $pagenow;

$nav    = [];
$locale = Hybrid\app( 'locale' );

// Plugin list page
$nav['filters'] = [
	'url'   => esc_url( add_query_arg(
		'post_type',
		Hybrid\app( 'group_post_type' ),
		get_admin_url() . 'edit.php'
	) ),
	'label' => __( 'Filters', $locale )
];

// New filter page
$nav['new'] = [
	'url'   => esc_url( add_query_arg(
		'post_type',
		Hybrid\app( 'group_post_type' ),
		get_admin_url() . 'post-new.php'
	) ),
	'label' => __( 'Add new', $locale )
];

// Plugin settings page
$nav['settings'] = [
	'url'   => esc_url( add_query_arg(
		'page',
		Hybrid\app( 'plugin_name' ),
		get_admin_url() . 'options-general.php'
	) ),
	'label' => __( 'Settings', $locale )
];

// Detect active page
$nav_active = false;
switch ( $pagenow ) {
	case 'post-new.php':
		$nav_active = 'new';
		break;
	case 'edit.php':
		$nav_active = 'filters';
		break;
	case 'options-general.php':
		$nav_active = 'settings';
		break;
}
?>

<div class="sf-admin__toolbar">
    <h2><?php _e( 'Simply Filters for WooCommerce', $locale ); ?></h2>
	<?php
	foreach ( $nav as $name => $link ) {
		printf( '<a class="sf-admin__toolbar-link %s" href="%s">%s</a>',
			$name === $nav_active ? 'is-active' : '',
			esc_url( $link['url'] ),
			esc_html( $link['label'] )
		);
	}
	?>
</div>