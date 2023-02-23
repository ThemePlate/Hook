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

class PluckAction extends BaseAction {

	/**
	 * @param $value array|string
	 *
	 * @return array|string
	 */
	public function process( $value ) {

		if ( is_array( $value ) ) {
			$index = array_search( $this->data, $value, true );

			if ( false !== $index ) {
				unset( $value[ $index ] );
			}
		} elseif ( is_string( $value ) ) {
			$value = str_replace( Helper::stringify( $this->data ), '', $value );
		}

		return $value;

	}

}
