<?php

declare(strict_types=1);

namespace Themosis\Components\Error;

interface Reporters {
	public function add( ReportCondition $condition, Reporter $reporter ): void;

	public function get_allowed_reporters( Issue $issue ): array;
}
