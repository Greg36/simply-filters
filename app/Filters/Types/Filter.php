<?php

namespace SimplyFilters\Filters\Types;

use SimplyFilters\Admin\Settings;
use SimplyFilters\Filters\FilterGroup;

abstract class Filter {

	/**
	 * @var string Filter's post ID
	 */
	protected $id;

	/**
	 * @var array Filter's data from unserialized post content
	 */
	protected $data;

	/**
	 * Type of the filter
	 *
	 * @var string
	 */
	protected $type;

	/**
	 * Name of the filter
	 *
	 * @var string
	 */
	protected $name;

	/**
	 * Description of the filter
	 *
	 * @var string
	 */
	protected $description;

	/**
	 * Is the filter enabled
	 *
	 * @var bool
	 */
	protected $enabled;

	/**
	 * @var Settings Settings handler
	 */
	protected $settings;

	/**
	 * @var $group Filter's current group
	 */
	protected $group;

	/**
	 * @var array Supported settings
	 */
	protected $supports = [];

	/**
	 * @var array Sources for current filter
	 */
	protected $sources = [];

	/**
	 * @var string Text domain locale
	 */
	protected $locale;

	abstract protected function filter_preview();

	/**
	 * Initialize filter with the config data
	 *
	 * @param $data array
	 */
	public function initialize( $data ) {
		$this->data    = $data;
		$this->id      = $data['id'];
		$this->enabled = isset( $data['enabled'] ) ? (bool) $data['enabled'] : true;
		$this->locale  = \Hybrid\app( 'locale' );

		$this->set_sources();
		if ( is_admin() ) {
			$this->load_settings();
		}
	}

	/**
	 * Set group filter belongs to
	 *
	 * @param FilterGroup $group
	 */
	public function set_group( $group ) {
		$this->group = $group;
	}

	/**
	 * Get ID of the field
	 *
	 * @return string
	 */
	public function get_id() {
		return $this->data['id'];
	}


	/**
	 * Get filter label
	 *
	 * @return string
	 */
	public function get_label() {
		return $this->data['label'];
	}

	/**
	 * Get filter type
	 *
	 * @return string
	 */
	public function get_type() {
		return $this->type;
	}

	/**
	 * Get filter description
	 *
	 * @return string
	 */
	public function get_description() {
		return $this->description;
	}

	/**
	 * Get filter name
	 *
	 * @return string
	 */
	public function get_name() {
		return $this->name;
	}

	/**
	 * Check if the filter is set to enabled
	 *
	 * @return bool
	 */
	public function is_enabled() {
		return $this->enabled;
	}

