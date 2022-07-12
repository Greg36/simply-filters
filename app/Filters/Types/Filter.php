<?php

namespace SimplyFilters\Filters\Types;

use SimplyFilters\Admin\Settings;
use SimplyFilters\Filters\FilterGroup;

/**
 * Main filter class, it handles configuration of settings,
 * retrieving of option's data and front-end rendering
 *
 * It does not handle actual filtering of product's data
 * all of filtering is based on GET data and handled
 * by FilterQuery class
 *
 * @since 1.0.0
 */
abstract class Filter {

	/**
	 * @var string Filter post object ID
	 */
	protected $id;

	/**
	 * @var array Unserialized filter post content data
	 */
	protected $data;

	/**
	 * @var string
	 */
	protected $type;

	/**
	 * @var string
	 */
	protected $name;

	/**
	 * @var string
	 */
	protected $description;

	/**
	 * @var bool Is the filter enabled
	 */
	protected $enabled;

	/**
	 * @var Settings
	 */
	protected $settings;

	/**
	 * @var FilterGroup Group filter is part of
	 */
	protected $group;

	/**
	 * @var string[] Supported settings
	 */
	protected $supports = [];

	/**
	 * @var array
	 */
	protected $sources = [];

	/**
	 * @var string
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
			$this->init_settings();
		}
	}

	/**
	 * Set group filter is part of
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
	 * Return filter data value
	 *
	 * @param string $key Data key
	 * @param string $default Optional default value
	 *
	 * @return mixed|string
	 */
	public function get_data( $key, $default = '' ) {
		return isset( $this->data[ $key ] ) ? $this->data[ $key ] : $default;
	}

	/**
	 * Initialize filter settings
	 */
	protected function init_settings() {
		$this->settings = new Settings( $this->get_id(), $this->data );
		$this->load_supported_settings();
	}

