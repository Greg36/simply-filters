<?php

namespace SimplyFilters\Admin\Controls;

/**
 * Text setting's control
 *
 * @since   1.0.0
 */
class TextControl extends Control {

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

		$this->unique   = isset( $params['unique'] ) ? (bool) $params['unique'] : false;
		$this->required = isset( $params['required'] ) ? (bool) $params['required'] : false;
	}

	/**
	 * Render HTMl input
	 */
	protected function render_setting_field() {

		if ( ! empty( $this->options ) ) {

			if ( ! is_array( $this->value ) ) {
				$this->value = [];
			}

			echo '<ul class="sf-text-list">';

			// Render group of text fields
			foreach ( $this->options as $option ) {

				printf( '<li><input type="text" id="%1$s" name="%2$s" value="%3$s" %4$s %5$s><label for="%1$s">%6$s</label></li>',
					esc_attr( $this->id . '_' . $option['key'] ),
					esc_attr( $this->key . '[' . $option['key'] . ']' ),
					array_key_exists( $option['key'], $this->value ) ? esc_attr( $this->value[ $option['key'] ] ) : esc_attr( $option['default'] ),
					$this->unique ? sprintf( 'data-unique="%s"', esc_attr( $this->label ) ) : '',
					$this->required ? 'required="required"' : '',
					wp_kses_post( $option['label'] )
				);
			}

			echo '</ul>';

		} else {

			// Render single text field
			printf( '<input type="text" id="%s" name="%s" value="%s" %s %s>',
				esc_attr( $this->id ),
				esc_attr( $this->key ),
				esc_attr( $this->value ),
				$this->unique ? sprintf( 'data-unique="%s"', esc_attr( $this->label ) ) : '',
				$this->required ? 'required="required"' : ''
			);

		}
	}

	/**
	 * Parse saved settings data as either string or array with options
	 *
	 * @param array|string $data
	 *
	 * @return array|string
	 */
	public function parse_data( $data ) {
		if ( ! is_array( $data ) && $data ) {
			return $data;
		}
		if ( $data === false ) {
			$data = [];
		}

		foreach ( $this->options as $key => $option ) {
			$data[ $key ] = isset( $data[ $key ] );
		}

		return $data;
	}
}