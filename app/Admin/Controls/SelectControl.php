<?php

namespace SimplyFilters\Admin\Controls;

/**
 * Select setting's control
 *
 * @since   1.0.0
 */
class SelectControl extends Control {

	public function __construct( $params ) {
		parent::__construct( $params );
	}

	/**
	 * Render HTMl inputs for all options
	 */
	protected function render_setting_field() {

		printf( '<select id="%s" name="%s">',
			esc_attr( $this->id ),
			esc_attr( $this->key )
		);

		if ( ! empty( $this->options ) ) {
			foreach ( $this->options as $value => $label ) {

				printf( '<option value="%s" %s>%s</option>',
					esc_attr( $value ),
					$value == $this->value ? 'selected' : '',
					wp_kses_post( $label )
				);
			}
		}

		echo '</select>';
	}
}