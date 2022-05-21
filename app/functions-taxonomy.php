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
function get_product_categories() {
	$categories = [
		'all' => __( 'All categories', \Hybrid\app( 'locale' ) ),
	];

	foreach (
		get_terms(
			array(
				'taxonomy'   => 'product_cat',
				'hide_empty' => false,
			)
		) as $term
	) {
		$categories[ $term->term_id ] = $term->name;
	}

	return $categories;
}

/**
 * Get array of terms
 *
 * @return array
 */
function get_terms_list( $taxonomy, $parent = 0 ) {
	foreach (
		get_terms(
			array(
				'taxonomy'     => $taxonomy,
				'hide_empty'   => false,
				'parent'       => $parent,
				'hierarchical' => false
			)
		) as $term
	) {
		$terms[] = [
			'name' => $term->name,
			'slug' => $term->slug,
			'id' => $term->term_id
		];
	}

	return $terms;
}