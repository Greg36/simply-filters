<?php

namespace SimplyFilters\Filters;

class DataParser {

	private $group_id;

	public function __construct( $group_id ) {
		$this->group_id = (int) $group_id;
	}

	public function save_filter( $id, $data ) {

		// Remove slashes
		$data = wp_unslash( $data );

		// Parse filter enabled checkbox
		if ( ! isset( $data['enabled'] ) ) {
			$data['enabled'] = false;
		} else if ( $data['enabled'] === 'on' ) {
			$data['enabled'] = true;
		}

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
		if ( false === get_post_status( $id ) ) {
			$post['ID'] = false;
			$save       = wp_insert_post( $post );
		} else {
			$save = wp_update_post( $post );
		}

		// Save color values to term meta
		if ( $save && $data['type'] === 'Color' ) {
			$this->save_color( $data );
		}
	}

	private function save_color( $data ) {
		$taxonomy = $data[ $data['sources'] ];
		if ( taxonomy_exists( $taxonomy ) ) {
			foreach ( $data['color'] as $id => $color ) {
				update_term_meta( intval( $id ), \Hybrid\app( 'term-color-key' ), sanitize_hex_color( $color ) );
			}
		}
	}

	public function remove_filter( $id ) {

		// Check if post is still available
		if ( false !== get_post_status( $id ) ) {
			wp_delete_post( $id, true );
		}
	}

	private function extract_key( array &$arr, $key ) {
		if ( array_key_exists( $key, $arr ) ) {
			$val = $arr[ $key ];
			unset( $arr[ $key ] );

			return $val;
		}

		return null;
	}

}