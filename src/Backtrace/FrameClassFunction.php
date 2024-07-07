<?php

declare(strict_types=1);

namespace Themosis\Components\Error\Backtrace;

interface FrameClassFunction extends FrameFunction {
	public function get_class(): string;
}
