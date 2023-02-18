<?php

/**
 * Advanced wrapper for filters
 *
 * @package ThemePlate
 * @since 0.1.0
 */

namespace ThemePlate;

use ThemePlate\Hook\Handler;

class Hook {

	public static function return( string $tag, $value, int $priority = 10 ): bool {

		return self::handler( 'return', $tag, $value, $priority );

	}

	public static function append( string $tag, $value, int $priority = 10 ): bool {

		return self::handler( 'append', $tag, $value, $priority );

	}


	public static function prepend( string $tag, $value, int $priority = 10 ): bool {

		return self::handler( 'prepend', $tag, $value, $priority );

	}


	public static function pluck( string $tag, $value, int $priority = 10 ): bool {

		return self::handler( 'pluck', $tag, $value, $priority );

	}


	public static function replace( string $tag, $old, $new, int $priority = 10 ): bool {

		return self::handler( 'replace', $tag, array( $old, $new ), $priority );

	}


	public static function insert( string $tag, $value, int $position, int $priority = 10 ): bool {

		return self::handler( 'insert', $tag, array( $value, $position ), $priority );

	}


	public static function once( string $tag, array $value, int $priority = 10 ): bool {

		return self::handler( 'once', $tag, compact( 'value', 'priority' ), $priority );

	}


	public static function remove( string $tag, array $value, int $priority = 10 ): bool {

		$handler = new Handler( compact( 'value', 'priority' ) );

		return $handler->remove( $tag );

	}


	private static function handler( string $action, string $tag, $value, int $priority ): bool {

		return add_filter( $tag, array( new Handler( $value ), $action ), $priority );

	}

}
