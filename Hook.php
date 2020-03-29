<?php

/**
 * Advanced wrapper for filters
 *
 * @package ThemePlate
 * @since 0.1.0
 */

namespace ThemePlate;

class Hook {

	public static function append( $tag, $value ) {

		return self::handler( 'append', $tag, $value );

	}


	public static function prepend( $tag, $value ) {

		return self::handler( 'prepend', $tag, $value );

	}


	public static function pluck( $tag, $value ) {

		return self::handler( 'pluck', $tag, $value );

	}


	public static function replace( $tag, $old, $new ) {

		return self::handler( 'replace', $tag, compact( 'old', 'new' ) );

	}


	private static function handler( $action, $tag, $value ) {

		return add_filter( $tag, array( new Handler( $value ), $action ) );

	}

}
