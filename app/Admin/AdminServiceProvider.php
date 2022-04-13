<?php

namespace SimplyFilters\Admin;

use Hybrid\Core\ServiceProvider;
use SimplyFilters\TemplateLoader;

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

		add_action( 'current_screen', [ $this, 'current_screen' ] );
//		add_action( 'save_post', array( $this, 'save_post' ), 10, 2 );

		add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_styles' ] );
		add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_scripts' ] );

		add_action( 'admin_head', [ $this, 'init_filters_group' ] );
		add_action( 'admin_menu', [ $this, 'init_settings' ] );
	}

	/**
	 * Load content for the current screen
	 *
	 * @since   1.0.0
	 */
	public function current_screen( $screen ) {

		// Detect current page
		$this->get_current_page( $screen );

		// Load admin header on any sf page
		if ( $this->app->get( 'is-page-sf' ) ) {
			add_action( 'in_admin_header', array( $this, 'admin_header_section' ) );
		}
	}

	/**
	 * Detect attributes of a current admin page
	 *
	 * @param $screen \WP_Screen Admin screen API instance
	 */
	private function get_current_page( $screen ) {

		global $pagenow;

		// Admin settings page
		if ( $screen->id === 'settings_page_simply-filters' ) {
			$this->app->instance( 'is-page-sf', true );
			$this->app->instance( 'is-page-settings', true );
		}

		// Filters group list
		if( $screen->id === 'edit-sf_filter_group' ) {
			$this->app->instance( 'is-page-sf', true );
			$this->app->instance( 'is-page-list', true );
		}

		// Single filter group
		if( isset( $screen->post_type ) && $screen->post_type === $this->app->get( 'group_post_type' ) ) {
			$this->app->instance( 'is-page-sf', true );
			$this->app->instance( 'is-page-post', true );

			// Edit post
			if( $pagenow === 'post.php' ) $this->app->instance( 'is-page-edit', true );

			// New post
			if( $pagenow === 'post-new.php' ) $this->app->instance( 'is-page-new', true );
		}
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
		wp_enqueue_script( 'simply-filters_admin', $this->getAssetPath( 'js/admin.js' ), [ 'wp-color-picker' ], null, false );

//		// Filter post type related scripts
//		if ( $this->app->get( 'is-page-post' ) ) {
//			wp_enqueue_script( 'simply-filters_admin-filters', $this->getAssetPath( 'js/admin-filters.js' ), , null, false );
//		}
	}


	/**
	 * Print admin header section
	 *
	 * @since   1.0.0
	 */
	public function admin_header_section() {
		require_once __DIR__ . '/views/admin-toolbar.php';
	}

	/**
	 * Initialize filters group settings
	 */
	public function init_filters_group() {

		$filter_id = false;

		if( $this->app->get( 'is-page-edit' ) ) {
			$filter_id = get_the_ID();
		}

		$this->app->instance( 'filter/group', new FilterGroup( $filter_id ) );
	}

	/**
	 * Add settings page to WooCommerce menu
	 *
	 * @since   1.0.0
	 */
	public function init_settings() {

		add_submenu_page(
			'options-general.php',
			esc_html__( 'Simply Filters', $this->app->get( 'locale' ) ),
			esc_html__( 'Simply Filters', $this->app->get( 'locale' ) ),
			'manage_options',
			$this->app->get( 'plugin_name' ),
			[ $this, 'settings_screen' ]
		);
	}

	/**
	 * Callback to render content of settings page
	 *
	 * @since   1.0.0
	 */
	public function settings_screen() {
		TemplateLoader::render( 'settings-page', [ 'test' => 'xd' ] );
	}

}
