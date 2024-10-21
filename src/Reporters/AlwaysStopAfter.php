<?php

// SPDX-FileCopyrightText: 2024 Julien Lambé <julien@themosis.com>
//
// SPDX-License-Identifier: GPL-3.0-or-later

declare(strict_types=1);

namespace Themosis\Components\Error\Reporters;

use Themosis\Components\Error\HaltReporter;
use Themosis\Components\Error\Issue;
use Themosis\Components\Error\Reporter;

final class AlwaysStopAfter implements HaltReporter
{
    public function __construct(
        private Reporter $reporter,
    ) {
    }

    public function stop(Issue $issue): bool
    {
        return true;
    }

    public function report(Issue $issue): void
    {
        $this->reporter->report($issue);
    }
}
