<?php

declare(strict_types=1);

namespace Themosis\Components\Error;

interface ReportCondition {
	public function can( Issue $issue ): bool;
}
