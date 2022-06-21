<?php

namespace SimplyFilters\Admin\Controls;

/**
 * Checkbox setting's control
 *
 * @since   1.0.0
 */
class CheckboxControl extends Control {

	public function __construct( $params ) {
		parent::__construct( $params );
	}

	/**
	 * Render HTMl inputs for all options
	 */
	protected function render_setting_field() {

		echo '<ul class="sf-input-list">';

		if ( ! empty( $this->options ) ) {
			foreach ( $this->options as $value => $label ) {

				printf( '<li><input type="checkbox" id="%1$s" name="%2$s" value="%3$s" %4$s> <label for="%1$s">%5$s</label></li>',
					esc_attr( $this->id . '_' . $value ),
					esc_attr( $this->key . '[' . $value . ']' ),
					esc_attr( $value ),
					$this->is_option_checked( $value ) ? 'checked' : '',
					wp_kses_post( $label )
				);
			}
		}

		echo '</ul>';
	}

	/**
	 * Check if given option is present in setting data
	 *
	 * @param mixed $value
	 *
	 * @return bool
	 */
	private function is_option_checked( $value ) {
		if ( is_array( $this->value ) ) {
			return in_array( $value, $this->value );
		}

		return $value === $this->value;
	}

	/**
	 * Parse saved settings data with all available options
	 *
	 * @param array|false $data
	 *
	 * @return array
	 */
	public function parse_data( $data ) {
		if ( $data === false ) {
			$data = [];
		}

		foreach ( $this->options as $key => $option ) {
			$data[ $key ] = isset( $data[ $key ] );
		}

		return $data;
	}
}