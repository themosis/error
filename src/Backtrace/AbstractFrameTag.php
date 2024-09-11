<?php

// SPDX-FileCopyrightText: 2024 Julien Lambé <julien@themosis.com>
//
// SPDX-License-Identifier: GPL-3.0-or-later

declare(strict_types=1);

namespace Themosis\Components\Error\Backtrace;

abstract class AbstractFrameTag implements FrameTag {
	public function equals( FrameTag $other ): bool {
		return $this->slug() === $other->slug() && $this->name() === $other->name();
	}
}
