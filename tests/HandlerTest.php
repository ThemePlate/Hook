<?php

/**
 * @package ThemePlate
 */

namespace Tests;

use ThemePlate\Hook\Handler;
use WP_UnitTestCase;

class HandlerTest extends WP_UnitTestCase {
	public const HOOK = 'test_hook';

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
		);
	}

	/** @dataProvider for_pluck */
	public function test_pluck( $initial, $wanted, $expected ) {
		add_filter( self::HOOK, array( new Handler( $wanted ), 'pluck' ) );

		$this->assertSame( $expected, apply_filters( self::HOOK, $initial ) );
	}

	public function for_replace(): array {
		return array(
			array(
				'initial',
				array( 'initi', 'fin' ),
				'final',
			),
			array(
				array( 'initial', 'values' ),
				array( 'initial', 'final' ),
				array( 'final', 'values' ),
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
				array( 'initial', 'values' ),
				array( 'default', 1 ),
				array( 'initial', 'default', 'values' ),
			),
		);
	}

	/** @dataProvider for_insert */
	public function test_insert( $initial, $wanted, $expected ) {
		add_filter( self::HOOK, array( new Handler( $wanted ), 'insert' ) );

		$this->assertSame( $expected, apply_filters( self::HOOK, $initial ) );
	}

	public function for_once_and_remove(): array {
		return array(
			array(
				array( 'initial' ),
				array(
					'value'    => array( 'return', array( 'this' ) ),
					'priority' => 10,
				),
				array( 'this' ),
			),
			array(
				array( 'initial', 'values' ),
				array(
					'value'    => array( 'insert', array( 'default', 1 ) ),
					'priority' => 10,
				),
				array( 'initial', 'default', 'values' ),
			),
		);
	}

	/** @dataProvider for_once_and_remove */
	public function test_once( $initial, $wanted, $expected ) {
		add_filter( self::HOOK, array( new Handler( $wanted ), 'once' ) );

		$this->assertSame( $expected, apply_filters( self::HOOK, $initial ) );
		$this->assertNotSame( $expected, apply_filters( self::HOOK, $initial ) );
		$this->assertSame( $initial, apply_filters( self::HOOK, $initial ) );
	}

	/** @dataProvider for_once_and_remove */
	public function test_remove_with_once( $initial, $wanted, $expected ) {
		add_filter( self::HOOK, array( new Handler( $wanted ), 'once' ) );
		( new Handler( $wanted ) )->remove( self::HOOK );

		$this->assertNotSame( $expected, apply_filters( self::HOOK, $initial ) );
		$this->assertSame( $initial, apply_filters( self::HOOK, $initial ) );
	}

	/** @dataProvider for_once_and_remove */
	public function test_remove_with_others( $initial, $wanted, $expected ) {
		add_filter( self::HOOK, array( new Handler( $wanted['value'][1] ), $wanted['value'][0] ) );
		( new Handler( $wanted ) )->remove( self::HOOK );

		$this->assertNotSame( $expected, apply_filters( self::HOOK, $initial ) );
		$this->assertSame( $initial, apply_filters( self::HOOK, $initial ) );
	}
}
