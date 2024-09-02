<?php

declare(strict_types=1);

namespace Themosis\Components\Error;

use DateTimeImmutable;
use Throwable;

final class ReportHandler {
	/**
	 * @var array<int, Issue>
	 */
	private array $captured_issues = [];

	public function __construct(
		private Reporters $reporters,
	) {
	}

	public function report( Throwable $exception, ?DateTimeImmutable $occured_at = null ): void {
		$this->captured_issues[] = Issue::from_exception( $exception, $occured_at );
	}

	public function release(): void {
		foreach ( $this->captured_issues as $issue ) {
			foreach ( $this->reporters->all() as $reporter ) {
				$reporter->report( $issue );
			}
		}
	}
}
