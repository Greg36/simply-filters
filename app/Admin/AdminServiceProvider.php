<?php

namespace SimplyFilters\Admin;

use Hybrid\Core\ServiceProvider;
use SimplyFilters\Filters\FilterGroup;
use SimplyFilters\TemplateLoader;
use function SimplyFilters\get_svg;

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
			return;
		}

		// Filters group list
		if ( $screen->id === 'edit-sf_filter_group' ) {
			$this->app->instance( 'is-page-sf', true );
			$this->app->instance( 'is-page-list', true );
			return;
		}

		// Single filter group
		if ( isset( $screen->post_type ) && $screen->post_type === $this->app->get( 'group_post_type' ) ) {
			$this->app->instance( 'is-page-sf', true );
			$this->app->instance( 'is-page-post', true );

			// Edit post
			if ( $pagenow === 'post.php' ) {
				$this->app->instance( 'is-page-edit', true );
			}

			// New post
			if ( $pagenow === 'post-new.php' ) {
				$this->app->instance( 'is-page-new', true );
			}
		}
	}

	/**
	 * Get all screen parameters as array
	 *
	 * @return array
	 */
	private function get_current_screen_parameters() {
		return [
			'is_page_sf'       => $this->app->get( 'is-page-sf' ),
			'is_page_settings' => $this->app->get( 'is-page-settings' ),
			'is_page_list'     => $this->app->get( 'is-page-list' ),
			'is_page_post'     => $this->app->get( 'is-page-post' ),
			'is_page_edit'     => $this->app->get( 'is-page-edit' ),
			'is_page_new'      => $this->app->get( 'is-page-new' ),
		];
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

		$locale = $this->app->get( 'locale' );

		wp_localize_script( 'simply-filters_admin', 'sf_admin', [
			'prefix'         => \Hybrid\app( 'prefix' ),
			'locale'         => [
				'copy'   => __( '(copy)', $locale ),
				'sure'   => __( 'Are you sure?', $locale ),
				'delete' => __( 'Delete', $locale ),
				'cancel' => __( 'Cancel', $locale )
			],
			'rest_url'       => get_rest_url(),
			'admin_url'      => get_admin_url(),
			'ajax_url'       => admin_url( 'admin-ajax.php' ),
			'ajax_nonce'     => wp_create_nonce( 'wp_rest' ),
			'loader_src'     => get_svg( 'loader' ),
			'current_screen' => $this->get_current_screen_parameters()
		] );
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

		if ( ! $this->app->get( 'is-page-post' ) ) {
			return;
		}

		$this->app->instance( 'filter/group', new FilterGroup( get_the_ID() ) );
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
