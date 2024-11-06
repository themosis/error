<?php

// SPDX-FileCopyrightText: 2024 Julien Lambé <julien@themosis.com>
//
// SPDX-License-Identifier: GPL-3.0-or-later

declare(strict_types=1);

namespace Themosis\Components\Error\Backtrace;

final class PhpFunction implements FrameFunction
{
    public function __construct(
        private string $functionName,
    ) {
    }

    public function getName(): string
    {
        return $this->functionName;
    }

    public function __toString(): string
    {
        return sprintf('%s()', $this->functionName);
    }
}
