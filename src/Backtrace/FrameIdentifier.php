<?php

declare(strict_types=1);

namespace Themosis\Components\Error\Backtrace;

interface FrameIdentifier {
	public function tag(): FrameTag;

	public function identify( Frame $frame ): bool;
}
