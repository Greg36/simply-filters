<?php

namespace SimplyFilters\Admin\Controls;

class SelectControl extends Control {

	public function __construct( $params ) {
		parent::__construct( $params );
	}

	protected function render_setting_field() {

		printf( '<select id="%s" name="%s">',
			esc_attr( $this->id ),
			esc_attr( $this->key )
		);

		if ( ! empty( $this->options ) ) {
			foreach ( $this->options as $value => $label ) {

				printf( '<option value="%s" %s>%s</option>',
					$value,
					$value == $this->value ? 'selected' : '',
					wp_kses_post( $label )
				);
			}
		}

		echo '</select>';
	}
}