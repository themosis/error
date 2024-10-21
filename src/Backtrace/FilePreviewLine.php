<?php

// SPDX-FileCopyrightText: 2024 Julien Lambé <julien@themosis.com>
//
// SPDX-License-Identifier: GPL-3.0-or-later

declare(strict_types=1);

namespace Themosis\Components\Error\Backtrace;

final class FilePreviewLine
{
    public function __construct(
        private string $content,
        private int $number,
    ) {
    }

    public function content(): string
    {
        return htmlentities($this->content);
    }

    public function number(): int
    {
        return $this->number;
    }
}
