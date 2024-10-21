<?php

// SPDX-FileCopyrightText: 2024 Julien Lambé <julien@themosis.com>
//
// SPDX-License-Identifier: GPL-3.0-or-later

declare(strict_types=1);

namespace Themosis\Components\Error\Reporters\Conditions;

use Closure;
use Themosis\Components\Error\Issue;
use Themosis\Components\Error\ReportCondition;

final class CallbackCondition implements ReportCondition
{
    public function __construct(
        private Closure $callback,
    ) {
    }

    public function can(Issue $issue): bool
    {
        return ( $this->callback )($issue);
    }
}
