<?php

namespace SimplyFilters\Admin\Controls;

class ToggleControl extends Control {

	public function __construct( $params ) {
		parent::__construct( $params );
	}

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
            <span class="sf-toggle__first"><?php _e( 'On', \Hybrid\app( 'locale' ) ) ?></span>
            <span class="sf-toggle__second"><?php _e( 'Off', \Hybrid\app( 'locale' ) ) ?></span>
            <span class="sf-toggle__slider"><span></span></span>
        </div>
		<?php

		echo '</label>';

	}

	/**
     * Return settings data as bool
     *
	 * @param $data
	 *
	 * @return bool
	 */
	public function parse_data( $data ) {
        return $data ? true : false;
	}
}