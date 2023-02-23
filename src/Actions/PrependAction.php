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

class PrependAction extends BaseAction {

	/**
	 * @param $value array|string
	 * @return array|string
	 */
	public function process( $value ) {

		if ( is_array( $value ) ) {
			array_unshift( $value, $this->data );
		} elseif ( is_string( $value ) ) {
			$value = Helper::stringify( $this->data ) . $value;
		}

		return $value;

	}

}
