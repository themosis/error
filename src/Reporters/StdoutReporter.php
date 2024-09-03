<?php

namespace Themosis\Components\Error\Reporters;

use Themosis\Components\Error\Backtrace\Backtrace;
use Themosis\Components\Error\Issue;
use Themosis\Components\Error\Reporter;

final class StdoutReporter implements Reporter {
	public function __construct(
		private Backtrace $backtrace,
	) {
	}

	public function report( Issue $issue ): void {
		$backtrace = $this->backtrace->capture_exception( $issue->exception() );

		printf(
			"[%s] %s\n%s\n",
			$issue->date(),
			$issue->message(),
			(string) $backtrace
		);
	}
}
