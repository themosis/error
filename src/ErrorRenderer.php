<?php

declare(strict_types=1);

namespace Themosis\Components\Error;

interface ErrorRenderer {
	public function render(): string;
}
