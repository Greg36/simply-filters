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
	 * @var string
	 */
	private $meta_query;


	public function __construct( \WP_Query $query ) {
		$this->query      = $query;
		$this->taxonomies = get_object_taxonomies( 'product' );
		$this->tax_query  = \WC_Query::get_main_tax_query();
		$this->meta_query = \WC_Query::get_main_meta_query();
	}

	public function filter() {

		// Bail early if query has already been filtered
		if ( $this->query->get( 'sf-filters-applied', false ) ) {
			return;
		}

		// Get filtering parameters from the URL
		$params = $this->parse_url();
		if ( ! empty( $params ) ) {
			foreach ( $params as $param ) {
				$this->generate_query( $param );
			}
		}

		$a = 'xd';
		$this->query->set( 'tax_query', $this->tax_query);
		$this->query->set( 'meta_query', $this->meta_query);
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
			$param = $this->verify_param_by_slug( sanitize_text_field( $param ) );

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
						intval( min( $data ) ),
						intval( max( $data ) )
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

	/**
	 * Generate query for given args
	 *
	 * @param $args
	 */
	private function generate_query( $args ) {

		if ( $args['type'] === 'attribute' || $args['type'] === 'taxonomy' ) {
			$this->tax_query = array_merge( $this->tax_query, [ $this->generate_tax_query( $args ) ] );
		}

		if ( $args['type'] === 'rating' ) {
			$this->meta_query = array_merge( $this->meta_query, [ $this->generate_rating_query( $args ) ] );
		}

		if ( $args['type'] === 'stock' ) {
			$this->meta_query = array_merge( $this->meta_query, [ $this->generate_stock_query( $args ) ] );
		}

		if ( $args['type'] === 'price' ) {
			$this->meta_query = array_merge( $this->meta_query, [ $this->generate_price_query( $args ) ] );
		}
	}

	/**
	 * Generate tax query with term ids
	 *
	 * @param $args
	 *
	 * @return array
	 */
	private function generate_tax_query( $args ) {

		$terms = get_terms( [
			'fields'     => 'ids',
			'hide_empty' => false,
			'taxonomy'   => $args['key'],
			'slug'       => $args['data']
		] );

		if ( empty( $terms ) ) {
			return [];
		}

		return [
			'taxonomy' => $args['key'],
			'field'    => 'term_id',
			'terms'    => $terms,
			'operator' => $args['operator']
		];
	}

	private function generate_rating_query( $args ) {
		return [
			'key'     => 'rating',
			'value'   => $args['data'][0],
			'compare' => '>'
		];
	}

	private function generate_stock_query( $args ) {
		return [
			'key'   => '_stock_status',
			'value' => $args['data'][0]
		];
	}

	private function generate_price_query( $args ) {
		// @todo: instead of going with this method I could filter WC_Query to use its methods and have it being queired by lookup table not standard one
		return [
			'key'     => '_price',
			'value'   => [ $args['data'][0], $args['data'][1] ],
			'compare' => $args['operator'],
			'type'    => 'NUMERIC'
		];
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