	/**
	 * Load supported settings
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
	 * Render settings
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
			'attributes'   => esc_html__( 'Attributes', $this->locale ),
			'product_cat'  => esc_html__( 'Product category', $this->locale ),
			'product_tag'  => esc_html__( 'Product tags', $this->locale )
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
	 * Order options according to settings value
	 *
	 * @param array $options Options list
	 * @param array $count Count of values in options
	 *
	 * @return array
	 */
	protected function order_options( $options, $count ) {

		$orderby = $this->data['order_by'];
		$order   = $this->data['order_type'];

		// Keep 'all setting' option at the top
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
	 * Return source key
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
	 * Get data required for render
	 *
	 * @return array|false
	 */
	protected function get_render_data() {
		$options = $this->get_current_source_options();

		if ( empty( $options ) ) {
			return false;
		}

		$count = $this->get_product_counts_in_terms( $options );

		return [
			'id'       => $this->get_id(),
			'key'      => $this->get_current_source_key(),
			'options'  => $this->order_options( $options, $count ),
			'values'   => $this->get_selected_values(),
			'settings' => [
				'group' => $this->get_group_settings(),
				'query' => $this->get_data( 'query', 'or' ),
				'count' => $count
			]
		];
	}

	/**
	 * Render preview for new filter screen
	 */
	public function render_new_filter_preview() {
		?>
        <div class="sf-preview">
            <div class="sf-preview__heading">
                <h4><?php echo wp_kses_post( $this->get_name() ); ?></h4>
                <p><?php echo wp_kses_post( $this->get_description() ); ?></p>
            </div>
            <div class="sf-preview__body">
				<?php $this->filter_preview(); ?>
            </div>
            <div class="sf-preview__footer">
                <a href="#" data-type="<?php echo esc_attr( $this->get_type() ); ?>" class="select-filter sf-button"><?php esc_html_e( 'Select filter', $this->locale ); ?></a>
            </div>
        </div>
		<?php
	}


	/**
	 * Return selected values based on parsed URL params
	 *
	 * @return array
	 */
	protected function get_selected_values() {

		// Parsed URL params
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

	/**
	 * Render all hidden meta fields
	 */
	public function render_meta_fields() {

		// Filter type field
		echo $this->meta_field( 'type', $this->get_type() );

		// Menu order field
		echo $this->meta_field( 'menu_order', $this->get_data( 'menu_order' ) );
	}

	/**
	 * Return hidden input field
	 *
	 * @param string $name Name of input field
	 * @param mixed $value Value of input field
	 *
	 * @return string
	 */
	private function meta_field( $name, $value = '' ) {

		$prefix = \Hybrid\app( 'prefix' );

		return sprintf( '<input type="hidden" id="%s" name="%s" %s>',
			esc_attr( sprintf( '%s-%s-%s', $prefix, $this->get_id(), $name ) ),
			esc_attr( sprintf( '%s[%s][%s]', $prefix, $this->get_id(), $name ) ),
			$value !== '' ? sprintf( ' value="%s" ', esc_attr( $value ) ) : ''
		);
	}

	/**
	 * Render input for enable switch
	 */
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
	 * Performs a query to count how many times given term IDs appear in filtered products
	 *
	 * @param array $terms An array of term IDs
	 *
	 * @return array|false
	 */
	protected function get_product_counts_in_terms( $terms ) {
		global $wpdb;
		$product_count = false;

		// Only if count setting is enabled
		if ( $this->get_data( 'count' ) ) {

			// Get JOIN and WHERE args from main product query to limit results to only filtered products
			$query_args = \Hybrid\app( 'filtered-query-args' );
			$join       = $query_args['join'];
			$where      = $query_args['where'];
            $taxonomy   = $this->get_current_source_taxonomy();

			$term_ids = $this->parse_term_ids( $terms );
            if( empty( $term_ids ) ) return [];

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

			// Use query hash as cache identifier
			$sql_hash       = md5( $sql );
			$cached_queries = (array) get_transient( 'sf_products_in_' . $taxonomy );

			// Query products and save query to daily cache
			if ( ! isset( $cached_queries[ $sql_hash ] ) ) {
				$results                     = $wpdb->get_results( $sql, ARRAY_A );
				$cached_queries[ $sql_hash ] = $this->count_product_ids( $results, $taxonomy );

				set_transient( 'sf_products_in_' . $taxonomy, $cached_queries, DAY_IN_SECONDS );
			}

			$product_count = $cached_queries[ $sql_hash ];
		}

		/**
		 * Products in terms counted
		 *
		 * @param array $product_count Array of term IDs and counted products from that term
         * @param array $terms         Array of all terms before counting
		 */
		$product_count = apply_filters( 'sf-product-count', $product_count, $terms );

		return $product_count;
	}

	/**
	 * Count products in each terms including aggregating products from term hierarchy
	 *
	 * @param array $results Result of DB query with product IDs for terms
	 * @param string $taxonomy Product taxonomy
	 *
	 * @return array
	 */
    private function count_product_ids( $results, $taxonomy ) {

	    $product_count = wp_list_pluck( $results, 'post_count', 'term_id' );

        if( $taxonomy === 'rating' ) return $product_count;

	    // Get term IDs hierarchy
	    $term_hierarchy = [];
        $term_ids = array_keys( $product_count );
	    foreach ( $term_ids as $term_id ) {
		    $term_hierarchy[ $term_id ] = get_term_children( $term_id, $taxonomy );

		    if ( isset( $term_hierarchy[ $term_id ] ) && count( $term_hierarchy[ $term_id ] ) ) {
			    $term_ids = array_merge( $term_ids, $term_hierarchy[ $term_id ] );
		    }
	    }

        // Add term children count to the parent
	    foreach ( $product_count as $term_id => $count ) {
            if( array_key_exists( $term_id, $term_hierarchy ) ) {
	            foreach ( $term_hierarchy[ $term_id ] as $child ) {
		            $product_count[ $term_id ] += $product_count[ $child ];
                }
            }
        }

        return $product_count;
    }

	/**
	 * Parse terms array to contain only term IDs
	 *
	 * @param array $terms An assoc array containing term IDs or array of IDs
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
	 * Get setting from filter's group
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