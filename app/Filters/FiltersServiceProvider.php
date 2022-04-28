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
		$this->app->instance( 'prefix_group', 'sf-group-setting' );
		$this->app->instance( 'term-color-key', 'sf_color' );

		$this->app->instance( 'shortcode_tag', 'sf_filters' );
		$this->app->instance( 'widget_id', 'sf_filters' );
	}

	public function boot() {

		add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_styles' ] );
		add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_scripts' ] );

		add_action( 'init', [ $this, 'register_group_post_type' ] );
		add_action( 'init', [ $this, 'register_single_post_type' ] );

		add_action( 'wp_ajax_sf/render_new_field', [ $this, 'ajax_render_new_settings_field' ] );
		add_action( 'wp_ajax_sf/get_color_options', [ $this, 'ajax_get_color_settings_options' ] );

		add_filter( 'wp_insert_post_data', array( $this, 'save_group_settings' ), 90, 2 );
		add_action( 'save_post', array( $this, 'save_filters' ), 10, 2 );
		add_action( 'delete_post', array( $this, 'remove_group' ), 90, 2 );

		add_action( 'widgets_init', array( $this, 'register_widgets' ) );
		add_action( 'init', array( $this, 'register_blocks' ) );
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

//		$locale = $this->app->get( 'locale' );
//
//		wp_localize_script( 'simply-filters_public', 'sf_public', [
//			'prefix'         => \Hybrid\app( 'prefix' ),
//			'locale'         => [
//
//			],
//			'rest_url'       => get_rest_url(),
//			'admin_url'      => get_admin_url(),
//			'ajax_url'       => admin_url( 'admin-ajax.php' ),
//			'ajax_nonce'     => wp_create_nonce( 'wp_rest' ),
//			'loader_src'     => \SimplyFilters\get_svg( 'loader' ),
//		] );
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
		$filter->render_setting_fields();

		die();
	}

	/**
	 * Save filters settings when saving the group
	 *
	 * @param $post_id
	 * @param $post
	 *
	 * @return mixed
	 */
	public function save_filters( $post_id, $post ) {

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

		// Save settings
		if ( ! empty( $_POST[ $prefix ] ) ) {
			foreach ( $_POST[ $prefix ] as $id => $data ) {
				if ( empty( $data ) || $id == $post_id ) {
					continue;
				}
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


	/**
	 * On filter group save update post_content with settings data
	 *
	 * @param $data
	 * @param $postarr
	 *
	 * @return mixed
	 */
	public function save_group_settings( $data, $postarr ) {
		$prefix   = (string) $this->app->get( 'prefix' );
		$settings = isset( $_POST[ $prefix ][ $postarr['ID'] ] ) ? $_POST[ $prefix ][ $postarr['ID'] ] : [];

		if ( ! empty( $settings ) ) {
			$settings             = wp_unslash( $settings );
			$data['post_content'] = wp_slash( maybe_serialize( $settings ) );
		}

		return $data;
	}

	/**
	 * When deleting filter group, remove all filters from database
	 *
	 * @param $group_id
	 *
	 * @return bool|void
	 */
	public function remove_group( $group_id, $post ) {

		// Process only filter groups
		if ( $post->post_type === $this->app->get( 'group_post_type' ) ) {

			$filters = get_posts(
				array(
					'posts_per_page'   => - 1,
					'post_type'        => \Hybrid\app( 'item_post_type' ),
					'suppress_filters' => true,
					'post_parent'      => $group_id,
					'post_status'      => array( 'publish', 'trash' ),
				)
			);

			// Remove filters
			foreach ( $filters as $filter ) {
				wp_delete_post( $filter->ID, true );
			}

			return true;
		}
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
	public function register_blocks(  ) {
		FilterBlock::getInstance();
	}
}
