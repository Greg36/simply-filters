<?php

namespace SimplyFilters\Filters;

class FilterBlock {

	use \SimplyFilters\Assets;

	private static $instance;

	/**
	 * Name of the registered block
	 *
	 * @var string
	 */
	private $block_name;

	/**
	 * Get the singleton instance via lazy initialization
	 *
	 * @return FilterBlock
	 */
	public static function getInstance() {
		return ! is_null( self::$instance ) ? self::$instance : self::$instance = new self();
	}

	public function __construct() {
		$this->block_name = 'simply-filters/filter-group';

		$this->register_assets();
		$this->register_block();
	}

	public function register_assets() {
		wp_register_script( 'simply-filters_filter-block', $this->getAssetPath( 'js/blocks/filter-block.js' ), [ 'wp-blocks' ], null, false );

		$locale = \Hybrid\app( 'locale' );

		wp_localize_script( 'simply-filters_filter-block', 'sf_filter_block', [
			'locale' => [
				'block_title'   => __( 'SF Filter Group xd', $locale ),
				'block_desc'    => __( 'SF DESC', $locale )
			]
		] );
	}

	public function register_block() {
		register_block_type( $this->block_name, array(
			'editor_script' => 'simply-filters_filter-block'
		) );
	}

}