<?php

/**
 * Handler for the hook methods
 *
 * @package ThemePlate
 * @since 0.1.0
 */

namespace ThemePlate\Hook;

use Closure;
use ThemePlate\Hook\Actions\AppendAction;
use ThemePlate\Hook\Actions\InsertAction;
use ThemePlate\Hook\Actions\PluckAction;
use ThemePlate\Hook\Actions\PrependAction;
use ThemePlate\Hook\Actions\ReplaceAction;
use ThemePlate\Hook\Actions\ReturnAction;

class Handler {

	/**
	 * @var mixed
	 */
	private $data;


	/**
	 * @param $data mixed
	 */
	public function __construct( $data ) {

		$this->data = $data;

	}


	public function return() {

		return ( new ReturnAction( $this->data ) )->handle( null );

	}


	public function stringy_data( $key = null ): string {

		$data = $this->data;

		if ( null !== $key && array_key_exists( $key, $data ) ) {
			$data = $data[ $key ];
		}

		return Helper::stringify( $data );

	}


	/**
	 * @param $values array|string
	 * @return array|string
	 */
	public function append( $values ) {

		return ( new AppendAction( $this->data ) )->handle( $values );

	}


	/**
	 * @param $values array|string
	 * @return array|string
	 */
	public function prepend( $values ) {

		return ( new PrependAction( $this->data ) )->handle( $values );

	}


	/**
	 * @param $values array|string
	 * @return array|string
	 */
	public function pluck( $values ) {

		return ( new PluckAction( $this->data ) )->handle( $values );

	}


	/**
	 * @param $values array|string
	 * @return array|string
	 */
	public function replace( $values ) {

		return ( new ReplaceAction( $this->data ) )->handle( $values );

	}


	/**
	 * @param $values array|string
	 * @return array|string
	 */
	public function insert( $values ) {

		return ( new InsertAction( $this->data ) )->handle( $values );

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

			$filter_instance = $filter['function'][0];
			$filter_action   = $filter['function'][1];
			$filter_data     = $filter_instance->return();

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
