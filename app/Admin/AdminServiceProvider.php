<?php

namespace SimplyFilters\Admin;

use Hybrid\Core\ServiceProvider;

/**
 * The admin-specific functionality of the plugin.
 *
 * @package    SimplyFilters
 * @subpackage SimplyFilters/Admin
 * @author     Grzegorz Niedzielski <admin@gregn.pl>
 */
class AdminServiceProvider extends ServiceProvider {

	use \SimplyFilters\Assets;

	public function boot() {
		$this->enqueue_styles();
		$this->enqueue_scripts();

		add_action( 'admin_menu', [ $this, 'init_dashboard' ] );
		add_action( 'current_screen', [ $this, 'current_screen' ] );
	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {
		wp_enqueue_style( 'simply-filters_admin', $this->getAssetPath( 'css/admin.css' ), null, null, 'all' );
	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {
		wp_enqueue_script( 'simply-filters_admin', $this->getAssetPath( 'js/admin.js' ), null, null, false );
	}

	/**
	 * Add settings page to WooCommerce menu
	 *
	 * @since   1.0.0
	 */
	public function init_dashboard() {

		add_submenu_page(
			'options-general.php',
			esc_html__( 'Simply Filters', $this->app->get( 'locale' ) ),
			esc_html__( 'Simply Filters', $this->app->get( 'locale' ) ),
			'manage_options',
			$this->app->get( 'plugin_name' ),
			[ $this, 'dashboard_screen' ]
		);
	}

	/**
	 * Callback to render content of settings page
	 *
	 * @since   1.0.0
	 */
	public function dashboard_screen() {
		require_once __DIR__ . '/partials/dashboard-page.php';
	}

	/**
	 * Add custom content to SF admin pages
	 *
	 */
	public function current_screen( $screen ) {

		// Check if current admin screen is either SF post type or SF settings page
		if (
			isset( $screen->post_type ) && $screen->post_type === $this->app->get( 'group_post_type' )
			|| $screen->id === 'settings_page_simply-filters'
		) {
			add_action( 'in_admin_header', array( $this, 'admin_header_section' ) );
		}
	}

	public function admin_header_section() {
		require_once __DIR__ . '/partials/admin-toolbar.php';
	}

}
