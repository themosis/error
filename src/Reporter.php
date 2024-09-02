<?php

declare(strict_types=1);

namespace Themosis\Components\Error;

interface Reporter {
	public function report( Issue $issue ): void;
}
