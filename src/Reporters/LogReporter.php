<?php

// SPDX-FileCopyrightText: 2024 Julien Lambé <julien@themosis.com>
//
// SPDX-License-Identifier: GPL-3.0-or-later

declare(strict_types=1);

namespace Themosis\Components\Error\Reporters;

use Psr\Log\LoggerInterface;
use Themosis\Components\Error\Info;
use Themosis\Components\Error\Issue;
use Themosis\Components\Error\Reporter;

final class LogReporter implements Reporter
{
    public function __construct(
        private LoggerInterface $logger,
    ) {
    }

    public function report(Issue $issue): void
    {
        $this->logger->log(
            level: $issue->level()->value,
            message: $issue->message(),
            context: array_reduce(
                $issue->info()?->getInformation() ?? [],
                static function (array $carry, Info $info) {
                    $carry[ $info->name() ] = $info->value();
                },
                []
            ),
        );
    }
}
