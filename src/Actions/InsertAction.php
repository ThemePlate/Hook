<?php

/**
 * Handler for the hook methods
 *
 * @package ThemePlate
 * @since 0.1.0
 */

namespace ThemePlate\Hook\Actions;

use ThemePlate\Hook\BaseAction;
use ThemePlate\Hook\Helper;

class InsertAction extends BaseAction {

	/**
	 * @param $value array|string
	 *
	 * @return array|string
	 */
	public function process( $value ) {

		if ( is_array( $value ) ) {
			array_splice( $value, $this->data[1], 0, $this->data[0] );
		} elseif ( is_string( $value ) ) {
			$replace = is_array( $this->data[0] ) ? array_map( array( Helper::class, 'stringify' ), $this->data[0] ) : Helper::stringify( $this->data[0] );

			$value = substr_replace( $value, $replace, (int) $this->data[1], 0 );
		}

		return $value;

	}

}
