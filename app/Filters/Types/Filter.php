<?php

namespace SimplyFilters\Filters\Types;

use SimplyFilters\Admin\Settings;

abstract class Filter {

	/**
	 * @var string Filter's post ID
	 */
	protected $id;

	/**
	 * @var array Filter's data from unserialized post content
	 */
	protected $data;

	/**
	 * Type of the filter
	 *
	 * @var string
	 */
	protected $type;

	/**
	 * Name of the filter
	 *
	 * @var string
	 */
	protected $name;

	/**
	 * Description of the filter
	 *
	 * @var string
	 */
	protected $description;

	/**
	 * Is the filter enabled
	 *
	 * @var bool
	 */
	protected $enabled;

	/**
	 * @var Settings Settings handler
	 */
	protected $settings;

	/**
	 * @var array Supported settings
	 */
	protected $supports = [];

	/**
	 * @var array Sources for current filter
	 */
	protected $sources = [];

	/**
	 * @var string Text domain locale
	 */
	protected $locale;

    abstract protected function filter_preview();

	/**
	 * Initialize filter with the config data
	 *
	 * @param $data array
	 */
	public function initialize( $data ) {
		$this->data    = $data;
		$this->id      = $data['id'];
		$this->enabled = isset( $data['enabled'] ) ? (bool) $data['enabled'] : true;
		$this->locale  = \Hybrid\app( 'locale' );

		$this->set_sources();
		$this->load_settings();
	}

	/**
	 * Get ID of the field
	 *
	 * @return string
	 */
	public function get_id() {
		return $this->data['id'];
	}


	/**
	 * Get filter label
	 *
	 * @return string
	 */
	public function get_label() {
		return $this->data['label'];
	}

	/**
	 * Get filter type
	 *
	 * @return string
	 */
	public function get_type() {
		return $this->type;
	}

	/**
	 * Get filter description
	 *
	 * @return string
	 */
	public function get_description() {
		return $this->description;
	}

	/**
	 * Get filter name
	 *
	 * @return string
	 */
	public function get_name() {
		return $this->name;
	}

	/**
	 * Check if the filter is set to enabled
	 *
	 * @return bool
	 */
	public function is_enabled() {
		return $this->enabled;
	}

	/**
	 * Return filter's data value
	 *
	 * @param $key
	 *
	 * @return mixed|string
	 */
	public function get_data( $key ) {
		return isset( $this->data[ $key ] ) ? $this->data[ $key ] : '';
	}

	/**
	 * Load filter's settings
	 *
	 * @return void
	 */
	protected function load_settings() {
		$this->settings = new Settings( $this->get_id(), $this->data );
		$this->load_supported_settings();
	}

	/**
	 * Load filter's supported settings
	 */
	protected function load_supported_settings() {

		// Filter label
		if ( in_array( 'label', $this->supports, true ) ) {
			$this->settings->add( 'label', 'text', [
				'name'        => __( 'Filter label', $this->locale ),
				'description' => __( 'Name of the filter that will be displayed above it', $this->locale ),
				'required'    => true
			] );
		}

		// URL label
		if ( in_array( 'url-label', $this->supports, true ) ) {
			$this->settings->add( 'url-label', 'text', [
				'name'        => __( 'URL label', $this->locale ),
				'description' => __( 'This label will be used in URL when filter is applied. <br>Use only lowercase letters, numbers and hyphens.', $this->locale ),
				'unique'      => true
			] );
		}

		// Query type
		if ( in_array( 'query', $this->supports, true ) ) {
			$this->settings->add( 'query', 'radio', [
				'name'        => __( 'Search relation', $this->locale ),
				'description' => __( 'How to display results when selecting more than 1 option', $this->locale ),
				'options'     => [
					'and' => __( 'AND - Product needs to match all selected options to be shown', $this->locale ),
					'or'  => __( 'OR - Product needs to match any of selected options to be shown ', $this->locale )
				]
			] );
		}

		// Source
		if ( in_array( 'sources', $this->supports, true ) ) {

			$this->settings->add( 'sources', 'select', [
				'name'        => __( 'Sources', $this->locale ),
				'description' => __( 'Categories, tags and attributes created for products. If you want to filter by i.e. clothing size you need to create an attribute first - learn more', $this->locale ), // @todo include the link
				'options'     => $this->sources
			] );

			// Load controls for each source field
			$this->add_source_settings();
		}
	}

	/**
	 * Render filter's settings
	 *
	 * @return void
	 */
	public function render_setting_fields() {
		$this->settings->render();
	}

