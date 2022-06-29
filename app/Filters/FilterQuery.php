<?php

namespace SimplyFilters\Filters;

class FilterQuery {


	/**
	 * @var \WP_Query Main query to filter
	 */
	private $query;

	/**
	 * @var array Parameters from parsed URL
	 */
	private $params;

	/**
	 * @var array WooCommerce product taxonomies
	 */
	private $taxonomies;

	/**
	 * @var string  WooCommerce product category slug
	 */
	private $cat_slug;

	/**
	 * @var string WooCommerce product tag slug
	 */
	private $tag_slug;

	/**
	 * @var bool
	 */
	private $attribute_lookup_enabled;

	/**
	 * @var string
	 */
	private $lookup_table_name;

	public function __construct( \WP_Query $query ) {
		global $wpdb;

		$this->query      = $query;
		$this->taxonomies = get_object_taxonomies( 'product' );

		$this->attribute_lookup_enabled = 'yes' === get_option( 'woocommerce_attribute_lookup_enabled' );
		$this->lookup_table_name        = $wpdb->prefix . 'wc_product_attributes_lookup';
	}

	/**
	 * Parse URL and apply it to the product query
	 */
	public function filter() {

		// Bail early if query has already been filtered
		if ( $this->query->get( 'sf-filters-applied', false ) ) {
			return;
		}

		// Get filtering parameters from the URL
		$params = $this->parse_url();

		/**
		 * Filters parameters parsed from URL
		 *
		 * @param array $params Array of params include key, type, data and operator
		 */
		$this->params = apply_filters( 'sf-url-parameters', $params );

		// Apply tax query
		$this->query->set( 'tax_query', $this->get_tax_query() );

		// Filter posts clauses
		add_filter( 'posts_clauses', [ $this, 'query_post_clauses' ], 10, 2 );

		// Save filtered post clauses
		add_filter( 'posts_clauses_request', [ $this, 'save_post_clauses' ], 10, 2 );

		// Mark query as filtered
		$this->query->set( 'sf-filters-applied', true );
	}

	/**
	 * Verify all URL parameters and process each value
	 *
	 * @return array
	 */
	private function parse_url() {
		$params = $_GET;
		$data   = [];

		// No params, no filtering
		if ( empty( $params ) ) {
			return $data;
		}

		foreach ( $params as $param => $value ) {
			$param = $this->verify_param_by_slug( wc_sanitize_taxonomy_name( $param ) );

			if ( $param ) {
				$value = $this->parse_value( sanitize_text_field( $value ), $param['type'] );

				if ( $value ) {
					$data[] = array_merge( $param, $value );
				}
			}
		}

		\Hybrid\app()->instance( 'filter-values', $data );

		return $data;
	}

	/**
	 * Verify param from GET request
	 *
	 * @param string $slug Parameter from request
	 *
	 * @return false|string[]
	 */
	private function verify_param_by_slug( $slug ) {

		switch ( $slug ) {

			// Attribute
			case in_array( 'pa_' . $slug, $this->taxonomies ):
				return [
					'key'  => 'pa_' . $slug,
					'type' => 'attribute'
				];

			// Product category
			case $this->get_cat_slug() :
				return [
					'key'  => 'product_cat',
					'type' => 'taxonomy'
				];

			// Product tag
			case $this->get_tag_slug() :
				return [
					'key'  => 'product_tag',
					'type' => 'taxonomy'
				];

			// Rating
			case _x( 'rating', 'slug', \Hybrid\app( 'locale' ) ) :
				return [
					'key'  => 'rating',
					'type' => 'rating'
				];

			// Price
			case _x( 'price', 'slug', \Hybrid\app( 'locale' ) ) :
				return [
					'key'  => '_price',
					'type' => 'price'
				];
		}

		return false;
	}

	/**
	 * Get the values and operator from URL param value
	 *
	 * @param string $value Request value from GET param
	 * @param string $type Verified parameter type
	 *
	 * @return array|false
	 */
	private function parse_value( $value, $type ) {

		// Handle price range
		if ( $type === 'price' ) {
			$data = explode( '_', $value );
			if ( count( $data ) === 2 && is_numeric( $data[0] ) && is_numeric( $data[1] ) ) {
				return [
					'data'     => [
						'min' => intval( min( $data ) ),
						'max' => intval( max( $data ) )
					],
					'operator' => 'BETWEEN'
				];
			}
		}

		preg_match_all( '/([\w\-_]+)|([ |])/', $value, $matches );

		// Get values array
		$values = array_filter( $matches[1] );
		if ( empty( $values ) ) {
			return false;
		}

		// Get the query operator based on delimiter in the URL
		// | in the URL is IN, encoded as a space
		// + in the URL is AND operator
		if ( isset( $matches[2] ) && ! empty( array_filter( $matches[2] ) ) ) {
			switch ( current( array_filter( $matches[2] ) ) ) {
				case '|' :
					$operator = 'IN';
					break;

				case ' ' :
					$operator = 'AND';
					break;

				default:
					return false;
			}
		} else {
			$operator = 'IN';
		}

		return [
			'data'     => $values,
			'operator' => $operator
		];
	}

