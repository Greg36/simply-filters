<?php

namespace SimplyFilters\Admin\Controls;

use function SimplyFilters\load_inline_svg;

/**
 * Color setting's control
 *
 * @since   1.0.0
 */
class ColorControl extends Control {

	public function __construct( $params ) {
		parent::__construct( $params );
	}

	/**
	 * Render HTMl inputs for all options
	 */
	protected function render_setting_field() {

		echo '<div class="sf-color">';

		if ( ! empty( $this->options ) ) {

			foreach ( $this->options as $option ) {

				// Each option needs individual key and ID
				$key   = $this->key . '[' . $option['id'] . ']';
				$id    = $this->id . '-' . $option['slug'];
				$value = $this->get_value_from_option( $option );

				?>
                <div class="sf-color__row">

                    <div class="sf-color__picker">
						<?php
						printf( '<input id="%s" name="%s" type="text" value="%s" class="sf-color__field" data-default-color="%s"/>',
							esc_attr( $id ),
							esc_attr( $key ),
							esc_attr( $value ),
							isset( $option['default'] ) ? esc_attr( $option['default'] ) : '#ffffff'
						);
						?>
                    </div>

                    <div class="sf-color__preview">
                        <div class="sf-color__swatch"></div>
                        <div class="sf-color__swatch sf-color__swatch--selected">
							<?php echo load_inline_svg( 'check.svg' ); ?>
                        </div>

                        <div class="sf-color__name">
							<?php
							printf( '<label for="%s">%s</label>',
								esc_attr( $key ),
								wp_kses_post( $option['name'] )
							);
							?>
                        </div>
                    </div>

                </div>
				<?php
			}

		} else {
			printf( '<div class="sf-color__no-elements">%s</div>',
				esc_html__( 'There are no options for selected source', \Hybrid\app( 'locale' ) )
			);
		}

		echo '</div>';
	}

	/**
	 * Get option's value from term meta or fallback to default
	 *
	 * @param array $option
	 *
	 * @return mixed|string
	 */
	private function get_value_from_option( $option ) {
		$value = get_term_meta( $option['id'], \Hybrid\app( 'term-color-key' ), true );

		if ( ! $value ) {
			if ( isset( $this->value[ $option['slug'] ] ) ) {
				$value = $this->value[ $option['slug'] ];
			} else if ( isset( $option['default'] ) ) {
				$value = $option['default'];
			} else {
				$value = '#ffffff';
			}
		}

		return $value;
	}
}