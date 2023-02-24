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
use ThemePlate\Hook\Helper;
use function Brain\Monkey\Filters\expectAdded;
use function Brain\Monkey\Functions\expect;

class HookTest extends TestCase {
	public const ARGS = array(
		'tag',
		'value',
		'extra',
	);

	protected static string $filter_output = 'test';

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

	public function for_readme_sample(): array {
		return array(
			array(
				'append',
				'ing',
			),
			array(
				'prepend',
				're-',
			),
			array(
				'return',
				'this!',
			),
			array(
				'pluck',
				'i',
			),
			array(
				'replace',
				array( '!', '...' ),
			),
			array(
				'insert',
				array( 'u', 2 ),
			),
			array(
				'once',
				array( 'pluck', 'th' ),
			),
		);
	}

	/** @dataProvider for_readme_sample */
	public function test_readme_sample( string $action, $value ): void {
		$second_filter_output = self::$filter_output;

		if ( 'once' === $action ) {
			expect( 'current_filter' )->withNoArgs()->andReturn( '' );

			$priority = 10;
			$value    = compact( 'value', 'priority' );
		}

		self::$filter_output = ( new Handler( $value ) )->$action( self::$filter_output );

		if ( 'once' === $action ) {
			$value = array_values( $value['value'] );
		} else {
			$second_filter_output = self::$filter_output;
		}

		$output = array(
			$action,
			Helper::stringify( $value ),
			self::$filter_output,
			$second_filter_output,
		);

		$mask = "| %7s | %-14s | %10s | %10s |\n";

		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		printf( $mask, ...$output );
		$this->assertIsString( self::$filter_output );
	}
}
