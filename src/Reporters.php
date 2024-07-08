<?php

declare(strict_types=1);

namespace Themosis\Components\Error;

interface Reporters {
	public function add( ReporterKey $key, Reporter $reporter ): void;

	public function find( ReporterKey $key ): Reporter;
}
