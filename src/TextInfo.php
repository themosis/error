<?php

declare(strict_types=1);

namespace Themosis\Components\Error;

final class TextInfo implements Info {
	public function __construct(
		private string $name,
		private string $value,
	) {
	}

	public function name(): string {
		return $this->name;
	}

	public function value(): string {
		return $this->value;
	}
}
