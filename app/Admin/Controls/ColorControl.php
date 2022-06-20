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

			foreach ( $this->options as $option ) {

				// Each picker needs individual key and ID
				$key = $this->key . '[' . $option['id'] . ']';
				$id  = $this->id  . '-' . $option['slug'];

				// Get the color value from term's meta
				$value = get_term_meta( $option['id'], \Hybrid\app( 'term-color-key' ), true );

                // If there is no term data use option value directly
				if ( ! $value ) {
                    if( isset( $this->value[ $option['slug'] ] ) ) {
	                    $value = $this->value[ $option['slug'] ];
                    } else if( isset( $option['default'] ) ) {
                        $value = $option['default'];
                    } else {
					    $value = '#ffffff';
                    }
				}

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
				__( 'There are no options for selected source', \Hybrid\app( 'locale' ) )
			);
		}

		echo '</div>';
	}
}