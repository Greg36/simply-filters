<?php

namespace SimplyFilters\Filters;

class FilterQuery {


	/**
	 * @var \WP_Query
	 */
	private $query;

	/**
	 * @var array
	 */
	private $taxonomies;

	/**
	 * @var array
	 */
	private $params;

	/**
	 * @var string
	 */
	private $cat_slug;

	/**
	 * @var string
	 */
	private $tag_slug;

	/**
	 * @var string
	 */
	private $tax_query;

	/**
	 * @var bool
	 */
	private $attribute_lookup_enabled;

	public function __construct( \WP_Query $query ) {

		global $wpdb;

		$this->query                    = $query;
		$this->taxonomies               = get_object_taxonomies( 'product' );
		$this->attribute_lookup_enabled = 'yes' === get_option( 'woocommerce_attribute_lookup_enabled' );
		$this->lookup_table_name = $wpdb->prefix . 'wc_product_attributes_lookup';

//		$this->tax_query  = \WC_Query::get_main_tax_query();
//		if( empty( $this->tax_query ) ) $this->tax_query = ['relation' => 'AND'];
//
//		$this->meta_query = \WC_Query::get_main_meta_query();
	}

	public function filter() {

		// Bail early if query has already been filtered
		if ( $this->query->get( 'sf-filters-applied', false ) ) {
			return;
		}

		// Get filtering parameters from the URL
		$this->params = $this->parse_url();

		// Apply tax query
		$this->query->set( 'tax_query', $this->get_tax_query() );

		add_filter( 'posts_clauses', [ $this, 'query_post_clauses' ], 10, 2 );

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

		return $data;
	}

	/**
	 * Verify param from GET request and assign its type
	 *
	 * @param $slug
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

			// Stock status
			case _x( 'stock-status', 'slug', \Hybrid\app( 'locale' ) ) :
				return [
					'key'  => 'stock_status',
					'type' => 'stock'
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
	 * @param $value string
	 * @param $type string
	 *
	 * @return array|false
	 */
	private function parse_value( $value, $type ) {

		// Handle price range, only here underscore is used as an operator
		// otherwise underscore is allowed on WP slugs
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

		// Match values and the delimiter
		preg_match_all( '/([\w\-_]+)|([ |])/', $value, $matches );

		// Get values array
		$values = array_filter( $matches[1] );
		if ( empty( $values ) ) {
			return false;
		}

		// Get the operator based on delimiter in the URL
		// | in the URL is IN, encoded it's a space
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


	public function query_post_clauses( $args, $wp_query ) {
		// @todo remove this filter in the_post to not duplicate this queries
		$args = $this->get_price_query( $args );
		$args = $this->get_attributes_query( $args, $wp_query );

		return $args;
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

		return $tax_query;
	}

	/**
	 * Get rating query based on product_visibility taxonomy
	 *
	 * @param $param
	 *
	 * @return array|false
	 */
	private function get_rating_query() {
		$params = $this->get_params_by_type( 'rating' );
		if ( ! $params ) {
			return false;
		}

		$visibility_terms = wc_get_product_visibility_term_ids();

		$terms = [];
		for ( $i = 1; $i <= 5; $i ++ ) {
			if ( in_array( $i, $params[0]['data'] ) && isset( $visibility_terms[ 'rated-' . $i ] ) ) {
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
	 * @param $args
	 * @param $wp_query
	 *
	 * @return mixed|void
	 */
	private function get_price_query( $args ) {

		global $wpdb;

		$params = $this->get_params_by_type( 'price' );
		if ( ! $params ) {
			return $args;
		}

		// Add meta lookup table to join clauses if it is not already present
		if ( strpos( $args['join'], 'wc_product_meta_lookup' ) === false ) {
			$args['join'] .= " LEFT JOIN {$wpdb->wc_product_meta_lookup} wc_product_meta_lookup ON $wpdb->posts.ID = wc_product_meta_lookup.product_id ";
		}

		// Add where clause for min and max price in lookup table
		$args['where'] .= $wpdb->prepare(
			' AND NOT (%f < wc_product_meta_lookup.min_price OR %f > wc_product_meta_lookup.max_price ) ',
			$params[0]['data']['min'],
			$params[0]['data']['max']
		);

		return $args;
	}

	private function get_attributes_query( $args ) {

		global $wpdb;

		$params = $this->get_params_by_type( 'attribute' );
		if ( empty( $params ) || ! $this->attribute_lookup_enabled ) {
			return $args;
		}

		$clause_root = " {$wpdb->posts}.ID IN ( SELECT product_or_parent_id FROM (";

		if ( 'yes' === get_option( 'woocommerce_hide_out_of_stock_items' ) ) {
			$in_stock_clause = ' AND in_stock = 1';
		} else {
			$in_stock_clause = '';
		}

		$filter_ids = [];
		$clauses    = [];

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

		if ( ! empty( $clauses ) ) {
			$args['where'] .= ' AND (' . join( ' temp ) AND ', $clauses ) . ' temp ))';
		} elseif ( ! empty( $params ) ) {
			$args['where'] .= ' AND 1=0';
		}

		return $args;

	}

	/**
	 * Return all params matching type
	 *
	 * @param $type
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