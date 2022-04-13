<?php

namespace SimplyFilters\Admin\Controls;

class ToggleControl extends Control {

	public function __construct( $params ) {
		parent::__construct( $params );
	}

	protected function render_setting_field() {

		echo '<label class="sf-toggle">';

		printf( '<input type="checkbox" name="%1$s" id="%1$s" value="%2$s">',
			esc_attr( $this->key ),
			esc_attr( $this->value )
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
}