<?php

namespace SimplyFilters\Admin;

use SimplyFilters\Admin\Controls\Control;

/**
 * Settings used for individual filter, group and general settings
 *
 * @since 1.0.0
 */
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
	 * @var int
	 */
	private $id;

	public function __construct( $id, $data ) {
		$this->id   = $id;
		$this->data = $data;
	}

	/**
	 * Return parsed data from all settings
	 *
	 * @return array
	 */
	public function get_data() {
		$data = [];
		foreach ( $this->settings as $setting ) {
			$value                   = isset( $this->data[ $setting['key'] ] ) ? $this->data[ $setting['key'] ] : false;
			$data[ $setting['key'] ] = $setting['control']->parse_data( $value );
		}

		return $data;
	}

	/**
	 * Add new setting
	 *
	 * @param string $key
	 * @param string $type
	 * @param array $data
	 * @param int $order
	 */
	public function add( $key, $type, $data, $order = 10 ) {
		$control = $this->get_control( $type, $data );

		if ( $control ) {
			$this->settings[] = [
				'key'     => $key,
				'control' => $control,
				'order'   => $order
			];
		}
	}

	/**
	 * Instantiate new control object base on given type
	 *
	 * @param string $type
	 * @param array $data
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
	 * Render all settings form fields
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
						'key'           => $this->prefix_key( $key ),
						'value'         => isset( $this->data[ $key ] ) ? $this->data[ $key ] : '',
						'load_defaults' => isset( $this->data['load_defaults'] ) ? $this->data['load_defaults'] : false,
						'id'            => $this->prefix_id( $key ),
						'label'         => $key
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