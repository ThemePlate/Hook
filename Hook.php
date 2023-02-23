<?php

/**
 * Advanced wrapper for filters
 *
 * @package ThemePlate
 * @since 0.1.0
 */

namespace ThemePlate;

use Error;
use ThemePlate\Hook\Handler;

/**
 * @method static bool return( string $tag, $value, int $priority = 10 )
 * @method static bool append( string $tag, $value, int $priority = 10 )
 * @method static bool prepend( string $tag, $value, int $priority = 10 )
 * @method static bool pluck( string $tag, $value, int $priority = 10 )
 * @method static bool replace( string $tag, $old, $new, int $priority = 10 )
 * @method static bool insert( string $tag, $value, int $position, int $priority = 10 )
 * @method static bool once( string $tag, array $value, int $priority = 10 )
 */
class Hook {

	public static function __callStatic( string $action, array $arguments ) {

		if ( in_array( $action, array( ...Handler::ACTIONS, 'once' ), true ) ) {
			$tag      = $arguments[0];
			$value    = $arguments[1];
			$priority = $arguments[2] ?? 10;

			if ( 'once' === $action ) {
				$value = compact( 'value', 'priority' );
			}

			if ( in_array( $action, array( 'insert', 'replace' ), true ) ) {
				$value    = array( $arguments[1], $arguments[2] );
				$priority = $arguments[3] ?? 10;
			}

			return add_filter( $tag, array( new Handler( $value ), $action ), $priority );
		}

		throw new Error( 'Call to undefined method ' . __CLASS__ . '::' . $action . '()' );

	}

	public static function remove( string $tag, array $value, int $priority = 10 ): bool {

		$handler = new Handler( compact( 'value', 'priority' ) );

		return $handler->remove( $tag );

	}

}
