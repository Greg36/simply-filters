<?php

namespace SimplyFilters\Filters;

use Hybrid\Core\ServiceProvider;
use SimplyFilters\Filters\Types\ColorFilter;
use function SimplyFilters\adjustBrightness;

/**
 * The public-facing functionality of the plugin
 */
class FiltersServiceProvider extends ServiceProvider {

	use \SimplyFilters\Assets;

	/**
	 * Register filter list and strings to app container
	 */
	public function register() {

		$this->app->instance( 'group_post_type', 'sf_filter_group' );
		$this->app->instance( 'item_post_type', 'sf_filter_item' );
		$this->app->instance( 'prefix', 'sf-setting' );
		$this->app->instance( 'prefix_group', 'sf-group-setting' );
		$this->app->instance( 'term-color-key', 'sf_color' );

		$this->app->instance( 'shortcode_tag', 'sf_filters' );
		$this->app->instance( 'widget_id', 'sf_filters' );

		$this->app->instance( 'filter_registry', [
			'Checkbox' => Types\CheckboxFilter::class,
			'Radio'    => Types\RadioFilter::class,
			'Select'   => Types\SelectFilter::class,
			'Color'    => Types\ColorFilter::class,
			'Rating'   => Types\RatingFilter::class,
			'Slider'   => Types\SliderFilter::class,
		] );
	}

	/**
	 * Hook all actions and filters
	 */
	public function boot() {

		// Scripts and styles
		add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_styles' ] );
		add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_scripts' ] );
		add_action( 'wp_footer', [ $this, 'late_enqueue_scripts' ] );

		// Post types
		add_action( 'init', [ $this, 'register_group_post_type' ] );
		add_action( 'init', [ $this, 'register_single_post_type' ] );

		// Display filters
		add_action( 'widgets_init', [ $this, 'register_widgets' ] );
		add_action( 'init', [ $this, 'register_blocks' ] );
		add_action( 'init', [ $this, 'register_shortcodes' ] );

		// Filtering
		if ( ! is_admin() ) {
			add_action( 'woocommerce_product_query', [ $this, 'filter_query' ] );
		}

		// Ajax calls
		add_action( 'wp_ajax_sf/render_new_field', [ $this, 'ajax_render_new_settings_field' ] );
		add_action( 'wp_ajax_sf/get_color_options', [ $this, 'ajax_get_color_settings_options' ] );
	}

	/**
	 * Register the stylesheets for the public-facing side of the site
	 */
	public function enqueue_styles() {
		wp_enqueue_style( 'simply-filters_public', $this->getAssetPath( 'css/public.css' ), null, null, 'all' );

		$this->enqueue_dynamic_styles( 'simply-filters_public' );
	}

	/**
	 * Register the JavaScript for the public-facing side of the site
	 */
	public function enqueue_scripts() {
		wp_enqueue_script( 'simply-filters_public', $this->getAssetPath( 'js/public.js' ), [ 'wp-i18n' ], null, true );

		wp_localize_script( 'simply-filters_public', 'sf_filters', [
			'loader_src'   => \SimplyFilters\get_svg( 'loader' ),
			'price_format' => get_option( 'woocommerce_currency_pos' ),
			'currency'     => get_woocommerce_currency_symbol(),
			'selectors'    => $this->get_custom_selectors()
		] );
	}

	/**
	 * Register the files to enqueue in footer page's content has been rendered
	 */
	public function late_enqueue_scripts() {

		// Enqueue slider script and dependencies if slider filter is used on the page
		if ( $this->app->get( 'enqueue-slider' ) ) {
			wp_enqueue_script( 'simply-filters_slider', $this->getAssetPath( 'js/range-slider.js' ), [ 'jquery', 'jquery-ui-slider' ], null, true );
		}
	}

	/**
	 * Register custom post type for filter group
	 */
	public function register_group_post_type() {

		$locale = $this->app->get( 'locale' );

		register_post_type(
			$this->app->get( 'group_post_type' ),
			array(
				'public'              => false,
				'has_archive'         => false,
				'publicaly_queryable' => false,
				'show_in_menu'        => 'woocommerce',
				'show_in_admin_bar'   => false,
				'show_in_rest'        => true,
				'show_ui'             => true,
				'hierarchical'        => false,
				'supports'            => array( 'title' ),
				'labels'              => array(
					'name'               => esc_html__( 'Filters', $locale ),
					'singular_name'      => esc_html__( 'Filter', $locale ),
					'add_new_item'       => esc_html__( 'Add New Filter Group', $locale ),
					'edit_item'          => esc_html__( 'Edit Filter Group', $locale ),
					'new_item'           => esc_html__( 'New Filter Group', $locale ),
					'view_item'          => esc_html__( 'View Filter Group', $locale ),
					'search_items'       => esc_html__( 'Search Filter Groups', $locale ),
					'not_found'          => esc_html__( 'No Filter Groups found', $locale ),
					'not_found_in_trash' => esc_html__( 'No Filter Groups found in Trash', $locale ),
				),
			)
		);
	}

