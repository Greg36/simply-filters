<?php

namespace SimplyFilters\Admin\Controls;

/**
 * Control used to display front-end form for admin settings
 *
 * @since   1.0.0
 */
abstract class Control {

	/**
	 * @var string Unique key
	 */
	protected $key;

	/**
	 * @var string
	 */
	protected $label;

	/**
	 * @var mixed
	 */
	protected $value;

	/**
	 * @var string
	 */
	protected $name;

	/**
	 * @var string
	 */
	protected $description;

	/**
	 * @var array
	 */
	protected $options = [];

	protected abstract function render_setting_field();

	public function __construct( $args ) {
		$this->name        = $args['name'];
		$this->description = isset( $args['description'] ) ? $args['description'] : '';
		$this->options     = isset( $args['options'] ) ? $args['options'] : [];
		$this->value       = isset( $args['default'] ) ? $args['default'] : '';
	}

	/**
	 * Render setting's field
	 *
	 * @param array $data
	 */
	public function render( $data ) {
		$this->key   = $data['key'];
		$this->id    = $data['id'];
		$this->label = $data['label'];
		if ( ! $data['load_defaults'] ) {
			$this->value = $data['value'];
		}

		$this->render_settings_row();
	}

	/**
	 * Parse saved setting's data
	 *
	 * @param $data
	 */
	public function parse_data( $data ) {
		return $data;
	}

	/**
	 * Output admin setting's table row
	 */
	protected function render_settings_row() {
		?>
        <tr class="sf-option">
            <td>
                <label for="<?php echo esc_attr( $this->key ); ?>"><?php echo wp_kses_post( $this->name ); ?></label>
                <p><?php echo wp_kses_post( $this->description ); ?></p>
            </td>
            <td>
				<?php $this->render_setting_field(); ?>
            </td>
        </tr>
		<?php
	}
}