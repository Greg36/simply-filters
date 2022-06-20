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
		wp_register_script( 'simply-filters_filter-block', $this->getAssetPath( 'js/blocks/filter-block.js' ), [
			'wp-block-editor',
			'wp-components',
			'wp-element',
			'wp-blocks',
			'wp-block-editor',
			'wp-data',
			'wp-compose',
			'wp-i18n',
			'wp-server-side-render'
		], null, false );
	}

	public function register_block() {
		register_block_type( $this->block_name, array(
			'editor_script' => 'simply-filters_filter-block',
			'render_callback' => array( $this, 'render_block' ),
			'attributes'      => [
				'group_id' => [
					'type'    => 'integer',
					'default' => 0
				],
				'isSelectGroup' => [
					'type' => 'boolean',
					'default' => true,
				],
				'isPreview' => [
					'type' => 'boolean',
					'default' => false
				]
			]
		) );
	}

	public function render_block( $attributes ) {
		if( ! isset( $attributes['group_id'] ) ) return '';

		\Hybrid\app()->instance( 'is-block-preview', true );

		ob_start();

		$group = new FilterGroup( $attributes['group_id'] );
		$group->render();

		return ob_get_clean();
	}

}