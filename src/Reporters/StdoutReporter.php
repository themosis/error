<?php

// SPDX-FileCopyrightText: 2024 Julien Lambé <julien@themosis.com>
//
// SPDX-License-Identifier: GPL-3.0-or-later

declare(strict_types=1);

namespace Themosis\Components\Error\Reporters;

use Themosis\Components\Error\Backtrace\Backtrace;
use Themosis\Components\Error\Issue;
use Themosis\Components\Error\Reporter;

final class StdoutReporter implements Reporter
{
    public function __construct(
        private Backtrace $backtrace,
    ) {
    }

    public function report(Issue $issue): void
    {
        $backtrace = $this->backtrace->captureException($issue->exception());

        printf(
            "[%s] %s\n%s\n",
            $issue->date()->toString(),
            $issue->message(),
            (string) $backtrace
        );
    }
}
