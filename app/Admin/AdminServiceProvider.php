<?php

namespace SimplyFilters\Admin;

use Hybrid\Core\ServiceProvider;
use SimplyFilters\Filters\DataParser;
use SimplyFilters\Filters\Types\Filter;
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

		add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_styles' ] );
		add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_scripts' ] );

		add_filter( "manage_{$this->app->get( 'group_post_type' )}_posts_columns", [ $this, 'group_table_columns' ] );
		add_filter( "manage_{$this->app->get( 'group_post_type' )}_posts_custom_column", [ $this, 'group_table_column_shortcode' ], 10, 2 );
		add_filter( 'post_updated_messages', [ $this, 'post_updated_message' ] );
		add_filter( 'post_row_actions', [ $this, 'post_row_actions' ], 10, 2 );

		add_action( 'admin_head', [ $this, 'init_metaboxes' ] );
		add_action( 'admin_menu', [ $this, 'init_settings' ] );

		add_action( 'sf_admin_before_filter_options', [ $this, 'display_rating_disabled_notice' ] );

		add_action( 'save_post', [ $this, 'save_filters' ], 10, 2 );
		add_filter( 'wp_insert_post_data', [ $this, 'save_group_settings' ], 90, 2 );
		add_action( 'delete_post', [ $this, 'remove_group' ], 90, 2 );
		add_action( 'admin_init', [ $this, 'save_general_settings' ], 95 );
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
		wp_enqueue_script( 'simply-filters_admin', $this->getAssetPath( 'js/admin.js' ), [ 'wp-color-picker', 'wp-i18n' ], null, false );

		wp_localize_script( 'simply-filters_admin', 'sf_admin', [
			'prefix'         => \Hybrid\app( 'prefix' ),
			'rest_url'       => get_rest_url(),
			'admin_url'      => get_admin_url(),
			'ajax_url'       => admin_url( 'admin-ajax.php' ),
			'ajax_nonce'     => wp_create_nonce( 'wp_rest' ),
			'loader_src'     => \SimplyFilters\get_svg( 'loader' ),
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
	 * Initialize metaboxes
	 */
	public function init_metaboxes() {

		if ( ! $this->app->get( 'is-page-post' ) ) {
			return;
		}

		$metaboxes = new Metaboxes( get_the_ID() );
		$metaboxes->init_metaboxes();
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

		$locale = \Hybrid\app( 'locale' );
		$settings = new Settings( 'options', get_option( 'sf-settings' ) );

		$settings->add( 'colors', 'color', [
			'name' => __( 'Filter colors', $locale ),
			'description' => __( 'Customize how filters look on the site. Changing colors here will affect all filters.', $locale ),
			'options' => [
				[
					'id' => 'accent',
					'slug' => 'accent',
					'name' => __( 'Accent', $locale ),
					'default' => '#4F76A3'
				],
				[
					'id' => 'highlight',
					'slug' => 'highlight',
					'name' => __( 'Highlight', $locale ),
					'default' => '#3987e1'
				],
				[
					'id' => 'background',
					'slug' => 'background',
					'name' => __( 'Background', $locale ),
					'default' => '#ffffff'
				],
				[
					'id' => 'font_options',
					'slug' => 'font_options',
					'name' => __( 'Option text', $locale ),
					'default' => '#445C78'
				],
				[
					'id' => 'font_titles',
					'slug' => 'font_titles',
					'name' => __( 'Title text', $locale ),
					'default' => '#404040'
				]
			]
		] );

		$settings->add( 'style', 'select', [
			'name' => __( 'Elements style', $locale ),
			'description' => __( 'Overall style of filter elements', $locale ),
			'options' => [
				'rounded' => __( 'Rounded', $locale ),
				'squared' => __( 'Squared', $locale )
			]
		] );

		$settings->add( 'change_selectors', 'toggle', [
			'name' => __( 'Enable custom selectors', $locale ),
			'description' => __( 'If your theme does not refresh products after filtering it might be due to non-standard CSS selectors. Enable this option to enter custom selector values.', $locale )
		] );

		$settings->add( 'selectors', 'text', [
			'name' => __( 'Selectors', $locale ),
			'description' => __( 'Enter CSS selector for each part of the page - it will be replaced with new content on page reload.<br><br>If you don\'t know what values to enter contact your theme\'s support.', $locale ),
			'options' => [
				[
					'key' => 'product',
					'label' => __( 'Products selector', $locale ),
					'default' => '.products'
				],
				[
					'key' => 'pagination',
					'label' => __( 'Pagination selector', $locale ),
					'default' => '.woocommerce-pagination'
				],
				[
					'key' => 'breadcrumbs',
					'label' => __( 'Breadcrumbs selector', $locale ),
					'default' => '.woocommerce-breadcrumb'
				],
				[
					'key' => 'count',
					'label' => __( 'Result count selector', $locale ),
					'default' => '.woocommerce-result-count'
				],
				[
					'key' => 'sorting',
					'label' => __( 'Sorting selector', $locale ),
					'default' => '.woocommerce-ordering'
				],
				[
					'key' => 'title',
					'label' => __( 'Page title selector', $locale ),
					'default' => '.woocommerce-products-header__title'
				],
			]
		]);

		TemplateLoader::render( 'settings-page', [
			'settings' => $settings,
		] );
	}

	/**
	 * Add shortcode column to filter group post type table
	 *
	 * @param $columns
	 *
	 * @return array
	 */
	public function group_table_columns( $columns ) {
		return array(
			'cb'           => '<input type="checkbox" />',
			'title'        => __( 'Title' ),
			'sf_shortcode' => __( 'Shortcode', $this->app->get( 'locale' ) ),
			'date'         => __( 'Date' ),
		);
	}

	/**
	 * Fill shortcode column with read-only input filed to copy shortcode easily
	 *
	 * @param $column
	 * @param $post_id
	 *
	 * @return void
	 */
	public function group_table_column_shortcode( $column, $post_id ) {
		if ( 'sf_shortcode' === $column ) {
			echo sprintf( '<input type="text" readonly="readonly" onclick="this.select();" value="%s"/>',
				esc_attr( '[' . $this->app->get( 'shortcode_tag' ) . ' group_id="' . $post_id . '"]' )
			);
		}
	}

	/**
	 * Change the post updated messages for filter group post type
	 *
	 * @param $messages
	 *
	 * @return array
	 */
	public function post_updated_message( $messages ) {

		$locale = $this->app->get( 'locale' );

		$messages[ (string) $this->app->get( 'group_post_type' ) ] = array(
			1  => __( 'Filter group updated.', $locale ),
			2  => __( 'Filter group updated.', $locale ),
			3  => __( 'Filter group deleted.', $locale ),
			4  => __( 'Filter group updated.', $locale ),
			5  => false, // Revisions
			6  => __( 'Filter group published.', $locale ),
			7  => __( 'Filter group saved.', $locale ),
			8  => __( 'Filter group submitted.', $locale ),
			9  => __( 'Filter group scheduled for.', $locale ),
			10 => __( 'Filter group draft updated.', $locale ),
		);

		return $messages;
	}

	/**
	 * Remove quick edit option from filter group row actions
	 *
	 * @param $actions
	 * @param $post
	 *
	 * @return mixed
	 */
	public function post_row_actions( $actions, $post ) {
		if( ! $this->app->get( 'is-page-list' ) ) return $actions;

		unset( $actions['inline hide-if-no-js'] );
		return $actions;
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

		// Save filter settings
		if ( ! empty( $_POST[ $prefix ] ) ) {
			foreach ( $_POST[ $prefix ] as $id => $data ) {
				if ( empty( $data ) || $id == $post_id ) {
					continue;
				}
				$parser->save_filter( $id, $data );
			}
		}

		// Delete filters
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
	 * On filter group save update post_content with settings data
	 *
	 * @param $data
	 * @param $postarr
	 *
	 * @return mixed
	 */
	public function save_group_settings( $data, $postarr ) {

		// Process only filters group
		if ( $data['post_type'] !== $this->app->get( 'group_post_type' ) ) {
			return $data;
		}

		$prefix   = (string) $this->app->get( 'prefix' );
		$settings = isset( $_POST[ $prefix ][ $postarr['ID'] ] ) ? $_POST[ $prefix ][ $postarr['ID'] ] : [];

		if ( ! empty( $settings ) ) {
			$settings             = wp_unslash( $settings ); // @todo is it safe enough sanitization?
			$data['post_content'] = wp_slash( maybe_serialize( $settings ) );
		}

		return $data;
	}

	public function save_general_settings() {

		// Check for general settings nonce
		if( isset( $_POST['sf-setting'] ) && wp_verify_nonce( sanitize_key( wp_unslash( $_POST['sf-general-settings'] ) ), 'sf-general-settings' ) ) {

			$options = [];
			if( ! empty( $_POST['sf-setting']['options'] ) ) {
				foreach ( $_POST['sf-setting']['options'] as $key => $option ) {
					$options[ sanitize_text_field( $key ) ] = wc_clean( $option );
				}
			}

			update_option( 'sf-settings', $options );
		}
	}

	/**
	 * Add notice to rating filter options about rating being disabled in WC settings
	 *
	 * @param Filter $filter
	 */
	public function display_rating_disabled_notice( Filter $filter ) {
		if ( ! wc_review_ratings_enabled() && $filter->get_type() === 'Rating' ) {

			echo '<div class="sf-filter__notice">';
			printf( __( 'Product rating is currently disabled in WooCommerce, for this filter to work enable star rating in <a href="%s" target="_blank" >settings</a>.', $this->app->get( 'locale' ) ),
				admin_url( 'admin.php?page=wc-settings&tab=products' )
			);
			echo '</div>';
		}
	}
}
