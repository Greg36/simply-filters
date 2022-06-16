<?php

namespace SimplyFilters\Filters;

use Hybrid\Core\ServiceProvider;
use SimplyFilters\Filters\Types\ColorFilter;
use function SimplyFilters\adjustBrightness;

/**
 * The public-facing functionality of the plugin.
 *
 * @package    SimplyFilters
 * @subpackage SimplyFilters/Filters
 * @author     Grzegorz Niedzielski <admin@gregn.pl>
 */
class FiltersServiceProvider extends ServiceProvider {

	use \SimplyFilters\Assets;

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
		add_action( 'wp_ajax_sf/render_new_field', [ $this, 'ajax_render_new_settings_field' ] ); // @todo: move?
		add_action( 'wp_ajax_sf/get_color_options', [ $this, 'ajax_get_color_settings_options' ] );// @todo: move?
	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {
		wp_enqueue_style( 'simply-filters_public', $this->getAssetPath( 'css/public.css' ), null, null, 'all' );

		$this->enqueue_dynamic_styles();
	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {
		wp_enqueue_script( 'simply-filters_public', $this->getAssetPath( 'js/public.js' ), null, null, true );

		wp_localize_script( 'simply-filters_public', 'sf_filters', [
			'loader_src'   => \SimplyFilters\get_svg( 'loader' ),
			'price_format' => get_option( 'woocommerce_currency_pos' ),
			'currency'     => get_woocommerce_currency_symbol(),
			'locale'       => [
				'show_less' => __( 'Show less', $this->app->get( 'locale') )
			]
		] );
	}

	/**
	 * Register the files to enqueue in footer page's content has been rendered
	 *
	 * @since    1.0.0
	 */
	public function late_enqueue_scripts() {

		// Enqueue slider script and dependencies if slider filter is used on the page
		if ( $this->app->get( 'enqueue-slider' ) ) {
			wp_enqueue_script( 'simply-filters_slider', $this->getAssetPath( 'js/range-slider.js' ), [ 'jquery', 'jquery-ui-slider' ], null, true );
		}
	}

	/**
	 * Include dynamic style variables
	 */
	private function enqueue_dynamic_styles() {
		$options = get_option( 'sf-settings' );

		// Element colors
		$colors = isset( $options['colors'] ) ? $options['colors'] : [];
		$colors = array_filter( $colors, function ( $option ) {
			return sanitize_hex_color( $option );
		});

		$defaults = [
			'accent' => '#4F76A3',
			'accent-dark' => '',
			'highlight' => '#3987e1',
			'background' => '#ffffff',
			'font_titles' => '#404040',
			'font_options' => '#445C78'
		];
		$colors = wp_parse_args( $colors, $defaults );
		$colors[ 'accent-dark' ] = adjustBrightness( $colors['accent'], -20 );
		$styles = '';
		foreach ( $colors as $key => $option ) {
			if( ! array_key_exists( $key, $defaults ) ) continue;

			$styles .= '--sf-' . $key . ': ' . $option . '; ';
		}

		// Elements style
		$element_style = isset( $options['style'] ) ? esc_attr( $options['style'] ) : 'rounded';
		if( $element_style === 'rounded' ) {
			$styles .= '--sf-corner: 3px; ';
			$styles .= '--sf-corner-button: 5px; ';
		} else {
			$styles .= '--sf-corner: 0; ';
			$styles .= '--sf-corner-button: 0; ';
		}

		wp_add_inline_style( 'simply-filters_public', "
		:root {
			 {$styles}
		}
		" );
	}

	/**
	 * Register custom post type for filter group
	 *
	 * @since 1.0.0
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
					'name'               => __( 'Filters', $locale ),
					'singular_name'      => __( 'Filter', $locale ),
					'add_new_item'       => __( 'Add New Filter Group', $locale ),
					'edit_item'          => __( 'Edit Filter Group', $locale ),
					'new_item'           => __( 'New Filter Group', $locale ),
					'view_item'          => __( 'View Filter Group', $locale ),
					'search_items'       => __( 'Search Filter Groups', $locale ),
					'not_found'          => __( 'No Filter Groups found', $locale ),
					'not_found_in_trash' => __( 'No Filter Groups found in Trash', $locale ),
				),
			)
		);
	}

	/**
	 * Register custom post type for single filter
	 *
	 * @since 1.0.0
	 */
	public function register_single_post_type() {

		register_post_type(
			$this->app->get( 'item_post_type' ),
			array(
				'public'       => false,
				'hierarchical' => false,
				'supports'     => array(),
				'labels'       => array(
					'name' => __( 'Filter Item', $this->app->get( 'locale' ) ),
				),
			)
		);
	}

	/**
	 * Register widgets
	 *
	 * @return void
	 */
	public function register_widgets() {
		register_widget( FilterWidget::class );
	}

	/**
	 * Register blocks
	 *
	 * @return void
	 */
	public function register_blocks() {
		FilterBlock::getInstance();
	}

	/**
	 * Register shortcodes
	 *
	 * @return void
	 */
	public function register_shortcodes() {
		add_shortcode( $this->app->get( 'shortcode_tag' ), function ( $atts ) {
			return ( new FilterShortcode( $atts['group_id'] ) )->getShortcode();
		} );
	}

	public function filter_query( \WP_Query $query ) {

		$filterer = new FilterQuery( $query );
		$filterer->filter();
	}

	/**
	 * Render new filter settings field
	 *
	 * @since 1.0.0
	 */
	public function ajax_render_new_settings_field() {

		// Verify nonce
		if ( ! wp_verify_nonce( $_POST['nonceAjax'], 'wp_rest' ) ) {
			die();
		}

		// Check for type
		$type  = filter_var( $_POST['type'], FILTER_SANITIZE_STRING );
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
				'label'         => __( '(no label)', $this->app->get( 'locale' ) ),
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
	 * Render new color settings options
	 *
	 * @since 1.0.0
	 */
	public function ajax_get_color_settings_options() {

		// Verify nonce
		if ( ! wp_verify_nonce( $_POST['nonceAjax'], 'wp_rest' ) ) {
			die();
		}

		$id       = filter_var( $_POST['filter_id'], FILTER_SANITIZE_STRING );
		$taxonomy = filter_var( $_POST['taxonomy'], FILTER_SANITIZE_STRING );
		$term_id  = filter_var( $_POST['term_id'], FILTER_SANITIZE_STRING );

		// Bail if there is no key or term ID
		if ( ! $taxonomy || ! $term_id || ! $id ) {
			die();
		}

		$filter = new ColorFilter();
		$filter->initialize( [
			'id'      => $id,
			'sources' => $taxonomy,
			$taxonomy => $term_id
		] );

		// Render the filter settings
		$filter->render_setting_fields();

		die();
	}
}