	/**
	 * Apply price and attribute params to query on later hook
	 * where there is direct access to post clauses
	 *
	 * @param array $clauses Associative array of the clauses for the query
	 * @param \WP_Query $wp_query The WP_Query instance reference
	 *
	 * @return array
	 */
	public function query_post_clauses( $clauses, $wp_query ) {
		if ( $wp_query->get( 'sf-filters-clauses-applied' ) ) {
			return $clauses;
		}

		$clauses = $this->get_price_query( $clauses );
		$clauses = $this->get_attributes_query( $clauses );

		return $clauses;
	}

	/**
	 * Generate tax query from URL params
	 *
	 * @return array
	 */
	private function get_tax_query() {

		$tax_query = $this->query->get( 'tax_query' );
		if ( ! is_array( $tax_query ) ) {
			$tax_query = [
				'relation' => 'AND',
			];
		}

		// Category and tag filter
		foreach ( $this->params as $param ) {
			if ( $param['type'] === 'taxonomy' ) {
				$tax_query[] = [
					'taxonomy' => $param['key'],
					'field'    => 'slug',
					'terms'    => $param['data'],
					'operator' => $param['operator']
				];
			}
		}

		// Attribute filter
		if ( ! $this->attribute_lookup_enabled ) {

			// If attributes lookup table is not enabled fallback to normal term query
			foreach ( $this->params as $param ) {
				if ( $param['type'] === 'attribute' ) {
					$tax_query[] = [
						'taxonomy' => $param['key'],
						'field'    => 'slug',
						'terms'    => $param['data'],
						'operator' => $param['operator'],
					];
				}
			}
		}

		// Rating filter
		$rating_query = $this->get_rating_query();
		if ( $rating_query ) {
			$tax_query[] = $rating_query;
		}

		/**
		 * Taxonomy query before being applied
		 *
		 * @param array $tax_query Taxonomy query clauses
		 */
		$tax_query = apply_filters( 'sf-taxonomy-query', $tax_query );

		return $tax_query;
	}

	/**
	 * Get rating query based on product_visibility taxonomy
	 *
	 * @return array|false
	 */
	private function get_rating_query() {
		$param = current( $this->get_params_by_type( 'rating' ) );
		if ( ! $param ) {
			return false;
		}

		$visibility_terms = wc_get_product_visibility_term_ids();

		$terms = [];
		for ( $i = 1; $i <= 5; $i ++ ) {
			if ( in_array( $i, $param['data'] ) && isset( $visibility_terms[ 'rated-' . $i ] ) ) {
				$terms[] = $visibility_terms[ 'rated-' . $i ];
			}
		}

		if ( ! empty( $terms ) ) {
			return [
				'taxonomy'      => 'product_visibility',
				'field'         => 'term_taxonomy_id',
				'terms'         => $terms,
				'operator'      => 'IN',
				'rating_filter' => true,
			];
		}

		return false;
	}

	/**
	 * Add price query via meta lookup table
	 *
	 * @param array $clauses Associative array of the clauses for the query
	 *
	 * @return mixed|void
	 */
	private function get_price_query( $clauses ) {
		global $wpdb;

		$param = current( $this->get_params_by_type( 'price' ) );
		if ( ! $param ) {
			return $clauses;
		}
		$min = $param['data']['min'];
		$max = $param['data']['max'];

		// Add meta lookup table to join clauses if it is not already present
		if ( strpos( $clauses['join'], 'wc_product_meta_lookup' ) === false ) {
			$clauses['join'] .= " LEFT JOIN {$wpdb->wc_product_meta_lookup} wc_product_meta_lookup ON $wpdb->posts.ID = wc_product_meta_lookup.product_id ";
		}

		/**
		 * Adjust query if taxes are enabled, price includes tax on front-end and product prices are entered without tax
		 */
		if ( wc_tax_enabled() && 'incl' === get_option( 'woocommerce_tax_display_shop' ) && ! wc_prices_include_tax() ) {
			$tax_class = apply_filters( 'woocommerce_price_filter_widget_tax_class', '' );
			$tax_rates = \WC_Tax::get_rates( $tax_class );

			if ( $tax_rates ) {
				$min -= \WC_Tax::get_tax_total( \WC_Tax::calc_inclusive_tax( $min, $tax_rates ) );
				$max -= \WC_Tax::get_tax_total( \WC_Tax::calc_inclusive_tax( $max, $tax_rates ) );
			}
		}

		// Add where clause for min and max price in lookup table
		$price_query = $wpdb->prepare(
			' AND NOT (%f < wc_product_meta_lookup.min_price OR %f > wc_product_meta_lookup.max_price ) ',
			$max,
			$min
		);

		/**
		 * Price query before being applied
		 *
		 * @param string $params Escaped price query string
		 */
		$price_query = apply_filters( 'sf-price-query', $price_query );

		$clauses['where'] .= $price_query;

		// Save price query
		\Hybrid\app()->instance( 'filtered-query-price', $price_query );

		return $clauses;
	}

