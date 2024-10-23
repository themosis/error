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
        private ReportHandler $reportHandler,
    ) {
    }

    public function __invoke(Throwable $exception)
    {
        $this->reportHandler
            ->capture(Issue::fromException($exception))
            ->publish();
    }
}
