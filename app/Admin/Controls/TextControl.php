<?php

namespace SimplyFilters\Admin\Controls;

class TextControl extends Control {

	public function __construct( $params ) {
		parent::__construct( $params );
	}

	protected function render_setting_field() {
		printf( '<input type="text" name="%1$s" id="%1$s" value="%2$s">',
			esc_attr( $this->key ),
			esc_attr( $this->value )
		);
	}
}