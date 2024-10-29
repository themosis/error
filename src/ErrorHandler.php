<?php

// SPDX-FileCopyrightText: 2024 Julien Lambé <julien@themosis.com>
//
// SPDX-License-Identifier: GPL-3.0-or-later

declare(strict_types=1);

namespace Themosis\Components\Error;

use ErrorException;

final class ErrorHandler
{
    public function __construct(
        private ReportHandler $reportHandler,
    ) {
    }

    public function __invoke(int $errno, string $errstr, string $errfile, int $errline): bool
    {
        $error = new ErrorException($errstr, 0, $errno, $errfile, $errline);

        if ($this->isDeprecation($errno)) {
            $this->reportHandler
                ->capture(ExceptionalIssue::create($error))
                ->publish();

            return true;
        } elseif (error_reporting() && $errno) {
            throw $error;
        }

        return false;
    }

    private function isDeprecation(int $level): bool
    {
        return in_array($level, [ E_DEPRECATED, E_USER_DEPRECATED ], true);
    }
}
