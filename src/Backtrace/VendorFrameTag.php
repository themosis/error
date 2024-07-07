<?php

declare(strict_types=1);

namespace Themosis\Components\Error\Backtrace;

final class VendorFrameTag extends AbstractFrameTag {
	public function slug(): string {
		return 'vendor';
	}

	public function name(): string {
		return 'Vendor';
	}
}
