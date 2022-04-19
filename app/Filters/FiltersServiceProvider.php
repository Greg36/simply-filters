<?php

namespace SimplyFilters\Filters;

use Hybrid\Core\ServiceProvider;
use SimplyFilters\Filters\Types\ColorFilter;
use SimplyFilters\SimplyFilters;

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
	}

	public function boot() {

		add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_styles' ] );
		add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_scripts' ] );

		add_action( 'init', [ $this, 'register_group_post_type' ] );
		add_action( 'init', [ $this, 'register_single_post_type' ] );

		add_action( 'wp_ajax_sf/render_new_field', [ $this, 'ajax_render_new_settings_field' ] );
		add_action( 'wp_ajax_sf/get_color_options', [ $this, 'ajax_get_color_settings_options' ] );

		add_action( 'save_post', array( $this, 'save_post' ), 10, 2 );
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
				'supports'            => array( '' ),
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
				'id'         => uniqid(),
				'label'      => __( '(no label)', $this->app->get( 'locale' ) ),
				'enabled'    => true,
				'sources'    => 'attributes',
				'attributes' => false
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
		$filter->render_settings();

		die();
	}

	public function save_post( $post_id, $post ) {

		// Bail early if WP is doing autos-ave
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return $post_id;
		}

		// Process only saving of filter group
		if ( $post->post_type !== $this->app->get( 'group_post_type' ) ) {
			return $post_id;
		}

		// Do not save revisions
		if ( wp_is_post_revision( $post_id ) ) {
			return $post_id;
		}

		// Verify nonce
		$nonce = isset( $_POST['sf-group-field'] ) ? $_POST['sf-group-field'] : false;
		if ( ! wp_verify_nonce( $nonce, 'sf-group-field' ) ) {
			return $post_id;
		}

		$parser = new DataParser( $post_id );
		$prefix = (string) $this->app->get( 'prefix' );

		// Save filters
		if ( ! empty( $_POST[ $prefix ] ) ) {
			foreach ( $_POST[ $prefix ] as $id => $data ) {
				if( empty( $data ) ) continue;
				$parser->save_filter( $id, $data );
			}
		}

		// Delete filters
		// @todo: replace string with a call to container?
		if ( $_POST['sf-removed-fields'] ) {
			$remove = explode( '|', $_POST['sf-removed-fields'] );
			$remove = array_map( 'intval', $remove );

			foreach ( $remove as $id ) {
				$parser->remove_filter( $id );
			}
		}

		return $post_id;
	}

}