	/**
	 * When the attribute lookup table is enabled use to it query
	 * attributes in more performant way
	 *
	 * @param array $args Associative array of the clauses for the query
	 *
	 * @return array
	 */
	private function get_attributes_query( $args ) {

		global $wpdb;

		// If there are no params or attribute lookup table is not enabled bail early
		$params = $this->get_params_by_type( 'attribute' );
		if ( empty( $params ) || ! $this->attribute_lookup_enabled ) {
			return $args;
		}

		if ( 'yes' === get_option( 'woocommerce_hide_out_of_stock_items' ) ) {
			$in_stock_clause = ' AND in_stock = 1';
		} else {
			$in_stock_clause = '';
		}

		$clause_root = " {$wpdb->posts}.ID IN ( SELECT product_or_parent_id FROM (";
		$filter_ids  = [];
		$clauses     = [];

		foreach ( $params as $param ) {
			$term_ids = get_terms( [
				'taxonomy' => $param['key'],
				'fields'   => 'ids',
				'slug'     => $param['data']
			] );

			if ( ! empty( $term_ids ) ) {
				if ( $param['operator'] === 'AND' && count( $term_ids ) > 1 ) {
					$filter_ids = array_merge( $filter_ids, $term_ids );
				} else {
					$term_list = '(' . join( ',', $term_ids ) . ')';

					$clauses[] = "
							{$clause_root}
							SELECT product_or_parent_id
							FROM {$this->lookup_table_name} lt
							WHERE term_id in {$term_list}
							{$in_stock_clause}
						)";
				}
			}
		}

		// Compound query for filtering by more than one
		// attribute with an AND parameter
		if ( ! empty( $filter_ids ) ) {
			$count     = count( $filter_ids );
			$term_list = '(' . join( ',', $filter_ids ) . ')';
			$clauses[] = "
				{$clause_root}
				SELECT product_or_parent_id
				FROM {$this->lookup_table_name} lt
				WHERE is_variation_attribute=0
				{$in_stock_clause}
				AND term_id in {$term_list}
				GROUP BY product_id
				HAVING COUNT(product_id)={$count}
				UNION
				SELECT product_or_parent_id
				FROM {$this->lookup_table_name} lt
				WHERE is_variation_attribute=1
				{$in_stock_clause}
				AND term_id in {$term_list}
			)";
		}

		/**
		 * Attributes clauses before being applied
		 *
		 * @param array $clauses Attribute query clauses
		 */
		$clauses = apply_filters( 'sf-attributes-query', $clauses );

		// Apply clauses to the query
		if ( ! empty( $clauses ) ) {
			$args['where'] .= ' AND (' . join( ' temp ) AND ', $clauses ) . ' temp ))';
		} else if ( ! empty( $params ) ) {
			$args['where'] .= ' AND 1=0';
		}

		return $args;
	}

	/**
	 * Save final query clauses to be used later with additional
	 * filter specific queries
	 *
	 * @param array $clauses Associative array of the clauses for the query
	 * @param \WP_Query $wp_query The WP_Query instance reference
	 *
	 * @return array
	 */
	public function save_post_clauses( $clauses, $wp_query ) {
		if ( $wp_query->get( 'sf-filters-clauses-applied' ) ) {
			return $clauses;
		}

		\Hybrid\app()->instance( 'filtered-query-args', $clauses );

		$wp_query->set( 'sf-filters-clauses-applied', true );

		return $clauses;
	}

	/**
	 * Return all params matching type
	 *
	 * @param string $type Parameter type
	 *
	 * @return array
	 */
	private function get_params_by_type( $type ) {
		return array_filter( $this->params, function ( $param ) use ( $type ) {
			return $param['type'] === $type;
		} );
	}

	/**
	 * Return product category slug used in GET request
	 *
	 * @return string
	 */
	private function get_cat_slug() {
		if ( $this->cat_slug ) {
			return $this->cat_slug;
		}

		return $this->cat_slug = get_option( 'woocommerce_product_category_slug' ) ? get_option( 'woocommerce_product_category_slug' ) : _x( 'product-category', 'slug', \Hybrid\app( 'locale' ) );
	}

	/**
	 * Return product tag slug used in GET request
	 *
	 * @return string
	 */
	private function get_tag_slug() {
		if ( $this->tag_slug ) {
			return $this->tag_slug;
		}

		return $this->tag_slug = get_option( 'woocommerce_product_tag_slug' ) ? get_option( 'woocommerce_product_tag_slug' ) : _x( 'product-tag', 'slug', \Hybrid\app( 'locale' ) );
	}

}