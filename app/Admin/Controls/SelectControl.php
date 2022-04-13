<?php

namespace SimplyFilters\Admin\Controls;

class SelectControl extends Control {

	public function __construct( $params ) {
		parent::__construct( $params );
	}

	protected function render_setting_field() {

		printf( '<select name="%1$s" id="%1$s" >',
			esc_attr( $this->key )
		);

		if ( ! empty( $this->options ) ) {
			foreach ( $this->options as $value => $label ) {

				printf( '<option value="%s" %s>%s</option>',
					$value,
					$value == $this->value ? 'selected' : '',
					esc_html( $label )
				);
			}
		}

		echo '</select>';
	}
}