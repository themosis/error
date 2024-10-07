<?php

declare(strict_types=1);

namespace Themosis\Components\Error;

interface Info {
	public function name(): string;

	public function value(): string;
}
