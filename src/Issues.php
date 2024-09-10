<?php

declare(strict_types=1);

namespace Themosis\Components\Error;

interface Issues {
	public function add( Issue $issue ): void;

	/**
	 * @return array<int, Issue>
	 */
	public function all(): array;
}
