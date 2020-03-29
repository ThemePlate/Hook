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


	public function append( $values ) {

		$values[] = $this->data;

		return $values;

	}


	public function prepend( $values ) {

		array_unshift( $values, $this->data );

		return $values;

	}


	public function pluck( $values ) {

		$index = array_search( $this->data, $values );

		unset( $values[ $index ] );

		return $values;

	}


	public function replace( $values ) {

		$index = array_search( $this->data['old'], $values );

		$values[ $index ] = $this->data['new'];

		return $values;

	}

}
