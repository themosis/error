<?php

// SPDX-FileCopyrightText: 2024 Julien Lambé <julien@themosis.com>
//
// SPDX-License-Identifier: GPL-3.0-or-later

declare(strict_types=1);

namespace Themosis\Components\Error;

use DateTimeImmutable;
use Stringable;

final class IssueDate implements Stringable
{
    private string $format = DateTimeImmutable::W3C;

    public function __construct(
        private DateTimeImmutable $datetime,
    ) {
    }

    public function withFormat(string $format): static
    {
        $this->format = $format;

        return $this;
    }

    public function toString(): string
    {
        return $this->datetime->format($this->format);
    }

    public function __toString(): string
    {
        return $this->toString();
    }
}
