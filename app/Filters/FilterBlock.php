<?php

namespace SimplyFilters\Filters;

/**
 * Block to render filter group
 *
 * @since 1.0.0
 */
class FilterBlock {

	use \SimplyFilters\Assets;

	/**
	 * @var self Singleton instance of class
	 */
	private static $instance;

	/**
	 * @var string Name of the registered block
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

	/**
	 * Register assets used with the block
	 */
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

	/**
	 * Register block
	 */
	public function register_block() {
		register_block_type( $this->block_name, array(
			'editor_script'   => 'simply-filters_filter-block',
			'render_callback' => array( $this, 'render_block' ),
			'attributes'      => [
				'group_id'      => [
					'type'    => 'integer',
					'default' => 0
				],
				'isSelectGroup' => [
					'type'    => 'boolean',
					'default' => true,
				],
				'isPreview'     => [
					'type'    => 'boolean',
					'default' => false
				]
			]
		) );
	}

	/**
	 * Return  render of the block
	 *
	 * @param array $attributes Block parameters
	 *
	 * @return false|string
	 */
	public function render_block( $attributes ) {
		if ( ! isset( $attributes['group_id'] ) ) {
			return '';
		}

		// Set flag for FilterGroup render method to allow display of filters in admin block preview
		if ( defined( 'REST_REQUEST' ) && REST_REQUEST ) {
			\Hybrid\app()->instance( 'is-block-preview', true );
		}
		ob_start();

		$group = get_post( $attributes['group_id'] );

		// Render only if group post is found and is published
		if( $group instanceof \WP_Post && $group->post_status === 'publish' ) {
			$filter_group = new FilterGroup( $group );
			$filter_group->render();
		}

		return ob_get_clean();
	}

}