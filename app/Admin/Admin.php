<?php

namespace SimplyFilters\Admin;

/**
 * The admin-specific functionality of the plugin.
 *
 * @package    SimplyFilters
 * @subpackage SimplyFilters/Admin
 * @author     Grzegorz Niedzielski <admin@gregn.pl>
 */
class Admin {

	use \SimplyFilters\Assets;

	public function init() {
		$this->enqueue_styles();
		$this->enqueue_scripts();

		add_action( 'admin_menu', [ $this, 'init_dashboard' ] );
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
			'woocommerce',
			esc_html__( 'Simply Filters', 'simply-filters' ),
			esc_html__( 'Simply Filters', 'simply-filters' ),
			'manage_options',
			'simply-filters',
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

}
