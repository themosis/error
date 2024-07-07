<?php

declare(strict_types=1);

namespace Themosis\Components\Error\Backtrace;

interface FrameIdentifiers {
	public function add( FrameIdentifier $identifier ): void;

	public function all(): array;
}
