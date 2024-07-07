<?php

declare(strict_types=1);

namespace Themosis\Components\Error\Backtrace;

interface FrameTag {
	public function slug(): string;

	public function name(): string;

	public function equals( FrameTag $other ): bool;
}
