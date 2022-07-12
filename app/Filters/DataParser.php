<?php

namespace SimplyFilters\Filters;

/**
 * Parse POST content to save filter data
 *
 * @since 1.0.0
 */
class DataParser {

	/**
	 * @var int Filter group post ID
	 */
	private $group_id;

	public function __construct( $group_id ) {
		$this->group_id = (int) $group_id;
	}

	/**
	 * Clean and save filter data as post content
	 *
	 * @param int $id Filter post ID
	 * @param array $data Unescaped filter data from POST request
	 */
	public function save_filter( $id, $data ) {

		// Parse filter enabled checkbox
		if ( ! isset( $data['enabled'] ) ) {
			$data['enabled'] = false;
		} else if ( $data['enabled'] === 'on' ) {
			$data['enabled'] = true;
		}

		/**
		 * Filter settings before save
		 *
		 * @param array $data Filter settings
		 * @param int $id Filter's post ID
		 */
		$data = apply_filters( 'sf-filter-data-before-save', $data, $id );

		// Prepare post data to be saved
		$post = [
			'ID'           => $id,
			'post_type'    => \Hybrid\app( 'item_post_type' ),
			'post_title'   => $this->extract_key( $data, 'label' ),
			'post_name'    => \Hybrid\app( 'prefix' ) . '-' . $id,
			'post_status'  => 'publish',
			'post_parent'  => $this->group_id,
			'menu_order'   => $this->extract_key( $data, 'menu_order' ),
			'post_content' => maybe_serialize( $data )
		];

		// Slash data
		$post = wp_slash( $post );

		// Create new post or update
		if ( is_numeric( $id ) && true === get_post_status( (int) $id ) ) {
			$save = wp_update_post( $post );
		} else {
			$post['ID'] = false;
			$save       = wp_insert_post( $post );
		}

		// Save color values to term meta
		if ( $save && $data['type'] === 'Color' ) {
			$this->save_color( $data );
		}
	}

	/**
	 * Save color values directly to term meta
	 *
	 * @param array $data Filter POST data
	 */
	private function save_color( $data ) {
		$taxonomy = $data[ $data['sources'] ];
		if ( taxonomy_exists( $taxonomy ) ) {
			foreach ( $data['color'] as $id => $color ) {
				update_term_meta( intval( $id ), \Hybrid\app( 'term-color-key' ), sanitize_hex_color( $color ) );
			}
		}
	}

	/**
	 * Remove filter post by ID
	 *
	 * @param int $id Filter post ID
	 */
	public function remove_filter( $id ) {

		// Check if post is still available
		if ( is_numeric( $id ) && false !== get_post_status( (int) $id ) ) {
			wp_delete_post( $id, true );
		}
	}

	/**
	 * Get value from post data by key
	 *
	 * @param array $arr Post data
	 * @param string $key Key value
	 *
	 * @return mixed|null
	 */
	private function extract_key( array &$arr, $key ) {
		if ( array_key_exists( $key, $arr ) ) {
			$val = $arr[ $key ];
			unset( $arr[ $key ] );

			return $val;
		}

		return null;
	}

}