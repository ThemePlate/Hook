<?php

/**
 * Advanced wrapper for filters
 *
 * @package ThemePlate
 * @since 0.1.0
 */

namespace ThemePlate;

class Hook {

	public static function return( $tag, $value, $priority = 10 ) {

		return self::handler( 'return', $tag, $value, $priority );

	}

	public static function append( $tag, $value, $priority = 10 ) {

		return self::handler( 'append', $tag, $value, $priority );

	}


	public static function prepend( $tag, $value, $priority = 10 ) {

		return self::handler( 'prepend', $tag, $value, $priority );

	}


	public static function pluck( $tag, $value, $priority = 10 ) {

		return self::handler( 'pluck', $tag, $value, $priority );

	}


	public static function replace( $tag, $old, $new, $priority = 10 ) {

		return self::handler( 'replace', $tag, array( $old, $new ), $priority );

	}


	public static function once( $action, $tag, $value, $priority = 10 ) {

		return self::handler( 'once', $tag, compact( 'action', 'value', 'priority' ), $priority );

	}


	private static function handler( $action, $tag, $value, $priority ) {

		return add_filter( $tag, array( new Handler( $value ), $action ), $priority );

	}

}
