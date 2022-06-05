<?php

namespace SimplyFilters\Admin\Controls;

abstract class Control {

	/**
	 * @var string Setting's unique key.
	 */
	protected $key;

	/**
	 * @var string Setting's label.
	 */
	protected $label;

	/**
	 * @var Setting's value.
	 */
	protected $value;

	/**
	 * @var string Setting's name.
	 */
	protected $name;

	/**
	 * @var string Setting's description.
	 */
	protected $description;

	/**
	 * @var array Setting's array options.
	 */
	protected $options = [];

	protected abstract function render_setting_field();

	/**
	 * Initialize field with data // @param $args
	 * @todo better doc
	 *
	 */
	public function __construct( $args ) {
		$this->name        = $args['name'];
		$this->description = isset( $args['description'] ) ? $args['description'] : '';
		$this->options     = isset( $args['options'] ) ? $args['options'] : [];
		$this->value       = isset( $args['default'] ) ? $args['default'] : '';
	}

	/**
	 * Render settings field
	 *
	 * @param $key
	 */
	public function render( $data ) {
		$this->key   = $data['key'];
		$this->id    = $data['id'];
		$this->label = $data['label'];
		if ( ! $data['load_defaults'] ) { // @todo: load_defaults work for AJAX but does not for initializing new group or global settings
			$this->value = $data['value'];
		}

		$this->render_settings_row();
	}

	public function parse_data( $data ) {
		return $data;
	} // @todo: change to get_data / save_data   or something like get/save_setting ?

	/**
	 * Output admin setting's table row for the filter
	 */
	protected function render_settings_row() {
		?>
        <tr class="sf-option">
            <td>
                <label for="<?php esc_attr_e( $this->key ); ?>"><?php esc_html_e( $this->name ); ?></label>
                <p><?php echo wp_kses_post( $this->description ); ?></p>
            </td>
            <td>
				<?php $this->render_setting_field(); ?>
            </td>
        </tr>
		<?php
	}
}