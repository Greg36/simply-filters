<?php

namespace SimplyFilters;

use SimplyFilters\Admin\Admin;
use SimplyFilters\Filters\Filters;

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since   1.0.0
 * @package SimplyFilters
 */
class SimplyFilters {

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since   1.0.0
	 * @var     string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since   1.0.0
	 * @var     string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * Singleton instance of class
	 *
	 * @since   1.0.0
	 */
	private static $instance;


	/**
	 * Check plugin compatibility and initialize core functionality.
	 *
	 * @since   1.0.0
	 */
	public function __construct() {
		$this->version = defined( 'SF_VERSION' ) ? SF_VERSION : '1.0.0';
		$this->plugin_name = 'simply-filters';

		$this->set_locale();

		// Check compatibility and init the plugin
		if ( ! function_exists( 'WC' ) ) {
			add_action( 'admin_notices', [ $this, 'install_woocommerce_admin_notice' ] );
		} else {
			$this->init();
		}
	}

	/**
	 * Initialize the admin and public hooks
	 *
	 * @since   1.0.0
	 */
	private function init() {

        $admin = new Admin;
        $admin->init();

        $filters = new Filters;
        $filters->init();
	}

	/**
	 * Print an admin notice if woocommerce is deactivated
	 *
	 * @since   1.0.0
	 */
	public function install_woocommerce_admin_notice() {
		?>
		<div class="error">
			<p><?php esc_html_e( 'Simply Filters for WooCommerce plugin is enabled but it requires WooCommerce in order to work.', 'simply-filters' ); ?></p>
		</div>
		<?php
	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * @since   1.0.0
	 */
	private function set_locale() {

		load_plugin_textdomain(
			'simply-filters',
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since   1.0.0
	 * @return  string    The name of the plugin.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since   1.0.0
	 * @return  string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}

	/**
	 * Gets the singleton instance via lazy initialization
	 *
	 * @since   1.0.0
	 * @return  self
	 */
	public static function factory() {
		if ( static::$instance === null ) {
			static::$instance = new static();
		}

		return static::$instance;
	}

}
