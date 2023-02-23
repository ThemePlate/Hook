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

class ReplaceAction extends BaseAction {

	/**
	 * @param $value array|string
	 *
	 * @return array|string
	 */
	public function process( $value ) {

		if ( is_array( $value ) ) {
			$index = array_search( $this->data[0], $value, true );

			if ( false !== $index ) {
				$value[ $index ] = $this->data[1];
			}
		} elseif ( is_string( $value ) ) {
			$search  = is_array( $this->data[0] ) ? array_map( array( Helper::class, 'stringify' ), $this->data[0] ) : Helper::stringify( $this->data[0] );
			$replace = is_array( $this->data[1] ) ? array_map( array( Helper::class, 'stringify' ), $this->data[1] ) : Helper::stringify( $this->data[1] );

			$value = str_replace( $search, $replace, $value );
		}

		return $value;

	}

}
