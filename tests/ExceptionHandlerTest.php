<?php

// SPDX-FileCopyrightText: 2024 Julien Lambé <julien@themosis.com>
//
// SPDX-License-Identifier: GPL-3.0-or-later

declare(strict_types=1);

namespace Themosis\Components\Error\Tests;

use Exception;
use PHPUnit\Framework\Attributes\Test;
use Themosis\Components\Error\ExceptionHandler;
use Themosis\Components\Error\InMemoryIssues;
use Themosis\Components\Error\InMemoryReporters;
use Themosis\Components\Error\Issue;
use Themosis\Components\Error\Reporters\CallbackReporter;
use Themosis\Components\Error\Reporters\Conditions\AlwaysReport;
use Themosis\Components\Error\ReportHandler;

final class ExceptionHandlerTest extends TestCase
{
    #[Test]
    public function it_can_report_thrown_exception(): void
    {
        $stdout = '';

        $reporters = new InMemoryReporters();
        $reporters->add(
            condition: new AlwaysReport(),
            reporter: new CallbackReporter(
                static function (Issue $issue) use (&$stdout) {
                    $stdout = $issue->message();
                }
            ),
        );

        $reportHandler = new ReportHandler(
            reporters: $reporters,
            issues: new InMemoryIssues(),
        );

        $exceptionHandler = new ExceptionHandler($reportHandler);
        $exceptionHandler(new Exception('Oops'));

        $this->assertSame('Oops', $stdout);
    }
}
