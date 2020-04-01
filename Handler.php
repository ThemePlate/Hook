<?php

/**
 * Handler for the hook methods
 *
 * @package ThemePlate
 * @since 0.1.0
 */

namespace ThemePlate;

class Handler {

	private $data;


	public function __construct( $data ) {

		$this->data = $data;

	}


	public function return() {

		return $this->data;

	}


	public function append( $values ) {

		if ( is_array( $values ) ) {
			$values[] = $this->data;
		} else {
			$values .= $this->data;
		}

		return $values;

	}


	public function prepend( $values ) {

		if ( is_array( $values ) ) {
			array_unshift( $values, $this->data );
		} else {
			$values = $this->data . $values;
		}

		return $values;

	}


	public function pluck( $values ) {

		if ( is_array( $values ) ) {
			$index = array_search( $this->data, $values );

			unset( $values[ $index ] );
		} else {
			$values = str_replace( $this->data, '', $values );
		}

		return $values;

	}


	public function replace( $values ) {

		if ( is_array( $values ) ) {
			$index = array_search( $this->data[0], $values );

			$values[ $index ] = $this->data[1];
		} else {
			$values = str_replace( $this->data[0], $this->data[1], $values );
		}


		return $values;

	}


	public function once( $values ) {

		$action = array_shift( $this->data['value'] );

		remove_filter( current_filter(), array( $this, __FUNCTION__ ), $this->data['priority'] );

		$this->data = $this->data['value'];

		return $this->$action( $values );

	}


	public function remove( $tag ) {

		global $wp_filter;

		extract( $this->data );

		$retval = false;

		if ( ! isset( $wp_filter[ $tag ], $wp_filter[ $tag ][ $priority ] ) ) {
			return $retval;
		}

		foreach ( $wp_filter[ $tag ][ $priority ] as $idx => $filter ) {
			if ( ! is_array( $filter['function'] ) || ! $filter['function'][0] instanceof \ThemePlate\Handler ) {
				continue;
			}

			if ( $action === $filter['function'][1] && $value === $filter['function'][0]->return() ) {
				$retval = true;

				unset( $wp_filter[ $tag ]->callbacks[ $priority ][ $idx ] );
			}
		}

		return $retval;

	}

}
