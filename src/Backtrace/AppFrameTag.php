<?php

declare(strict_types=1);

namespace Themosis\Components\Error\Backtrace;

final class AppFrameTag extends AbstractFrameTag {
	public function slug(): string {
		return 'app';
	}

	public function name(): string {
		return 'Application';
	}
}
