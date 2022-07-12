<?php

namespace SimplyFilters\Admin\Controls;

/**
 * Toggle setting's control
 *
 * @since   1.0.0
 */
class ToggleControl extends Control {

	public function __construct( $params ) {
		parent::__construct( $params );
	}

	/**
	 * Render HTMl input
	 */
	protected function render_setting_field() {

		echo '<label class="sf-toggle">';

		printf( '<input type="checkbox" id="%s" name="%s" value="%s" %s>',
			esc_attr( $this->id ),
			esc_attr( $this->key ),
			esc_attr( $this->label ),
			$this->value ? 'checked' : ''
		);
		?>

        <div class="sf-toggle__switch">
            <span class="sf-toggle__first"><?php esc_html_e( 'On', \Hybrid\app( 'locale' ) ) ?></span>
            <span class="sf-toggle__second"><?php esc_html_e( 'Off', \Hybrid\app( 'locale' ) ) ?></span>
            <span class="sf-toggle__slider"><span></span></span>
        </div>

		<?php
		echo '</label>';

	}

	/**
	 * Return settings data as bool
	 *
	 * @param mixed $data
	 *
	 * @return bool
	 */
	public function parse_data( $data ) {
		return $data ? true : false;
	}
}