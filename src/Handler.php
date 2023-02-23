<?php

/**
 * Handler for the hook methods
 *
 * @package ThemePlate
 * @since 0.1.0
 */

namespace ThemePlate\Hook;

use Closure;
use Error;

/**
 * @method mixed return( mixed $value )
 * @method mixed append( mixed $value )
 * @method mixed prepend( mixed $value )
 * @method mixed pluck( mixed $value )
 * @method mixed replace( mixed $value )
 * @method mixed insert( mixed $value )
 */
class Handler {

	/**
	 * @var mixed
	 */
	protected $data;

	public const ACTIONS = array(
		'return',
		'append',
		'prepend',
		'pluck',
		'replace',
		'insert',
	);


	/**
	 * @param $data mixed
	 */
	public function __construct( $data ) {

		$this->data = $data;

	}


	public function __call( string $name, array $arguments ) {

		if ( in_array( $name, static::ACTIONS, true ) ) {
			/** @var ActionInterface $class */
			$class = __NAMESPACE__ . '\\Actions\\' . ucfirst( $name ) . 'Action';

			return call_user_func_array( array( new $class( $this->data ), 'handle' ), $arguments );
		}

		throw new Error( 'Call to undefined method ' . __CLASS__ . '::' . $name . '()' );

	}


	/**
	 * @param $values array|string
	 * @return array|string
	 */
	public function once( $values ) {

		if ( ! isset( $this->data['value'], $this->data['priority'] ) ) {
			return $values;
		}

		$action = array_shift( $this->data['value'] );

		remove_filter( current_filter(), array( $this, __FUNCTION__ ), $this->data['priority'] );

		$this->data = $this->data['value'];

		if ( 1 === count( $this->data ) ) {
			$this->data = $this->data[0];
		}

		return $this->$action( $values );

	}


	public function remove( string $tag ): bool {

		if ( ! isset( $this->data['value'], $this->data['priority'] ) ) {
			return false;
		}

		global $wp_filter;

		$wanted_value    = $this->data['value'];
		$wanted_priority = $this->data['priority'];

		if ( ! isset( $wp_filter[ $tag ], $wp_filter[ $tag ][ $wanted_priority ] ) ) {
			return false;
		}

		$wanted_action = array_shift( $wanted_value );
		$is_successful = false;

		if ( 1 === count( $wanted_value ) ) {
			$wanted_value = $wanted_value[0];
		}

		foreach ( $wp_filter[ $tag ][ $wanted_priority ] as $idx => $filter ) {
			if (
				! is_callable( $filter['function'] ) ||
				$filter['function'] instanceof Closure ||
				! $filter['function'][0] instanceof self
			) {
				continue;
			}

			/** @var Handler $filter_instance */
			$filter_instance = $filter['function'][0];
			$filter_action   = $filter['function'][1];
			$filter_data     = $filter_instance->return( null );

			if (
				( $wanted_action === $filter_action && $wanted_value === $filter_data ) ||
				( 'once' === $filter_action && $wanted_value === $filter_data['value'][1] )
			) {
				$is_successful = true;

				unset( $wp_filter[ $tag ]->callbacks[ $wanted_priority ][ $idx ] );
			}
		}

		return $is_successful;

	}

}
