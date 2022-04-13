<?php

namespace SimplyFilters\Admin\Controls;

class RadioControl extends Control {
	public function __construct( $params ) {
		parent::__construct( $params );
	}

	protected function render_setting_field() {

		echo '<ul class="sf-input-list">';

            if ( ! empty( $this->options ) ) {
                foreach ( $this->options as $value => $label ) {

                    printf( '<li><input type="radio" id="%2$s" name="%1$s" value="%2$s" %3$s> <label for="%2$s">%4$s</label></li>',
                        esc_attr( $this->key ),
                        $value,
	                    $value === $this->value ? 'checked' : '',
                        esc_html( $label )
                    );
                }
            }

		echo '</ul>';
	}
}