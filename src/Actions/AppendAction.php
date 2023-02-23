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

class AppendAction extends BaseAction {

	/**
	 * @param $value array|string
	 * @return array|string
	 */
	public function process( $value ) {

		if ( is_array( $value ) ) {
			$value[] = $this->data;
		} elseif ( is_string( $value ) ) {
			$value .= Helper::stringify( $this->data );
		}

		return $value;

	}

}
