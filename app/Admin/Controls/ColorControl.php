<?php

namespace SimplyFilters\Admin\Controls;

use function SimplyFilters\load_inline_svg;

class ColorControl extends Control {

	public function __construct( $params ) {
		parent::__construct( $params );
	}

	protected function render_setting_field() {

		echo '<div class="sf-color">';

		if ( ! empty( $this->options ) ) {

			foreach ( $this->options as $option => $name ) {

				// Each picker needs individual key and ID
				$key = $this->key . '[' . $option . ']';
				$id  = $this->id  . '-' . $option;

				// Get the color value from term's meta
				$value = get_term_meta( $option, \Hybrid\app( 'term-color-key' ), true ); //@todo: this should be something to remove on uninstall

                // If there is no term data use option value directly
				if ( ! $value ) {
                    if( isset( $this->value[ $option ] ) ) {
	                    $value = $this->value[ $option ];
                    } else {
					    $value = '#ffffff';
                    }
				}

				?>
                <div class="sf-color__row">

                    <div class="sf-color__picker">
		                <?php
		                printf( '<input id="%s" name="%s" type="text" value="%s" class="sf-color__field" data-default-color="#fff"/>',
                            esc_attr( $id ),
			                esc_attr( $key ),
			                esc_attr( $value )
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
								wp_kses_post( $name )
							);
							?>
                        </div>
                    </div>

                </div>
				<?php
			}

		} else {
			printf( '<div class="sf-color__no-elements">%s</div>',
				__( 'There are no options for selected source', \Hybrid\app( 'locale' ) ) // @todo: change message for better one or even dedicated for attribute or category
			);
		}

		echo '</div>';
	}
}