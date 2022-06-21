<?php

namespace SimplyFilters;

use Hybrid\Core\Application;

/**
 * The core plugin class.
 *
 * @since   1.0.0
 */
class SimplyFilters {

	/**
	 * @var self Singleton instance of class
	 */
	private static $instance;

	/**
	 * @var Application Application container
	 */
	protected $app;

	/**
	 * Create a new application.
	 */
	public function __construct() {
		$this->app = \Hybrid\booted() ? \Hybrid\app() : new \Hybrid\Core\Application();

		$this->app->instance( 'version', defined( 'SF_VERSION' ) ? SF_VERSION : '1.0.0' );
		$this->app->instance( 'plugin_name', 'simply-filters' );
		$this->app->alias( 'plugin_name', 'locale' );

		// Boot application after plugins have been loaded to check if WooCommerce is active
		add_action( 'plugins_loaded', [ $this, 'boot' ], 11 );
	}

	/**
	 * Check compatibility and boot the application
	 */
	public function boot() {

		$this->set_locale();

		if ( $this->is_woocommerce_active() ) {
			$this->registerServices();
			$this->app->boot();
		} else {
			add_action( 'admin_notices', [ $this, 'install_woocommerce_admin_notice' ] );
		}
	}

	/**
	 * Register application's service providers
	 */
	private function registerServices() {
		$this->app->provider( Filters\FiltersServiceProvider::class );

		if ( is_admin() ) {
			$this->app->provider( Admin\AdminServiceProvider::class );
		}
	}

	/**
	 * Check if WooCommerce is active or active for multisite network
	 *
	 * @return  bool
	 */
	private function is_woocommerce_active() {
		$woocommerce     = 'woocommerce/woocommerce.php';
		$network_plugins = array();

		if ( is_multisite() ) {
			$network_plugins = get_site_option( 'active_sitewide_plugins' );
		}

		$is_active             = in_array( $woocommerce, (array) get_option( 'active_plugins', array() ), true );
		$is_active_for_network = is_multisite() && isset( $network_plugins[ $woocommerce ] );

		return $is_active || $is_active_for_network || defined( 'WP_TESTS_DOMAIN' );
	}

	/**
	 * Print an admin notice if woocommerce is deactivated
	 */
	public function install_woocommerce_admin_notice() {
		?>
        <div class="error">
            <p><?php esc_html_e( 'Simply Filters for WooCommerce plugin is enabled but it requires WooCommerce in order to work.', $this->app->get( 'locale' ) ); ?></p>
        </div>
		<?php
	}

	/**
	 * Define plugin's textdomain
	 */
	private function set_locale() {

		load_plugin_textdomain(
			$this->app->get( 'locale' ),
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);
	}

	/**
	 * Gets the singleton instance via lazy initialization
	 *
	 * @return  self
	 */
	public static function factory() {
		if ( static::$instance === null ) {
			static::$instance = new static();
		}

		return static::$instance;
	}
}
