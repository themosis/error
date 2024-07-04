<?php

declare(strict_types=1);

namespace Themosis\Components\Error\Backtrace;

final class PhpFunction implements FrameFunction {
	public function __construct(
		private string $function_name,
	) {
	}

	public function __toString(): string {
		return sprintf( '%s()', $this->function_name );
	}
}
