<?php

namespace SimplyFilters\Admin\Controls;

class NumberControl extends Control {

	/**
	 * @var bool Should value be unique among all filters in group.
	 */
	protected $unique;

	/**
	 * @var bool Should control be required.
	 */
	protected $required;

	public function __construct( $params ) {
		parent::__construct( $params );

		$this->unique = isset( $params['unique'] ) ? (bool) $params['unique'] : false;
		$this->required = isset( $params['required'] ) ? (bool) $params['required'] : false;
	}

	protected function render_setting_field() {
		printf( '<input type="number" name="%s" id="%s" value="%s" %s %s>',
			esc_attr( $this->key ),
			esc_attr( $this->id ),
			esc_attr( $this->value ),
			$this->unique ? sprintf( 'data-unique="%s"', esc_attr( $this->label ) ) : '',
			$this->required ? 'required="required"' : ''
		);
	}
}