<?php

/**
 * Handler for the hook methods
 *
 * @package ThemePlate
 * @since 0.1.0
 */

namespace ThemePlate\Hook;

use Closure;

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

		return $this->data;

	}


	protected function stringify( $data ): string {

		if ( is_scalar( $data ) || null === $data ) {
			$data = (string) $data;
		} else {
			// phpcs:ignore WordPress.WP.AlternativeFunctions.json_encode_json_encode
			$data = json_encode( $data );
		}

		return $data;

	}


	public function stringy_data( $key = null ): string {

		$data = $this->data;

		if ( null !== $key && array_key_exists( $key, $data ) ) {
			$data = $data[ $key ];
		}

		return $this->stringify( $data );

	}


	/**
	 * @param $values array|string
	 * @return array|string
	 */
	public function append( $values ) {

		if ( is_array( $values ) ) {
			$values[] = $this->data;
		} elseif ( is_string( $values ) ) {
			$values .= $this->stringy_data();
		}

		return $values;

	}


	/**
	 * @param $values array|string
	 * @return array|string
	 */
	public function prepend( $values ) {

		if ( is_array( $values ) ) {
			array_unshift( $values, $this->data );
		} elseif ( is_string( $values ) ) {
			$values = $this->stringy_data() . $values;
		}

		return $values;

	}


	/**
	 * @param $values array|string
	 * @return array|string
	 */
	public function pluck( $values ) {

		if ( is_array( $values ) ) {
			$index = array_search( $this->data, $values, true );

			if ( false !== $index ) {
				unset( $values[ $index ] );
			}
		} elseif ( is_string( $values ) ) {
			$values = str_replace( $this->stringy_data(), '', $values );
		}

		return $values;

	}


	/**
	 * @param $values array|string
	 * @return array|string
	 */
	public function replace( $values ) {

		if ( is_array( $values ) ) {
			$index = array_search( $this->data[0], $values, true );

			if ( false !== $index ) {
				$values[ $index ] = $this->data[1];
			}
		} elseif ( is_string( $values ) ) {
			$search  = is_array( $this->data[0] ) ? array_map( array( $this, 'stringify' ), $this->data[0] ) : $this->stringy_data( 0 );
			$replace = is_array( $this->data[1] ) ? array_map( array( $this, 'stringify' ), $this->data[1] ) : $this->stringy_data( 1 );

			$values = str_replace( $search, $replace, $values );
		}

		return $values;

	}


	/**
	 * @param $values array|string
	 * @return array|string
	 */
	public function insert( $values ) {

		if ( is_array( $values ) ) {
			array_splice( $values, $this->data[1], 0, $this->data[0] );
		} elseif ( is_string( $values ) ) {
			$replace = is_array( $this->data[0] ) ? array_map( array( $this, 'stringify' ), $this->data[0] ) : $this->stringy_data( 0 );

			$values = substr_replace( $values, $replace, (int) $this->data[1], 0 );
		}

		return $values;

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
