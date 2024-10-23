<?php

// SPDX-FileCopyrightText: 2024 Julien Lambé <julien@themosis.com>
//
// SPDX-License-Identifier: GPL-3.0-or-later

declare(strict_types=1);

namespace Themosis\Components\Error;

use Closure;

final class ReportHandler
{
    public function __construct(
        private Reporters $reporters,
        private Issues $issues,
    ) {
    }

    public function capture(Issue $issue): static
    {
        $this->issues->add($issue);

        return $this;
    }

    public function publish(): void
    {
        $shouldStop = static function (Reporter $reporter): Closure {
            return static function (Issue $issue) use ($reporter): bool {
                return $reporter instanceof HaltReporter && $reporter->stop($issue);
            };
        };

        array_reduce(
            $this->issues->all(),
            static function (callable $reportersFor, Issue $issue) use ($shouldStop) {
                foreach ($reportersFor($issue) as $reporter) {
                    /** @var Reporter $reporter */
                    $reporter->report($issue);

                    if ($shouldStop($reporter)($issue)) {
                        break;
                    }
                }

                return $reportersFor;
            },
            $this->reporters->getAllowedReporters(...)
        );
    }
}
