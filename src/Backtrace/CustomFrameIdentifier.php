<?php

// SPDX-FileCopyrightText: 2024 Julien Lambé <julien@themosis.com>
//
// SPDX-License-Identifier: GPL-3.0-or-later

declare(strict_types=1);

namespace Themosis\Components\Error\Backtrace;

use Closure;

final class CustomFrameIdentifier implements FrameIdentifier {
	public function __construct(
		private FrameTag $tag,
		private Closure $identifier,
	) {
	}

	public function tag(): FrameTag {
		return $this->tag;
	}

	public function identify( Frame $frame ): bool {
		$callback = $this->identifier;

		return $callback( $frame );
	}
}
