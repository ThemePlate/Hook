<?php

/**
 * Handler for the hook methods
 *
 * @package ThemePlate
 * @since 0.1.0
 */

namespace ThemePlate\Hook;

class Helper {

	public static function stringify( $data ): string {

		if ( is_scalar( $data ) || null === $data ) {
			$data = (string) $data;
		} else {
			// phpcs:ignore WordPress.WP.AlternativeFunctions.json_encode_json_encode
			$data = json_encode( $data );
		}

		return $data;

	}

}
