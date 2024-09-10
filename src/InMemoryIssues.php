<?php

declare(strict_types=1);

namespace Themosis\Components\Error;

final class InMemoryIssues implements Issues {
	/**
	 * @var array<int, Issue>
	 */
	private array $issues = [];

	public function add( Issue $issue ): void {
		$this->issues[] = $issue;
	}

	/**
	 * @return array<int, Issue>
	 */
	public function all(): array {
		return $this->issues;
	}
}