	/**
	 * Check in cookie if filter is collapsed
	 *
	 * @return bool
	 */
	public function is_filter_collapsed() {
		if ( isset( $_COOKIE['sf-filters-collapsed'] ) ) {
			$ids = explode( '|', $_COOKIE['sf-filters-collapsed'] );
			if ( in_array( $this->get_id(), $ids ) ) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Return filter's data value
	 *
	 * @param $key
	 *
	 * @return mixed|string
	 */
	public function get_data( $key, $default = '' ) {
		return isset( $this->data[ $key ] ) ? $this->data[ $key ] : $default;
	}

	/**
	 * Load filter's settings
	 *
	 * @return void
	 */
	protected function load_settings() {
		$this->settings = new Settings( $this->get_id(), $this->data );
		$this->load_supported_settings();
	}

	/**
	 * Load filter's supported settings
	 */
	protected function load_supported_settings() {

		// Filter label
		if ( in_array( 'label', $this->supports, true ) ) {
			$this->settings->add( 'label', 'text', [
				'name'        => __( 'Filter label', $this->locale ),
				'description' => __( 'Name of the filter that will be displayed above it', $this->locale ),
				'required'    => true
			] );
		}

		// URL label
		if ( in_array( 'url-label', $this->supports, true ) ) {
			$this->settings->add( 'url-label', 'text', [
				'name'        => __( 'URL label', $this->locale ),
				'description' => __( 'This label will be used in URL when filter is applied. <br>Use only lowercase letters, numbers and hyphens.', $this->locale ),
				'unique'      => true
			] );
		}

		// Query type
		if ( in_array( 'query', $this->supports, true ) ) {
			$this->settings->add( 'query', 'radio', [
				'name'        => __( 'Search relation', $this->locale ),
				'description' => __( 'How to display results when selecting more than 1 option', $this->locale ),
				'options'     => [
					'or'  => __( 'OR - Product needs to match any of selected options to be shown ', $this->locale ),
					'and' => __( 'AND - Product needs to match all selected options to be shown', $this->locale )
				],
				'default'     => 'or'
			] );
		}

		// Source
		if ( in_array( 'sources', $this->supports, true ) ) {

			$this->settings->add( 'sources', 'select', [
				'name'        => __( 'Sources', $this->locale ),
				'description' => sprintf(
					__( 'Categories, tags and attributes created for products. If you want to filter by i.e. clothing size you need to create an attribute first - <a href="%s" target="_blank">learn more</a>.', $this->locale ),
					'https://woocommerce.com/document/managing-product-taxonomies/'
				),
				'options'     => $this->sources
			] );

			// Load controls for each source field
			$this->add_source_settings();
		}

		// All options label
		if ( in_array( 'all_option', $this->supports, true ) ) {
			$this->settings->add( 'all_option', 'text', [
				'name'        => __( 'All elements label', $this->locale ),
				'description' => __( 'First option selected by default have all elements show, what should be its label i.e. "All brands"', $this->locale ),
				'required'    => true
			] );
		}

		// Products count
		if ( in_array( 'count', $this->supports, true ) ) {
			$this->settings->add( 'count', 'toggle', [
				'name'        => __( 'Display product count', $this->locale ),
				'description' => __( 'Show/hide product count next to options', $this->locale ),
				'default'     => true
			] );
		}

		// Order by
		if ( in_array( 'order_by', $this->supports, true ) ) {
			$this->settings->add( 'order_by', 'select', [
				'name'        => __( 'Order by', $this->locale ),
				'description' => __( 'Select how options should be ordered. By default you can go to category or attribute and manually change order.', $this->locale ),
				'options'     => [
					'default' => __( 'Default', $this->locale ),
					'name'    => __( 'Name', $this->locale ),
					'count'   => __( 'Count', $this->locale )
				],
				'default'     => 'order'
			] );
		}

		// Order type
		if ( in_array( 'order_type', $this->supports, true ) ) {
			$this->settings->add( 'order_type', 'select', [
				'name'        => __( 'Order type', $this->locale ),
				'description' => __( 'Type by which options are ordered', $this->locale ),
				'options'     => [
					'asc'  => __( 'ASC', $this->locale ),
					'desc' => __( 'DESC', $this->locale )
				],
				'default'     => 'desc'
			] );
		}
	}

	/**
	 * Render filter's settings
	 *
	 * @return void
	 */
	public function render_setting_fields() {
		$this->settings->render();
	}

	/**
	 * Set filter sources options
	 */
	protected function set_sources() {
		$this->sources = $this->get_default_sources();
	}

	/**
	 * Get all default sources options
	 *
	 * @return array
	 */
	public function get_default_sources() {
		return [
			'attributes'   => __( 'Attributes', $this->locale ),
			'product_cat'  => __( 'Product category', $this->locale ),
			'product_tag'  => __( 'Product tags', $this->locale ),
			'stock_status' => __( 'Stock status', $this->locale )
		];
	}

	/**
	 * Add settings for supported sources
	 */
	protected function add_source_settings() {

		// Attributes
		if ( array_key_exists( 'attributes', $this->sources ) ) {
			$this->settings->add( 'attributes', 'select', [
				'name'        => __( 'Attribute', $this->locale ),
				'description' => sprintf(
					__( 'Select one of the attributes <br> You can edit and add new in <a href="%s" target="_blank" >attributes panel</a>', $this->locale ),
					admin_url( 'edit.php?post_type=product&page=product_attributes' )
				),
				'options'     => \SimplyFilters\get_attributes()
			] );
		}

		// Categories
		if ( array_key_exists( 'product_cat', $this->sources ) ) {
			$this->settings->add( 'product_cat', 'select', [
				'name'        => __( 'Product category', $this->locale ),
				'description' => sprintf(
					__( 'Select all categories or one to show it\'s children <br> You can edit and add new in <a href="%s" target="_blank">categories panel</a>', $this->locale ),
					admin_url( 'edit-tags.php?taxonomy=product_cat&post_type=product' )
				),
				'options'     => \SimplyFilters\get_product_categories()
			] );
		}
	}

	/**
	 * Get values from the currently selected source option if there is one
	 *
	 * @return array
	 */
	protected function get_current_source_options() {

		// Bail early if no source is set
		if ( ! isset( $this->data['sources'] ) || ! $this->data['sources'] ) {
			return [];
		}

		switch ( $this->data['sources'] ) {
			case 'attributes' :

				// Set default attribute if none is selected
				if ( ! $this->data['attributes'] ) {
					$attributes               = wc_get_attribute_taxonomies();
					$this->data['attributes'] = 'pa_' . array_shift( $attributes )->attribute_name;
				}

				return \SimplyFilters\get_terms_list( [
					'taxonomy' => $this->data['attributes']
				] );

			case 'product_cat' :

				$args = [ 'taxonomy' => 'product_cat' ];

				if ( $this->data['product_cat'] !== 'all' ) {
					$args['parent'] = $this->data['product_cat'];
				}

				return \SimplyFilters\get_terms_list( $args );

			case 'product_tag' :

				return \SimplyFilters\get_terms_list( [
					'taxonomy' => 'product_tag'
				] );

			default :

				return [];
		}

	}

	/**
	 * Get order options from filter's settings
	 *
	 * @return array
	 */
	protected function order_options( $options, $count ) {

		$orderby = $this->data['order_by'];
		$order   = $this->data['order_type'];

		// Keep all setting at the top
		$first = $options[0]['slug'] === 'no-filter' ? [ array_shift( $options ) ] : [];

		if ( $orderby === 'name' ) {
			usort( $options, function ( $a, $b ) {
				return strcmp( $a['name'], $b['name'] );
			} );
		}

		if ( $orderby === 'count' && ! empty( $count ) ) {
			usort( $options, function ( $a, $b ) use ( $count ) {
				$a = isset( $count[ $a['id'] ] ) ? intval( $count[ $a['id'] ] ) : 0;
				$b = isset( $count[ $b['id'] ] ) ? intval( $count[ $b['id'] ] ) : 0;

				return ( $a < $b ) ? - 1 : 1;
			} );
		}

		if ( $order === 'desc' ) {
			$options = array_reverse( $options );
		}

		return array_merge( $first, $options );
	}

	/**
	 * Return source key to be used as a URL slug
	 *
	 * @return string
	 */
	protected function get_current_source_key() {

		if ( ! isset( $this->data['sources'] ) || ! $this->data['sources'] ) {
			return '';
		}

		switch ( $this->data['sources'] ) {
			case 'attributes' :
				return preg_replace( '/^pa_/', '', $this->data['attributes'] );

			case 'product_cat' :
				return get_option( 'woocommerce_product_category_slug' ) ? get_option( 'woocommerce_product_category_slug' ) : _x( 'product-category', 'slug', $this->locale );

			case 'product_tag' :
				return get_option( 'woocommerce_product_tag_slug' ) ? get_option( 'woocommerce_product_tag_slug' ) : _x( 'product-tag', 'slug', $this->locale );

			case 'stock_status' :
				return _x( 'stock-status', 'slug', $this->locale );
		}

		return '';
	}

	/**
	 * Return source taxonomy
	 *
	 * @return string
	 */
	protected function get_current_source_taxonomy() {

		if ( ! isset( $this->data['sources'] ) || ! $this->data['sources'] ) {
			return '';
		}

		if ( $this->data['sources'] === 'attributes' ) {
			return $this->data['attributes'];
		}

		return $this->data['sources'];
	}

	/**
	 * Get filter's data required to render
	 *
	 * @return array|false
	 */
	protected function get_render_data() {
		$options = $this->get_current_source_options();
        $key = $this->get_current_source_key();
		if ( empty( $options ) ) {
			return false;
		}

		$count = $this->get_product_counts_in_terms( $options, $key );

		return [
			'id'       => $this->get_id(),
			'key'      => $key,
			'options'  => $this->order_options( $options, $count ),
			'values'   => $this->get_selected_values(),
			'settings' => [
				'group' => $this->get_group_settings(),
				'query' => $this->get_data( 'query', 'or' ),
				'count' => $count
			]
		];
	}

	public function render_new_filter_preview() {
		?>
        <div class="sf-preview">
            <div class="sf-preview__heading">
                <h4><?php esc_html_e( $this->get_name() ); ?></h4>
                <p><?php esc_html_e( $this->get_description() ); ?></p>
            </div>
            <div class="sf-preview__body">
				<?php $this->filter_preview(); ?>
            </div>
            <div class="sf-preview__footer">
                <a href="#" data-type="<?php esc_attr_e( $this->get_type() ); ?>" class="select-filter sf-button"><?php _e( 'Select filter', $this->locale ); ?></a>
            </div>
        </div>
		<?php
	}


	/**
	 * Return selected values in the filter taken from URL params
	 *
	 * @return array
	 */
	protected function get_selected_values() {
		$params = \Hybrid\app( 'filter-values' );
		if ( empty( $params ) ) {
			return [];
		}

		$key = $this->get_current_source_taxonomy();

		foreach ( $params as $param ) {
			if ( $param['key'] === $key ) {
				return $param['data'];
			}
		}

		return [];
	}

	public function render_meta_fields() {

		// Filter type field
		echo $this->meta_field( 'type', $this->get_type() );

		// Menu order field
		echo $this->meta_field( 'menu_order', $this->get_data( 'menu_order' ) );
	}

	private function meta_field( $label, $value = '' ) {

		$prefix = \Hybrid\app( 'prefix' );

		return sprintf( '<input type="hidden" id="%s" name="%s" %s>',
			esc_attr( sprintf( '%s-%s-%s', $prefix, $this->get_id(), $label ) ),
			esc_attr( sprintf( '%s[%s][%s]', $prefix, $this->get_id(), $label ) ),
			$value !== '' ? sprintf( ' value="%s" ', esc_attr( $value ) ) : ''
		);
	}

	public function enabled_switch() {

		$prefix = \Hybrid\app( 'prefix' );

		echo '<label class="sf-switch">';
		printf( '<input type="checkbox" id="%s" name="%s" %s>',
			esc_attr( sprintf( '%s-%s-enabled', $prefix, $this->get_id() ) ),
			esc_attr( sprintf( '%s[%s][enabled]', $prefix, $this->get_id() ) ),
			checked( $this->is_enabled(), true, false )
		);
		echo '<span class="sf-switch__slider"></span></label>';
	}

	/**
	 * Performs a query to count how many times given term ids appear in filtered products
	 *
	 * @param $terms
	 *
	 * @return array|false
	 */
	protected function get_product_counts_in_terms( $terms, $key = 'term' ) {
		global $wpdb;
		$product_count = false;

		if ( $this->get_data( 'count' ) ) {

			// Get JOIN and WHERE args from main product query to limit results to only filtered products
			$query_args = \Hybrid\app( 'filtered-query-args' );
			$join       = $query_args['join'];
			$where      = $query_args['where'];

			$term_ids = $this->parse_term_ids( $terms );

			$sql = "
                SELECT term_taxonomy.term_id AS term_id, COUNT( DISTINCT {$wpdb->posts}.ID) AS post_count
                FROM {$wpdb->posts}
                INNER JOIN {$wpdb->term_relationships} AS term_relationships ON {$wpdb->posts}.ID = term_relationships.object_id
                INNER JOIN {$wpdb->term_taxonomy} AS term_taxonomy ON term_relationships.term_taxonomy_id = term_taxonomy.term_taxonomy_id
                $join
                WHERE 1=1
                $where
                AND term_taxonomy.term_id IN (" . implode( ',', $term_ids ) . ")
                GROUP BY term_taxonomy.term_id
            ";

            $sql_hash = md5( $sql );
			$cached_queries = (array) get_transient( 'sf_products_in_' . urlencode( $key ) );

			// Query products and save query to daily cache
			if( ! isset( $cached_queries[ $sql_hash ] ) ) {
	            $results = $wpdb->get_results( $sql, ARRAY_A );
                $cached_queries[ $sql_hash ] = wp_list_pluck( $results, 'post_count', 'term_id' );

                set_transient( 'sf_products_in_' . urlencode( $key ), $cached_queries, DAY_IN_SECONDS );
            }

			$product_count = $cached_queries[ $sql_hash ];
		}

		return $product_count;
	}

	/**
	 * Extract only term ids from terms array
	 *
	 * @param $terms
	 *
	 * @return array
	 */
	private function parse_term_ids( $terms ) {
		if ( isset( end( $terms )['id'] ) ) {
			$term_ids = array_map( function ( $term ) {
				return isset( $term['id'] ) ? $term['id'] : false;
			}, $terms );

			$term_ids = array_filter( $term_ids, function ( $term ) {
				return is_numeric( $term );
			} );

			return $term_ids;
		} else {
			return $terms;
		}
	}

	/**
	 * Get settings from filter's group
	 *
	 * @return array
	 */
	protected function get_group_settings() {
		if ( ! $this->group ) {
			return [];
		}

		return $this->group->get_settings()->get_data();
	}
}