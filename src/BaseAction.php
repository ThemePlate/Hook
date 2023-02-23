<?php

/**
 * @package ThemePlate
 */

namespace ThemePlate\Hook;

abstract class BaseAction implements ActionInterface {

	/**
	 * @var mixed
	 */
	protected $data;


	/**
	 * @param $value mixed
	 */
	public function __construct( $value ) {

		$this->data = $value;

	}


	/**
	 * @return mixed
	 */
	public function handle() {

		return call_user_func( array( $this, 'process' ), func_get_arg( 0 ) );

	}

}
