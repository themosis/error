<?php

declare(strict_types=1);

namespace Themosis\Components\Error;

final class InMemoryReporters implements Reporters {
	/**
	 * @var array<int, Reporter>
	 */
	private array $reporters = [];

	public function add( Reporter $reporter ): void {
		$this->reporters[] = $reporter;
	}

	/**
	 * @return array<int, Reporter>
	 */
	public function all(): array {
		return $this->reporters;
	}
}
