<?php

/**
 * Handler for the hook methods
 *
 * @package ThemePlate
 * @since 0.1.0
 */

namespace ThemePlate\Hook;

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


	/**
	 * @param $values array|string
	 * @return array|string
	 */
	public function append( $values ) {

		if ( is_array( $values ) ) {
			$values[] = $this->data;
		} elseif ( is_string( $values ) ) {
			$values .= $this->data;
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
			$values = $this->data . $values;
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
			$values = str_replace( $this->data, '', $values );
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
			$values = str_replace( $this->data[0], $this->data[1], $values );
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
			$values = substr_replace( $values, $this->data[0], $this->data[1], 0 );
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

		$value    = $this->data['value'];
		$priority = $this->data['priority'];
		$retval   = false;

		if ( ! isset( $wp_filter[ $tag ], $wp_filter[ $tag ][ $priority ] ) ) {
			return $retval;
		}

		$action = array_shift( $value );

		if ( 1 === count( $value ) ) {
			$value = $value[0];
		}

		foreach ( $wp_filter[ $tag ][ $priority ] as $idx => $filter ) {
			if ( ! is_array( $filter['function'] ) || ! $filter['function'][0] instanceof self ) {
				continue;
			}

			if ( ( $action === $filter['function'][1] && $value === $filter['function'][0]->return() ) ||
				( 'once' === $filter['function'][1] && $value === $filter['function'][0]->return()['value'][1] ) ) {
				$retval = true;

				unset( $wp_filter[ $tag ]->callbacks[ $priority ][ $idx ] );
			}
		}

		return $retval;

	}

}
