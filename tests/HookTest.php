<?php

/**
 * @package ThemePlate
 */

namespace Tests;

use Brain\Monkey;
use Error;
use PHPUnit\Framework\TestCase;
use ThemePlate\Hook;
use ThemePlate\Hook\Handler;
use function Brain\Monkey\Filters\expectAdded;

class HookTest extends TestCase {
	public const ARGS = array(
		'tag',
		'value',
		'extra',
	);

	protected function setUp(): void {
		parent::setUp();
		Monkey\setUp();
	}

	protected function tearDown(): void {
		Monkey\tearDown();
		parent::tearDown();
	}

	public function test_unknown_action(): void {
		$this->expectException( Error::class );
		$this->expectExceptionMessage( 'Call to undefined method ' . Hook::class . '::unknown()' );
		call_user_func( array( Hook::class, 'unknown' ) );
	}

	public function for_hook_correctly_added(): array {
		$actions = array( ...Handler::ACTIONS, 'once' ); // remove to rely on the handler test

		return array_combine(
			$actions,
			array_map(
				function ( $action ) {
					return array( $action );
				},
				$actions,
			)
		);
	}

	public function assert_in_args( $actual ): bool {
		$this->assertInstanceOf( Handler::class, $actual[0] );
		$this->assertSame( $this->dataName(), $actual[1] );

		return true;
	}

	/** @dataProvider for_hook_correctly_added */
	public function test_hook_correctly_added( string $action ): void {
		expectAdded( 'test_' . $action . '_tag' )->once()->whenHappen( array( $this, 'assert_in_args' ) );

		$args = array_combine(
			self::ARGS,
			array(
				'test_' . $action . '_tag',
				array( 'non_relevant_tester' ),
				'',
			)
		);

		if ( in_array( $action, array( 'insert', 'replace' ), true ) ) {
			$args['extra'] = 1;
		}

		$args = array_values( (array) array_filter( $args ) ); // Cleanup

		call_user_func_array( array( Hook::class, $action ), $args );
	}
}
