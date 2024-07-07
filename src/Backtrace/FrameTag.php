<?php

declare(strict_types=1);

namespace Themosis\Components\Error\Backtrace;

use Themosis\Components\Error\Exceptions\InvalidFrameTagArgument;

final class FrameTag {
	public function __construct(
		private string $slug,
		private string $name,
	) {
		if ( $this->slug_has_invalid_format( $this->slug ) ) {
			throw new InvalidFrameTagArgument(
				message: sprintf( 'Frame tag slug "%s" is not valid. Use only lowercase letters and underscores.', $this->slug ),
			);
		}

		if ( empty( $this->name ) ) {
			throw new InvalidFrameTagArgument(
				message: sprintf( 'Frame tag name "%s" is not valid. Avoid empty tag names.', $this->name ),
			);
		}
	}

	private function slug_has_invalid_format( string $slug ): bool {
		if ( empty( $slug ) ) {
			return true;
		}

		$is_matching = preg_match(
			pattern: '/[^a-z_]+/',
			subject: $slug,
		);

		if ( 1 === $is_matching ) {
			return true;
		}

		return false;
	}

	public function name(): string {
		return $this->name;
	}

	public function slug(): string {
		return $this->slug;
	}
}
