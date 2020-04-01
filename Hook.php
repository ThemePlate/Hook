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


	public static function insert( $tag, $value, $position, $priority = 10 ) {

		return self::handler( 'insert', $tag, array( $value, $position ), $priority );

	}


	public static function once( $tag, $value, $priority = 10 ) {

		return self::handler( 'once', $tag, compact( 'value', 'priority' ), $priority );

	}


	public static function remove( $tag, $value, $priority = 10 ) {

		$handler = new Handler( compact( 'value', 'priority' ) );

		return $handler->remove( $tag );

	}


	private static function handler( $action, $tag, $value, $priority ) {

		return add_filter( $tag, array( new Handler( $value ), $action ), $priority );

	}

}
