<?php

namespace SimplyFilters\Admin;

use SimplyFilters\Admin\Controls\Control;

class Settings {

	/**
	 * @var array Settings controls
	 */
	protected $settings = [];

	/**
	 * @var array
	 */
	private $data;

	/**
	 * @var
	 */
	private $id;

	/**
	 * @param array $settings
	 */
	public function __construct( $id, $data ) {
		$this->id   = $id;
		$this->data = $data;

		$this->types = \Hybrid\app( 'input_controls' );
	}


	/**
	 * Save control object
	 *
	 * @param string $key
	 * @param string $type
	 * @param array $data
	 * @param int $order
	 */
	public function add( $key, $type, $data, $order = 10 ) {
		$control = $this->get_control( $type, $data );

		if( $control ) {
			$this->settings[] = [
				'key'     => $key,
				'control' => $control,
				'order'   => $order
			];
		}
	}

	/**
	 * Instantiate new control object
	 *
	 * @param $type
	 * @param $data
	 *
	 * @return Control | false
	 */
	private function get_control( $type, $data ) {
		$name  = ucfirst( $type ) . 'Control';
		$class = "SimplyFilters\\Admin\\Controls\\{$name}";

		if ( class_exists( $class ) ) {
			return new $class( $data );
		}

		return false;
	}

	/**
	 * Render each setting control
	 */
	public function render() {

		// Sort all settings according to their order
		usort( $this->settings, function ( $item1, $item2 ) {
			return $item1['order'] <=> $item2['order'];
		} );

		if ( ! empty( $this->settings ) ) {
			foreach ( $this->settings as $setting ) {
				$key = $setting['key'];
				$setting['control']->render( [
						'key'   => $this->prefix_key( $key ),
						'value' => isset( $this->data[ $key ] ) ? $this->data[ $key ] : '',
						'id'    => $this->prefix_id( $key ),
						'label' => $key
					]
				);
			}
		}
	}

	/**
	 * Prefix key to be used in name attribute on input field
	 *
	 * @param string $key
	 *
	 * @return string
	 */
	private function prefix_key( $key ) {
		return sprintf( '%s[%s][%s]',
			\Hybrid\app( 'prefix' ),
			$this->id,
			$key
		);
	}

	/**
	 * Prefix id to be used in id attribute on input field
	 *
	 * @param string $key
	 *
	 * @return string
	 */
	private function prefix_id( $key ) {
		return sprintf( '%s-%s-%s',
			\Hybrid\app( 'prefix' ),
			$this->id,
			$key
		);
	}

}