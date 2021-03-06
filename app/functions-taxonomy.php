<?php

namespace SimplyFilters;

/**
 * Get array of all WooCommerce attributes in name => label pairs
 *
 * @return array
 */
function get_attributes() {
	$attributes = [];

	foreach ( wc_get_attribute_taxonomies() as $attribute ) {
		$attributes[ 'pa_' . $attribute->attribute_name ] = $attribute->attribute_label;
	}

	return $attributes;
}

/**
 * Get array of all WooCommerce product categories in term ID => name pairs
 *
 * @return array
 */
function get_product_categories( $args = [] ) {
	$categories = [
		'all' => esc_html__( 'All categories', \Hybrid\app( 'locale' ) ),
	];

	foreach (
		get_terms(
			wp_parse_args( $args, [
				'taxonomy'   => 'product_cat',
				'hide_empty' => false,
			] )
		) as $term
	) {
		$categories[ $term->term_id ] = $term->name;
	}

	return $categories;
}

/**
 * Get array of terms
 *
 * @param array $args Additional query arguments
 *
 * @return array
 */
function get_terms_list( $args = [] ) {
	foreach (
		get_terms(
			wp_parse_args( $args, [
				'hide_empty'   => false,
				'hierarchical' => false,
			] )
		) as $term
	) {
		$terms[] = [
			'name' => $term->name,
			'slug' => $term->slug,
			'id'   => $term->term_id
		];
	}

	return $terms;
}