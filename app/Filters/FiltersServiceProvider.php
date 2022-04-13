<?php

namespace SimplyFilters\Filters;

use Hybrid\Core\ServiceProvider;

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
		$this->app->instance( 'prefix', 'sf-setting-' );
	}

	public function boot() {

		add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_styles' ] );
		add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_scripts' ] );

		add_action( 'init', [ $this, 'register_group_post_type' ] );
		add_action( 'init', [ $this, 'register_single_post_type' ] );
	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {
		wp_enqueue_style( 'simply-filters_public', $this->getAssetPath( 'css/public.css' ), null, null, 'all' );
	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {
		wp_enqueue_script( 'simply-filters_public', $this->getAssetPath( 'js/public.js' ), null, null, true );
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
				'show_ui'             => true,
				'hierarchical'        => false,
				'supports'            => array(''),
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
		);;
	}

}
