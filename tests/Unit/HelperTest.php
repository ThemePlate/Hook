<?php

/**
 * @package ThemePlate
 */

namespace Tests;

use PHPUnit\Framework\TestCase;
use ThemePlate\Hook\Helper;

class HelperTest extends TestCase {
	public function for_stringify(): array {
		return array(
			array(
				array(),
				'[]',
			),
			array(
				(object) array(),
				'{}',
			),
			array(
				true,
				'1',
			),
			array(
				false,
				'',
			),
			array(
				0,
				'0',
			),
			array(
				123,
				'123',
			),
			array(
				456.789,
				'456.789',
			),
			array(
				null,
				'',
			),
		);
	}

	/** @dataProvider for_stringify */
	public function test_stringify( $data, string $expected ): void {
		$this->assertSame( $expected, Helper::stringify( $data ) );
	}
}
