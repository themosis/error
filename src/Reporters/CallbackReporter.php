<?php

declare(strict_types=1);

namespace Themosis\Components\Error\Reporters;

use Closure;
use Themosis\Components\Error\Issue;
use Themosis\Components\Error\Reporter;

final class CallbackReporter implements Reporter {
	public function __construct(
		private Closure $callback,
	) {
	}

	public function report( Issue $issue ): void {
		( $this->callback )( $issue );
	}
}
