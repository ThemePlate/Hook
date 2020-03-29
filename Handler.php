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


	public function return( $values ) {

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
			$index = array_search( $this->data['old'], $values );

			$values[ $index ] = $this->data['new'];
		} else {
			$values = str_replace( $this->data['old'], $this->data['new'], $values );
		}


		return $values;

	}


	public function once( $values ) {

		$action = $this->data['action'];

		remove_filter( current_filter(), array( $this, __FUNCTION__ ), $this->data['priority'] );

		$this->data = $this->data['value'];

		return $this->$action( $values );

	}

}
