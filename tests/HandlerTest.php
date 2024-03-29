<?php

/**
 * @package ThemePlate
 */

namespace Tests;

use Error;
use stdClass;
use ThemePlate\Hook\Handler;
use WP_UnitTestCase;

class HandlerTest extends WP_UnitTestCase {
	public const HOOK = 'test_hook';

	public function test_unknown_action(): void {
		$this->expectException( Error::class );
		$this->expectExceptionMessage( 'Call to undefined method ' . Handler::class . '::unknown()' );

		add_filter( self::HOOK, array( new Handler( 'test' ), 'unknown' ) );
		apply_filters( self::HOOK, 'test' );
	}

	public function for_return(): array {
		return array(
			array(
				'initial',
				'test',
				'test',
			),
			array(
				array( 'initial' ),
				array( 'test' ),
				array( 'test' ),
			),
			array(
				PHP_INT_MAX,
				PHP_INT_MIN,
				PHP_INT_MIN,
			),
		);
	}

	/** @dataProvider for_return */
	public function test_return( $initial, $wanted, $expected ) {
		add_filter( self::HOOK, array( new Handler( $wanted ), 'return' ) );

		$this->assertSame( $expected, apply_filters( self::HOOK, $initial ) );
	}

	public function for_append(): array {
		return array(
			array(
				'initial',
				'test',
				'initialtest',
			),
			array(
				array( 'initial' ),
				'test',
				array( 'initial', 'test' ),
			),
			array(
				PHP_INT_MIN,
				987,
				PHP_INT_MIN,
			),
		);
	}

	/** @dataProvider for_append */
	public function test_append( $initial, $wanted, $expected ) {
		add_filter( self::HOOK, array( new Handler( $wanted ), 'append' ) );

		$this->assertSame( $expected, apply_filters( self::HOOK, $initial ) );
	}

	public function for_prepend(): array {
		return array(
			array(
				'initial',
				'test',
				'testinitial',
			),
			array(
				array( 'initial' ),
				'test',
				array( 'test', 'initial' ),
			),
			array(
				PHP_FLOAT_MAX,
				'123',
				PHP_FLOAT_MAX,
			),
		);
	}

	/** @dataProvider for_prepend */
	public function test_prepend( $initial, $wanted, $expected ) {
		add_filter( self::HOOK, array( new Handler( $wanted ), 'prepend' ) );

		$this->assertSame( $expected, apply_filters( self::HOOK, $initial ) );
	}

	public function for_pluck(): array {
		return array(
			array(
				'initial',
				'ial',
				'init',
			),
			array(
				array( 'initial', 'values' ),
				'values',
				array( 'initial' ),
			),
			array(
				PHP_FLOAT_MIN,
				array( 987 ),
				PHP_FLOAT_MIN,
			),
		);
	}

	/** @dataProvider for_pluck */
	public function test_pluck( $initial, $wanted, $expected ) {
		add_filter( self::HOOK, array( new Handler( $wanted ), 'pluck' ) );

		$this->assertSame( $expected, apply_filters( self::HOOK, $initial ) );
	}

	public function for_replace(): array {
		$object = new stdClass();

		return array(
			array(
				'initial',
				array( 'initi', 'fin' ),
				'final',
			),
			array(
				'tester',
				array( array( 'er' ), null ),
				'test',
			),
			array(
				'test123ingthis',
				array( array( 123, 'ing' ), array( true, false ) ),
				'test1this',
			),
			array(
				array( 'initial', 'values' ),
				array( 'initial', 'final' ),
				array( 'final', 'values' ),
			),
			array(
				$object,
				true,
				$object,
			),
		);
	}

	/** @dataProvider for_replace */
	public function test_replace( $initial, $wanted, $expected ) {
		add_filter( self::HOOK, array( new Handler( $wanted ), 'replace' ) );

		$this->assertSame( $expected, apply_filters( self::HOOK, $initial ) );
	}

	public function for_insert(): array {
		return array(
			array(
				'initial',
				array( 's cruc', '4' ),
				'inits crucial',
			),
			array(
				'test',
				array( 'a ', null ),
				'a test',
			),
			array(
				'test',
				array( (object) array(), true ),
				't{}est',
			),
			array(
				array( 'initial', 'values' ),
				array( 'default', 1 ),
				array( 'initial', 'default', 'values' ),
			),
			array(
				true,
				null,
				true,
			),
		);
	}

	/** @dataProvider for_insert */
	public function test_insert( $initial, $wanted, $expected ) {
		add_filter( self::HOOK, array( new Handler( $wanted ), 'insert' ) );

		$this->assertSame( $expected, apply_filters( self::HOOK, $initial ) );
	}

	public function for_once(): array {
		$data = array();

		foreach ( Handler::ACTIONS as $action ) {
			$method = 'for_' . $action;
			$data[] = array_map(
				function( $value ) use ( $action ) {
					$value[1] = array(
						'value'    => array( $action, $value[1] ),
						'priority' => 10,
					);

					return $value;
				},
				$this->$method()
			);
		}

		return array_merge( ...$data );
	}

	/** @dataProvider for_once */
	public function test_once( $initial, $wanted, $expected ) {
		add_filter( self::HOOK, array( new Handler( $wanted ), 'once' ) );

		$this->assertSame( $expected, apply_filters( self::HOOK, $initial ) );

		if ( is_string( $initial ) || is_array( $initial ) ) {
			$this->assertNotSame( $expected, apply_filters( self::HOOK, $initial ) );
		}

		$this->assertSame( $initial, apply_filters( self::HOOK, $initial ) );
	}

	public function for_remove(): array {
		$data = array();

		foreach ( array( ...Handler::ACTIONS, 'once' ) as $action ) {
			$method = 'for_' . $action;
			$data[] = array_map(
				function( $value ) use ( $action ) {
					array_unshift( $value, $action );

					return $value;
				},
				$this->$method()
			);
		}

		return array_merge( ...$data );
	}

	/** @dataProvider for_remove */
	public function test_remove( $action, $initial, $wanted, $expected ) {
		add_filter( self::HOOK, array( new Handler( $wanted ), $action ) );
		( new Handler(
			array(
				'value'    => array( $action, $wanted ),
				'priority' => 10,
			)
		) )->remove( self::HOOK );

		if ( is_string( $initial ) || is_array( $initial ) ) {
			$this->assertNotSame( $expected, apply_filters( self::HOOK, $initial ) );
		}

		$this->assertSame( $initial, apply_filters( self::HOOK, $initial ) );
	}

	public function for_incomplete_once_remove(): array {
		return array(
			array( 'once' ),
			array( 'remove' ),
		);
	}

	/** @dataProvider for_incomplete_once_remove */
	public function test_incomplete_once_remove( string $action ): void {
		$initial = 'test';
		$wanted  = 'this';

		add_filter( self::HOOK, array( new Handler( $wanted ), $action ) );

		$this->assertNotSame( $wanted, apply_filters( self::HOOK, $initial ) );

		if ( 'once' === $action ) {
			$this->assertSame( $initial, apply_filters( self::HOOK, $initial ) );
		} else {
			$this->assertFalse( ( new Handler( $wanted ) )->remove( self::HOOK ) );
		}
	}
}
