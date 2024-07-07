<?php

declare(strict_types=1);

namespace Themosis\Components\Error\Backtrace;

use Themosis\Components\Error\Exceptions\DuplicateFrameIdentifier;

final class InMemoryFrameIdentifiers implements FrameIdentifiers {
	/**
	 * @var array<int, FrameIdentifier>
	 */
	private array $identifiers = [];

	public function add( FrameIdentifier $identifier ): void {
		if ( isset( $this->identifiers[ $identifier->tag()->slug() ] ) ) {
			throw new DuplicateFrameIdentifier(
				message: sprintf( 'Frame identifier already exists for tag "%s"', $identifier->tag()->slug() ),
			);
		}

		$this->identifiers[ $identifier->tag()->slug() ] = $identifier;
	}

	/**
	 * @return array<int, FrameIdentifier>
	 */
	public function all(): array {
		return $this->identifiers;
	}
}
