<?php

namespace SimplyFilters\Admin\Controls;

class TextControl extends Control {

	public function __construct( $params ) {
		parent::__construct( $params );
	}

	protected function render_setting_field() {
		printf( '<input type="text" name="%s" id="%s" value="%s">',
			esc_attr( $this->key ),
			esc_attr( $this->id ),
			esc_attr( $this->value )
		);
	}
}