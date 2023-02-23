<?php

/**
 * Handler for the hook methods
 *
 * @package ThemePlate
 * @since 0.1.0
 */

namespace ThemePlate\Hook\Actions;

use ThemePlate\Hook\BaseAction;

class ReturnAction extends BaseAction {

	/**
	 * @return mixed
	 */
	public function process() {

		return $this->data;

	}

}
