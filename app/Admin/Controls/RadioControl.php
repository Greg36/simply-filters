<?php

namespace SimplyFilters\Admin\Controls;

/**
 * Radio setting's control
 *
 * @since   1.0.0
 */
class RadioControl extends Control {
	public function __construct( $params ) {
		parent::__construct( $params );
	}

	/**
	 * Render HTMl inputs for all options
	 */
	protected function render_setting_field() {

		echo '<ul class="sf-input-list">';

		if ( ! empty( $this->options ) ) {

			// Fallback to default value
			if ( $this->value === '' ) {
				$this->value = key( $this->options );
			}

			foreach ( $this->options as $value => $label ) {

				printf( '<li><input type="radio" id="%1$s" name="%2$s" value="%3$s" %4$s> <label for="%1$s">%5$s</label></li>',
					esc_attr( $this->id . '_' . $value ),
					esc_attr( $this->key ),
					$value,
					$value === $this->value ? 'checked' : '',
					wp_kses_post( $label )
				);
			}
		}

		echo '</ul>';
	}
}