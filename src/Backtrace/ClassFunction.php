<?php

declare(strict_types=1);

namespace Themosis\Components\Error\Backtrace;

final class ClassFunction implements FrameFunction {
	public function __construct(
		private string $class_name,
		private string $function_name,
		private string $type,
	) {
	}

	public function __toString(): string {
		return sprintf( '%s%s%s()', $this->class_name, $this->type, $this->function_name );
	}
}
