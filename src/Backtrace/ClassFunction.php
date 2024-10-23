<?php

// SPDX-FileCopyrightText: 2024 Julien Lambé <julien@themosis.com>
//
// SPDX-License-Identifier: GPL-3.0-or-later

declare(strict_types=1);

namespace Themosis\Components\Error\Backtrace;

final class ClassFunction implements FrameClassFunction
{
    public function __construct(
        private string $className,
        private string $functionName,
        private string $type,
    ) {
    }

    public function getClass(): string
    {
        return $this->className;
    }

    public function __toString(): string
    {
        return sprintf('%s%s%s()', $this->className, $this->type, $this->functionName);
    }
}
