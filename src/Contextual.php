<?php

declare(strict_types=1);

namespace Themosis\Components\Error;

interface Contextual {
	public function context(): AdditionalInformation;
}
