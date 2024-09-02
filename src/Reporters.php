<?php

declare(strict_types=1);

namespace Themosis\Components\Error;

interface Reporters {
	public function add( Reporter $reporter ): void;

	public function all(): array;
}
