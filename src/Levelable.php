<?php

declare(strict_types=1);

namespace Themosis\Components\Error;

interface Levelable {
	public function level(): Level;
}
