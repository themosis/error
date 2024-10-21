<?php

// SPDX-FileCopyrightText: 2024 Julien Lambé <julien@themosis.com>
//
// SPDX-License-Identifier: GPL-3.0-or-later

declare(strict_types=1);

namespace Themosis\Components\Error;

use Throwable;

final class ExceptionHandler
{
    public function __construct(
        private ReportHandler $report_handler,
    ) {
    }

    public function __invoke(Throwable $exception)
    {
        $this->report_handler
            ->capture(Issue::from_exception($exception))
            ->publish();
    }
}
