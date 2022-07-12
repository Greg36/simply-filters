<?php
/**
 * Top navigation for plugin's pages
 *
 * @since   1.0.0
 */

global $pagenow;

$nav         = [];
$text_domain = Hybrid\app( 'locale' );

// Plugin list page
$nav['filters'] = [
	'url'   => esc_url( add_query_arg(
		'post_type',
		Hybrid\app( 'group_post_type' ),
		get_admin_url() . 'edit.php'
	) ),
	'label' => __( 'Filters', $text_domain )
];

// New filter page
$nav['new'] = [
	'url'   => esc_url( add_query_arg(
		'post_type',
		Hybrid\app( 'group_post_type' ),
		get_admin_url() . 'post-new.php'
	) ),
	'label' => __( 'Add new', $text_domain )
];

// Plugin settings page
$nav['settings'] = [
	'url'   => esc_url( add_query_arg(
		'page',
		Hybrid\app( 'plugin_name' ),
		get_admin_url() . 'options-general.php'
	) ),
	'label' => __( 'Settings', $text_domain )
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
    <h2><?php esc_html_e( 'Simply Filters for WooCommerce', $text_domain ); ?></h2>
	<?php
	foreach ( $nav as $name => $nav_link ) {
		printf( '<a class="sf-admin__toolbar-link %s" href="%s">%s</a>',
			sanitize_html_class( $name === $nav_active ? 'is-active' : '' ),
			esc_url( $nav_link['url'] ),
			esc_html( $nav_link['label'] )
		);
	}
	?>
</div>