	/**
	 * Register custom post type for single filter
	 */
	public function register_single_post_type() {

		register_post_type(
			$this->app->get( 'item_post_type' ),
			array(
				'public'       => false,
				'hierarchical' => false,
				'supports'     => array(),
				'labels'       => array(
					'name' => esc_html__( 'Filter Item', $this->app->get( 'locale' ) ),
				),
			)
		);
	}

	/**
	 * Register widgets
	 */
	public function register_widgets() {
		register_widget( FilterWidget::class );
	}

	/**
	 * Register blocks
	 */
	public function register_blocks() {
		FilterBlock::getInstance();
	}

	/**
	 * Register shortcodes
	 */
	public function register_shortcodes() {
		add_shortcode( $this->app->get( 'shortcode_tag' ), function ( $atts ) {
			return ( new FilterShortcode( $atts['group_id'] ) )->getShortcode();
		} );
	}

	/**
	 * Do the actual filtering of the product query
	 *
	 * @param \WP_Query $query Main WooCommerce product query reference
	 */
	public function filter_query( \WP_Query $query ) {

		$filterer = new FilterQuery( $query );
		$filterer->filter();

		$this->app->instance( 'is-woocommerce-page', true );
	}

	/**
	 * AJAX render new filter admin settings field
	 */
	public function ajax_render_new_settings_field() {

		// Verify nonce
		$nonce = isset( $_POST['nonceAjax'] ) ? filter_var( $_POST['nonceAjax'], FILTER_SANITIZE_STRING ) : false;
		if ( ! wp_verify_nonce( $nonce, 'wp_rest' ) ) {
			die();
		}


		// Check for type
		$type  = isset( $_POST['type'] ) ? filter_var( $_POST['type'], FILTER_SANITIZE_STRING ) : '';
		$class = "SimplyFilters\\Filters\\Types\\{$type}Filter";

		if ( class_exists( $class ) ) {

			/**
			 * Instantiate new filter with blank data
			 *
			 * @var $filter Types\Filter
			 */
			$filter = new $class;
			$filter->initialize( [
				'id'            => uniqid(),
				'label'         => esc_html__( '(no label)', $this->app->get( 'locale' ) ),
				'enabled'       => true,
				'sources'       => 'attributes',
				'attributes'    => false,
				'load_defaults' => true,
			] );

			// Render the filter field row
			\SimplyFilters\TemplateLoader::render( 'filter-field', [
				'filter' => $filter,
				'order'  => 0
			] );
		}

		die();
	}

	/**
	 * AJAX render new color settings options
	 */
	public function ajax_get_color_settings_options() {

		// Verify nonce
		$nonce = isset( $_POST['nonceAjax'] ) ? filter_var( $_POST['nonceAjax'], FILTER_SANITIZE_STRING ) : false;
		if ( ! wp_verify_nonce( $nonce, 'wp_rest' ) ) {
			die();
		}


		$id        = isset( $_POST['filter_id'] ) ? filter_var( $_POST['filter_id'], FILTER_SANITIZE_NUMBER_INT ) : false;
		$taxonomy  = isset( $_POST['taxonomy'] ) ? filter_var( $_POST['taxonomy'], FILTER_SANITIZE_STRING ) : false;
		$term_slug = isset( $_POST['term_id'] ) ? filter_var( $_POST['term_id'], FILTER_SANITIZE_STRING ) : false;

		// Bail if there is no key or term ID
		if ( ! $taxonomy || ! $term_slug || ! $id ) {
			die();
		}

		$filter = new ColorFilter();
		$filter->initialize( [
			'id'      => $id,
			'sources' => sanitize_text_field( $taxonomy ),
			$taxonomy => $term_slug
		] );

		// Render the filter settings
		$filter->render_setting_fields();

		die();
	}

	/**
	 * Get custom CSS selectors from general options
	 *
	 * @return array|false
	 */
	private function get_custom_selectors() {
		$options = get_option( 'sf-settings' );
		if ( isset( $options['change_selectors'] ) && $options['change_selectors'] ) {

			// Move product selector to the first element of array
			$product = $options['selectors']['product'];
			unset( $options['selectors']['product'] );

			return [ 'product' => $product ] + $options['selectors'];
		}

		return false;
	}
}