	/**
	 * Set filter sources options
	 */
	protected function set_sources() {
		$this->sources = $this->get_default_sources();
	}

	/**
	 * Get all default sources options
	 *
	 * @return array
	 */
	public function get_default_sources() {
		// @todo add custom_taxonomy option?
		return [
			'attributes'   => __( 'Attributes', $this->locale ),
			'product_cat'  => __( 'Product category', $this->locale ),
			'product_tag'  => __( 'Product tags', $this->locale ),
			'stock_status' => __( 'Stock status', $this->locale )
		];
	}

	/**
	 * Add settings for supported sources
	 */
	protected function add_source_settings() {

		// Attributes
		if ( array_key_exists( 'attributes', $this->sources ) ) {
			$this->settings->add( 'attributes', 'select', [
				'name'        => __( 'Attribute', $this->locale ),
				'description' => sprintf(
					__( 'Select one of the attributes <br> You can edit and add new in <a href="%s">attributes panel</a>', $this->locale ),
					admin_url( 'edit.php?post_type=product&page=product_attributes' )
				),
				'options'     => \SimplyFilters\get_attributes()
			] );
		}

		// Categories
		if ( array_key_exists( 'product_cat', $this->sources ) ) {
			$this->settings->add( 'product_cat', 'select', [
				'name'        => __( 'Product category', $this->locale ),
				'description' => sprintf(
					__( 'Select all categories or one to show it\'s children <br> You can edit and add new in <a href="%s">categories panel</a>', $this->locale ),
					admin_url( 'edit-tags.php?taxonomy=product_cat&post_type=product' )
				),
				'options'     => \SimplyFilters\get_product_categories()
			] );
		}
	}

	/**
	 * Get values from the currently selected source option if there is one
	 *
	 * @return array
	 */
	protected function get_current_source_options() {

		// Bail early if no source is set
		if ( ! isset( $this->data['sources'] ) || ! $this->data['sources'] ) {
			return [];
		}

		switch ( $this->data['sources'] ) {
			case 'attributes' :

				// Set default attribute if none is selected
				if ( ! $this->data['attributes'] ) {
					$attributes               = wc_get_attribute_taxonomies();
					$this->data['attributes'] = 'pa_' . array_shift( $attributes )->attribute_name;
				}

				return \SimplyFilters\get_terms_list( $this->data['attributes'] );

			case 'product_cat' :

				if ( $this->data['product_cat'] === 'all' || ! $this->data['product_cat'] ) {
					return \SimplyFilters\get_product_categories();
				}

				return \SimplyFilters\get_terms_list( 'product_cat', $this->data['product_cat'] );

			case 'product_tag' :

				return \SimplyFilters\get_terms_list( 'product_tag' );

			default :

				return [];
		}

	}

	public function render_new_filter_preview() {
		?>
        <div class="sf-preview">
            <div class="sf-preview__heading">
                <h4><?php esc_html_e( $this->get_name() ); ?></h4>
                <p><?php esc_html_e( $this->get_description() ); ?></p>
            </div>
            <div class="sf-preview__body">
				<?php $this->filter_preview(); ?>
            </div>
            <div class="sf-preview__footer">
                <a href="#" data-type="<?php esc_attr_e( $this->get_type() ); ?>" class="select-filter sf-button"><?php _e( 'Select filter', $this->locale ); ?></a>
            </div>
        </div>
		<?php
	}

	public function render_meta_fields() {

		// Filter type field
		echo $this->meta_field( 'type', $this->get_type() );

		// Menu order field
		echo $this->meta_field( 'menu_order', $this->get_data( 'menu_order' ) );
	}

	private function meta_field( $label, $value = '' ) {

		$prefix = \Hybrid\app( 'prefix' );

		return sprintf( '<input type="hidden" id="%s" name="%s" %s>',
			esc_attr( sprintf( '%s-%s-%s', $prefix, $this->get_id(), $label ) ),
			esc_attr( sprintf( '%s[%s][%s]', $prefix, $this->get_id(), $label ) ),
			$value !== '' ? sprintf( ' value="%s" ', esc_attr( $value ) ) : ''
		);
	}

	public function enabled_switch() {

		$prefix = \Hybrid\app( 'prefix' );

		echo '<label class="sf-switch">';
		printf( '<input type="checkbox" id="%s" name="%s" %s>',
			esc_attr( sprintf( '%s-%s-enabled', $prefix, $this->get_id() ) ),
			esc_attr( sprintf( '%s[%s][enabled]', $prefix, $this->get_id() ) ),
			checked( $this->is_enabled(), true, false )
		);
		echo '<span class="sf-switch__slider"></span></label>';
	}

}