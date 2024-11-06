<?php

// SPDX-FileCopyrightText: 2024 Julien Lambé <julien@themosis.com>
//
// SPDX-License-Identifier: GPL-3.0-or-later

declare(strict_types=1);

namespace Themosis\Components\Error\Backtrace;

use Stringable;

final class File implements Stringable
{
    public function __construct(
        private string $filepath,
        private int $line,
    ) {
    }

    public function path(): string
    {
        return $this->filepath;
    }

    public function line(): int
    {
        return $this->line;
    }

    public function __toString(): string
    {
        return sprintf('%s:%d', $this->filepath, $this->line);
    }
}
