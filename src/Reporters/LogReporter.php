<?php

declare(strict_types=1);

namespace Themosis\Components\Error\Reporters;

use Psr\Log\LoggerInterface;
use Themosis\Components\Error\Issue;
use Themosis\Components\Error\Reporter;

final class LogReporter implements Reporter {
	public function __construct(
		private LoggerInterface $logger,
	) {
	}

	public function report( Issue $issue ): void {
		$this->logger->log(
			level: $issue->level()->value,
			message: $issue->message(),
			context: $issue->info()->as_array(),
		);
	}
}
